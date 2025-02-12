<?php
$barcode = $_POST['barcode'] ?? '';
if ($barcode) {
    $jsonFile = 'scans.json';
    // Load the existing data from the .json file
    if (file_exists($jsonFile)) {
        $data = json_decode(file_get_contents($jsonFile), true);
    } else {
        $data = [];
    }

    // Get the current timestamp
    $timestamp = time();
    
    // Add the new barcode and timestamp
    $data[$timestamp] = $barcode;

    // If the length of the JSON data exceeds 20, clear it
    if (count($data) > 20) {
        $data = [];
    }

    // Save the updated data back to the JSON file
    file_put_contents($jsonFile, json_encode($data));

    echo "Barcode saved successfully!";
} else {
    echo "No barcode provided.";
}
?>
