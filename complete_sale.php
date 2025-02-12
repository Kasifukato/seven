<?php
// complete_sale.php
require_once('includes/load.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['sale_data']) && !empty($_POST['sale_data'])) {
        $items = json_decode($_POST['sale_data'], true);

        foreach ($items as $item) {
            $name = $db->escape($item['name']);
            $qty = (int)$item['qty'];
            $price = (float)$item['sale_price'];

            // Fetch product by name
            $product = find_by_name('products', $name);
            
            if ($product) {
                $product_id = $product['id'];
                $current_qty = (int)$product['quantity'];
                $new_qty = $current_qty - $qty;

                // Ensure the new quantity is not negative
                if ($new_qty >= 0) {
                    // Update product quantity in the inventory
                    $update_inventory = $db->query("UPDATE products SET quantity = '{$new_qty}' WHERE id = '{$product_id}'");

                    if ($update_inventory) {
                        // Record the sale
                        $date = date('Y-m-d H:i:s');
                        $total = $qty * $price;
                        $insert_sale = $db->query("INSERT INTO sales (product_id, qty, sale_price, total, date) VALUES ('{$product_id}', '{$qty}', '{$price}', '{$total}', '{$date}')");

                        if (!$insert_sale) {
                            echo 'Error recording the sale.';
                            exit;
                        }
                    } else {
                        echo 'Error updating inventory.';
                        exit;
                    }
                } else {
                    echo 'Not enough stock for product: ' . $name;
                    exit;
                }
            } else {
                echo 'Product not found: ' . $name;
                exit;
            }
        }

        echo 'Sale Completed and Inventory Updated';
    } else {
        echo 'No sale data received.';
    }
}
?>
