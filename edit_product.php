<?php
$page_title = 'Edit Product';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(2);

$product = find_by_id('products', (int)$_GET['id']);
$all_categories = find_all('categories');
$all_photo = find_all('media');

if (!$product) {
  $session->msg("d", "Missing product ID.");
  redirect('product.php');
}

// Handle Discard button click
if (isset($_POST['discard'])) {
    // Redirect to admin.php
  redirect('admin.php', false);
  exit;
}

// Handle Save button click
if (isset($_POST['product'])) {
  $req_fields = array('product-title', 'product-categorie', 'product-quantity', 'buying-price', 'saleing-price');
  validate_fields($req_fields);

  if (empty($errors)) {
    $p_name = remove_junk($db->escape($_POST['product-title']));
    $p_barcode = remove_junk($db->escape($_POST['product-barcode']));
    $p_cat = (int)$_POST['product-categorie'];
    $p_qty = remove_junk($db->escape($_POST['product-quantity']));
    $p_buy = remove_junk($db->escape($_POST['buying-price']));
    $p_sale = remove_junk($db->escape($_POST['saleing-price']));

    if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
      $media_id = '0';
    } else {
      $media_id = remove_junk($db->escape($_POST['product-photo']));
    }

    $query = "UPDATE products SET";
    $query .= " barcode ='{$p_barcode}',";
    $query .= " name ='{$p_name}', quantity ='{$p_qty}',";
    $query .= " buy_price ='{$p_buy}', sale_price ='{$p_sale}', categorie_id ='{$p_cat}', media_id='{$media_id}'";
    $query .= " WHERE id ='{$product['id']}'";

    $result = $db->query($query);
    if ($result && $db->affected_rows() === 1) {
      $session->msg('s', "Product updated");
      redirect('product.php', false);
    } else {
      $session->msg('d', 'Sorry, failed to update!');
      redirect('edit_product.php?id=' . $product['id'], false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('edit_product.php?id=' . $product['id'], false);
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
  <h1 class="workboard__title">Edit Products</h1>
</div>
<div class="workpanel product__main">
  <div class="overall-info">
    <div class="row">
      <div class="col xs-12">
        <form class="general--form access__form info"method="post" action="edit_product.php?id=<?php echo (int)$product['id'] ?>">
          <div class="info">
            <div class="row">
              <div class="col xs-12 sx-6">
                <span>Product</span>
              </div>
              <div class="col xs-12 sx-6">
                <div class="site-panel">
                  <div class="form__action">
                    <input
                    type="submit"
                    name="discard"
                    class="button tertiary-line"
                    value="Discard" />
                  </div>
                  <div class="form__action">
                    <input
                    type="submit"
                    class="button primary-tint"
                    name="product"
                    value="Save" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col xs-12 sm-3">
             <div   class = "form__module">
              <label for   = "brcode" class = "form__label">Barcode</label>
              <div   class = "form__set">
                <input type  = "text" id= "brcode"  name="product-barcode" placeholder = "12345" value="<?php echo remove_junk($product['barcode']);?>" >
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
                value="<?php echo remove_junk($product['name']);?>"
                placeholder="Product Title" />
              </div>
            </div>
          </div>
          <div class="col xs-12 sm-3">
            <div class="form__module">
              <label for="prodcat" class="form__label">Category</label>
              <select class="form-control" name="product-categorie">
                <option value="">Select Product Category</option>
                <?php  foreach ($all_categories as $cat): ?>
                 <option value="<?php echo (int)$cat['id']; ?>" <?php if($product['categorie_id'] === $cat['id']): echo "selected"; endif; ?> >
                   <?php echo remove_junk($cat['name']); ?></option>
                 <?php endforeach; ?>
               </select>
             </div>
           </div>
           <div class="col xs-12 sm-3">
            <div class="form__module">
              <label for="prodthrhold" class="form__label">Product Photo</label>
              <select class="form-control" name="product-photo">
                <option value="">Select Product Photo</option>
                <?php  foreach ($all_photo as $photo): ?>
                  <option value="<?php echo (int)$photo['id'];?>" <?php if($product['media_id'] === $photo['id']): echo "selected"; endif; ?> >
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
                  value="<?php echo remove_junk($product['buy_price']);?>"
                  id="prodbuying" />
                </div>
              </div>
            </div>
            <div class="col xs-12 sm-3">
              <div class="form__module">
                <label for="prodbuying" class="form__label">Selling Price</label>
                <div class="form__set">
                  <input
                  type="tel"
                  id="prodbuying"
                  name="saleing-price"
                  value="<?php echo remove_junk($product['sale_price']);?>"
                  placeholder="Selling Price" />
                </div>
              </div>
            </div>
            <div class="col xs-12 sm-3">
              <div class="form__module">
                <label for="prodqty" class="form__label">Quantity</label>
                <div class="form__set">
                  <input
                  type="number"
                  id="prodqty"
                  name="product-quantity"
                  value="<?php echo remove_junk($product['quantity']); ?>"
                  placeholder="Product Quantity" />
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