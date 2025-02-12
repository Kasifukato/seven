<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once('includes/load.php');

if (!$session->isUserLoggedIn()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Supplier') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$user_name = $_SESSION['user_name'] ?? '';
if (empty($user_name)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'User not found']);
    exit;
}

// Escape the name to prevent SQL injection
$user_name_escaped = $db->escape($user_name);

// Get supplier ID
$supplier_query = "SELECT id FROM suppliers WHERE LOWER(name) = LOWER('{$user_name_escaped}') LIMIT 1";
$supplier_result = $db->query($supplier_query);

if (!$supplier_result || !($supplier = $supplier_result->fetch_assoc())) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Supplier not found']);
    exit;
}

$supplier_id = (int)$supplier['id'];

// Fetch recent orders
$recent_orders_query = "
    SELECT 
        o.id,
        p.name as product_name,
        o.required_quantity,
        o.sent_quantity,
        o.status,
        o.order_date
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.supplier_id = {$supplier_id}
    ORDER BY o.order_date DESC
    LIMIT 10";

$recent_orders_result = $db->query($recent_orders_query);
$orders = [];

if ($recent_orders_result) {
    while ($order = $recent_orders_result->fetch_assoc()) {
        $orders[] = [
            'id' => (int)$order['id'],
            'product_name' => remove_junk(first_character($order['product_name'])),
            'required_quantity' => (int)$order['required_quantity'],
            'sent_quantity' => (int)$order['sent_quantity'],
            'status' => htmlspecialchars($order['status']),
            'order_date' => $order['order_date']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($orders);