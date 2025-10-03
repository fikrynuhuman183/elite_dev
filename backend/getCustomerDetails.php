<?php
include 'conn.php';

header('Content-Type: application/json');

if (isset($_GET['customer_id'])) {
    $customer_id = $_GET['customer_id'];

    try {
        $query = "SELECT customer_id, name, email, phone, vat_number FROM customers WHERE customer_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();

        if ($customer) {
            echo json_encode([
                'status' => 'success',
                'data' => $customer
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Customer not found'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Customer ID not provided'
    ]);
}
?>
