<?php
require_once('includes/load.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['pending' => 0, 'supplied' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get Supplier ID from the `suppliers` table
$supplier_query = "SELECT id FROM suppliers WHERE user_id = '{$user_id}' LIMIT 1";
$supplier_result = $db->query($supplier_query);

if ($supplier_result && $supplier = $supplier_result->fetch_assoc()) {
    $supplier_id = $supplier['id'];

    // Count Pending Orders
    $pending_query = "SELECT COUNT(*) AS pending FROM orders WHERE supplier_id = {$supplier_id} AND status = 'Pending'";
    $pending_result = $db->query($pending_query);
    $pending = ($pending_result && $row = $pending_result->fetch_assoc()) ? (int)$row['pending'] : 0;

    // Sum of Supplied Quantity
    $supplied_query = "SELECT SUM(sent_quantity) AS supplied FROM orders WHERE supplier_id = {$supplier_id} AND status = 'Sent'";
    $supplied_result = $db->query($supplied_query);
    $supplied = ($supplied_result && $row = $supplied_result->fetch_assoc() && $row['supplied'] !== null) ? (int)$row['supplied'] : 0;

    echo json_encode(['pending' => $pending, 'supplied' => $supplied]);
} else {
    echo json_encode(['pending' => 0, 'supplied' => 0]);
}
?>
