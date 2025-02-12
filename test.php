<?php
$page_title = 'Add Product';
require_once('includes/load.php');  
// Checkin What level user has permission to view this page
page_require_level(2);
$all_categories = find_all('categories');
$all_photo = find_all('media');

// Handle Discard button click
if (isset($_POST['discard'])) {
    // Redirect to product.php
    redirect('product.php', false);
    exit;
}

if (isset($_POST['add_product'])) {
    // Required fields for validation
    $req_fields = array('product-title', 'product-categorie', 'product-quantity', 'buying-price', 'saleing-price');
    validate_fields($req_fields);

    if (empty($errors)) {
        // Sanitize form inputs
        $p_name  = remove_junk($db->escape($_POST['product-title']));
        $p_cat   = remove_junk($db->escape($_POST['product-categorie']));
        $p_qty   = remove_junk($db->escape($_POST['product-quantity']));
        $p_buy   = remove_junk($db->escape($_POST['buying-price']));
        $p_sale  = remove_junk($db->escape($_POST['saleing-price']));
        $p_barcode = remove_junk($db->escape($_POST['product-barcode'])); // Handle Barcode
        $media_id = isset($_POST['product-photo']) && !empty($_POST['product-photo']) ? remove_junk($db->escape($_POST['product-photo'])) : 0;
        $date    = make_date();

        // SQL Query to insert or update product
        $query = "INSERT INTO products (name, barcode, quantity, buy_price, sale_price, categorie_id, media_id, date) 
                  VALUES ('{$p_name}', '{$p_barcode}', '{$p_qty}', '{$p_buy}', '{$p_sale}', '{$p_cat}', '{$media_id}', '{$date}')
                  ON DUPLICATE KEY UPDATE 
                      name='{$p_name}', 
                      quantity = quantity + {$p_qty},  -- Update quantity by adding the new value
                      buy_price='{$p_buy}',
                      sale_price='{$p_sale}',
                      categorie_id='{$p_cat}',
                      media_id='{$media_id}', 
                      date='{$date}'";

        // Execute the query
        if ($db->query($query)) {
            $session->msg('s', "Product added/updated successfully.");
            redirect('add_product.php', false);
        } else {
            $session->msg('d', 'Sorry, failed to add the product.');
            redirect('product.php', false);
        }
    } else {
        // Display validation errors
        $session->msg("d", $errors);
        redirect('add_product.php', false);
    }
}
?>
<?php include_once('layouts/header.php'); ?>

    <script>
        function fetchDetails() {
            fetch("fetch_details.php")
                .then(response => response.json())
                .then(product => {
                    if (product) {
                        document.getElementById("productBarcode").textContent = product.barcode;
                        document.getElementById("productName").textContent = product.name;
                        document.getElementById("productStatus").textContent = product.status;
                        document.getElementById("productQuantity").textContent = product.quantity;
                    } else {
                        document.getElementById("productBarcode").textContent = "N/A";
                        document.getElementById("productName").textContent = "N/A";
                        document.getElementById("productStatus").textContent = "N/A";
                        document.getElementById("productQuantity").textContent = "N/A";
                    }
                })
                .catch(error => console.error("Error:", error));
        }

        setInterval(fetchDetails, 2000); // Fetch details every 2 seconds
    </script>
</head>
<body>
    <h1>Scanned Product Details</h1>
    <p><strong>Product Name:</strong> <span id="productBarcode">Waiting...</span></p>
    <p><strong>Product Name:</strong> <span id="productName">Waiting...</span></p>
    <p><strong>Price:</strong> <span id="productStatus">Waiting...</span></p>
    <p><strong>Stock:</strong> <span id="productQuantity">Waiting...</span></p>
</body>
</html>
