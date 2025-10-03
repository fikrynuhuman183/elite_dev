<?php
include 'conn.php';

// Decode JSON data from request
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

// Validate input
if (!isset($data['shipment_id']) || !isset($data['status'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    http_response_code(400);
    exit;
}

$shipment_id = $data['shipment_id']; // Keep original type
$status = $data['status'];

// Validate status value
$valid_statuses = ['pending', 'submitted', 'paid', 'generated'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid status value']);
    http_response_code(400);
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // Verify shipment exists first
    $check_sql = "SELECT COUNT(*) as count FROM shipments WHERE shipment_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $shipment_id); // Use "s" for string type
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        throw new Exception("Shipment not found");
    }

    // Update ONLY the specific shipment
    $update_sql = "UPDATE shipments SET status = ? WHERE shipment_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ss", $status, $shipment_id); // Both as strings
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        throw new Exception("No rows updated - possible ID mismatch");
    }

    // If status is being changed to 'paid', handle payment records
    if ($status === 'paid') {
        // Get shipment details including customer and total amount
        $shipment_sql = "SELECT s.customer_id, SUM(sc.total_amount) AS total_amount
                FROM shipments s
                JOIN shipment_charges sc ON s.shipment_id = sc.shipment_id
                WHERE s.shipment_id = ?
                GROUP BY s.customer_id";
        $stmt = $conn->prepare($shipment_sql);
        $stmt->bind_param("s", $shipment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $shipment = $result->fetch_assoc();
            $customer_id = $shipment['customer_id'];
            $total_amount = $shipment['total_amount'];
            
            // Check existing payments for this shipment
            $payment_check = "SELECT partial_payment, partial_payment_validity 
                            FROM payments 
                            WHERE shipment_id = ?
                            ORDER BY payment_date DESC
                            LIMIT 1";
            $stmt = $conn->prepare($payment_check);
            $stmt->bind_param("s", $shipment_id);
            $stmt->execute();
            $check_result = $stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $existing_payment = $check_result->fetch_assoc();
                
                // Case 1: Existing full payment (partial_payment = 0)
                if ($existing_payment['partial_payment'] == 0) {
                    // Do nothing - full payment already exists
                    $conn->commit();
                    echo json_encode(['status' => 'success', 'message' => 'Status updated. Full payment already exists.']);
                    $conn->close();
                    exit;
                }
                // Case 2: Existing partial payment that's still valid
                elseif ($existing_payment['partial_payment_validity'] == 1) {
                    // Invalidate all previous partial payments for this shipment
                    $invalidate_sql = "UPDATE payments 
                                    SET partial_payment_validity = 0 
                                    WHERE shipment_id = ? AND partial_payment_validity = 1";
                    $stmt = $conn->prepare($invalidate_sql);
                    $stmt->bind_param("s", $shipment_id);
                    $stmt->execute();
                    
                    // Insert new full payment record
                    $payment_sql = "INSERT INTO payments 
                                (customer_id, shipment_id, payment_amount, partial_payment, partial_payment_validity, payment_date, note) 
                                VALUES (?, ?, ?, 0, 0, CURRENT_DATE(), 'Full payment for shipment #$shipment_id')";
                    $stmt = $conn->prepare($payment_sql);
                    $stmt->bind_param("isd", $customer_id, $shipment_id, $total_amount);
                    $stmt->execute();
                }
            }
            // Case 3: No existing payment records
            else {
                // Insert new full payment record
                $payment_sql = "INSERT INTO payments 
                            (customer_id, shipment_id, payment_amount, partial_payment, partial_payment_validity, payment_date, note) 
                            VALUES (?, ?, ?, 0, 0, CURRENT_DATE(), 'Initial Full payment for shipment #$shipment_id')";
                $stmt = $conn->prepare($payment_sql);
                $stmt->bind_param("isd", $customer_id, $shipment_id, $total_amount);
                $stmt->execute();
            }
        }
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Status updated successfully']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    http_response_code(500);
}

$conn->close();
?>