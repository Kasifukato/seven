<?php
// Include the load.php or directly connect here
// Make sure you include the correct path to your load.php file or manually define the connection
require_once('includes/load.php'); // Or directly define the connection here

// Ensure $con is defined, either through load.php or directly in this file
if (!isset($con)) {
    $con = mysqli_connect("localhost", "root", "", "inventory_system");
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }
}

if (isset($_GET['query'])) {
    $query = mysqli_real_escape_string($con, $_GET['query']); // Sanitize the input
    
    // Query the database for products that match the search
    $sql = "SELECT name FROM products WHERE name LIKE '%$query%'";
    $result = mysqli_query($con, $sql);
    
    // Check if query execution was successful
    if (!$result) {
        echo json_encode(['error' => 'Database error: ' . mysqli_error($con)]);
        exit;
    }

    // Fetch results and prepare response
    $suggestions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions[] = $row['name'];
    }

    // Return the suggestions as a JSON response
    echo json_encode($suggestions);
} else {
    echo json_encode(['error' => 'No query provided']);
}
?>
