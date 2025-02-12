<?php
// Start the session
session_start();

// Check if a product is scanned
if (isset($_SESSION['scanned_product'])) {
    echo json_encode([
        "status" => "success",
        "product" => $_SESSION['scanned_product']
    ]);

    // Clear the session after sending the product
    unset($_SESSION['scanned_product']);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No product scanned"
    ]);
}
?>
