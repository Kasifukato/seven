<?php
// Establish connection to the database
$con = mysqli_connect("localhost", "root", "", "inventory_system");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL query to get the total count of products
$total_products_query = "SELECT COUNT(*) AS total_products FROM products";
$total_products_result = mysqli_query($con, $total_products_query);

// Fetch the result
$total_products_row = mysqli_fetch_assoc($total_products_result);

// Return the count of products as a response
echo $total_products_row['total_products'];

// Close the database connection
mysqli_close($con);
?>
