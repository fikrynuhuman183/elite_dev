<?php
include "conn.php"; // adjust path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // First, get the shipment_id from the credit note record
    $stmt = $conn->prepare("SELECT shipment_id FROM credit_notes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $shipment_id = $row['shipment_id'];

        // Delete related shipment charges
        $deleteCharges = $conn->prepare("DELETE FROM shipment_charges WHERE shipment_id = ? AND credit_note_id = ?");
        $deleteCharges->bind_param("ii", $shipment_id, $id);
        $deleteCharges->execute();

        // Delete credit note
        $deleteCreditNote = $conn->prepare("DELETE FROM credit_notes WHERE id = ?");
        $deleteCreditNote->bind_param("i", $id);
        $deleteCreditNote->execute();

        echo "Credit note and associated charges deleted successfully.";
    } else {
        echo "Credit note not found.";
    }
} else {
    echo "Invalid request.";
}
?>
