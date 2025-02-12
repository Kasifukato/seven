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
        $p_barcode = remove_junk($db->escape($_POST['product-barcode']));
        $media_id = isset($_POST['product-photo']) && !empty($_POST['product-photo']) ? remove_junk($db->escape($_POST['product-photo'])) : 0;
        $date    = make_date();

        // Validate quantity is not negative
        if ($p_qty <= 0) {
            $session->msg('d', 'Quantity must be greater than zero.');
            redirect('add_product.php', false);
            exit;
        }

        // Validate barcode format (8-12 digits only)
        if (!empty($p_barcode)) {  // Only validate if barcode is provided
            if (!preg_match('/^\d{8,12}$/', $p_barcode)) {
                $session->msg('d', 'Barcode must be between 8 and 12 digits and contain only numbers.');
                redirect('add_product.php', false);
                exit;
            }
        }

        // Check if buying price is less than selling price
        if (floatval($p_buy) >= floatval($p_sale)) {
            $session->msg('d', 'Buying price must be less than selling price.');
            redirect('add_product.php', false);
            exit;
        }

        // For products with existing barcode, check if update would result in negative quantity
        if (!empty($p_barcode)) {
            $current_product = find_by_sql("SELECT quantity FROM products WHERE barcode = '{$p_barcode}' LIMIT 1");
            if (!empty($current_product)) {
                $current_qty = $current_product[0]['quantity'];
                if (($current_qty + $p_qty) < 0) {
                    $session->msg('d', 'Cannot reduce quantity below zero. Current stock: ' . $current_qty);
                    redirect('add_product.php', false);
                    exit;
                }
            }
        }

        // SQL Query to insert or update product
        $query = "INSERT INTO products (name, barcode, quantity, buy_price, sale_price, categorie_id, media_id, date) 
                  VALUES ('{$p_name}', '{$p_barcode}', '{$p_qty}', '{$p_buy}', '{$p_sale}', '{$p_cat}', '{$media_id}', '{$date}')
                  ON DUPLICATE KEY UPDATE 
                      name='{$p_name}', 
                      quantity = GREATEST(0, quantity + {$p_qty}),  -- Ensure quantity never goes below 0
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
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<div class="workboard__heading">
    <h1 class="workboard__title">Products</h1>
</div>

<div class="workpanel inventorypg">
    <div class="overall-info">
        <div class="row">
            <div class="col xs-12">
                <form class="general--form access__form info" method="post" action="add_product.php" class="clearfix">
                    <div class="info">
                        <div class="row">
                            <div class="col xs-12 sx-6">
                                <span>New Product</span>
                            </div>
                            <div class="col xs-12 sx-6">
                                <div class="site-panel">
                                    <div class="form__action">
                                        <input
                                        type="submit"
                                        class="button tertiary-line"
                                        value="Discard" name="discard" />
                                    </div>
                                    <div class="form__action">
                                        <input
                                        type="submit"
                                        class="button primary-tint"
                                        name="add_product"
                                        value="Save" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="brcode" class="form__label">Barcode (8-12 digits)</label>
                                <div class="form__set">
                                    <input type="text" 
                                           id="brcode" 
                                           name="product-barcode" 
                                           placeholder="12345678" 
                                           pattern="\d{8,12}" 
                                           title="Barcode must be between 8 and 12 digits"/>
                                </div>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="prodname" class="form__label">Product Name</label>
                                <div class="form__set">
                                    <input
                                    type="text"
                                    id="prodname"
                                    name="product-title"
                                    placeholder="Product Title" />
                                </div>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="prodcat" class="form__label">Category</label>
                                <select class="form-control" name="product-categorie">
                                    <option value="">Select Product Category</option>
                                    <?php foreach ($all_categories as $cat): ?>
                                        <option value="<?php echo (int)$cat['id'] ?>">
                                            <?php echo $cat['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="prodphoto" class="form__label">Product Photo</label>
                                <select class="form-control" name="product-photo">
                                    <option value="">Select Product Photo</option>
                                    <?php foreach ($all_photo as $photo): ?>
                                        <option value="<?php echo (int)$photo['id'] ?>">
                                            <?php echo $photo['file_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="prodbuying" class="form__label">Buying Price</label>
                                <div class="form__set">
                                    <input
                                    type="tel"
                                    name="buying-price"
                                    placeholder="Buying Price"
                                    id="prodbuying" />
                                </div>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="prodselling" class="form__label">Selling Price</label>
                                <div class="form__set">
                                    <input
                                    type="tel"
                                    name="saleing-price"
                                    placeholder="Selling Price"
                                    id="prodselling" />
                                </div>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="prodqty" class="form__label">Quantity</label>
                                <div class="form__set">
                                    <input
                                    type="number"
                                    name="product-quantity"
                                    placeholder="Product Quantity"
                                    id="prodqty"
                                    min="1" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>