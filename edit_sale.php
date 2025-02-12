<?php
$page_title = 'Edit Sale';
require_once('includes/load.php');
// Check user permission level
page_require_level(3);

// Get sale information
$sale = find_by_id('sales', (int)$_GET['id']);
if (!$sale) {
    $session->msg("d", "Missing sale ID.");
    redirect('sales.php');
}

// Get product information
$product = find_by_id('products', $sale['product_id']);
if (!$product) {
    $session->msg("d", "Product not found for this sale.");
    redirect('sales.php');
}

// Handle Discard button
if (isset($_POST['update_sale'])) {
  $req_fields = array('title', 'quantity', 'price', 'total', 'date');
  validate_fields($req_fields);

  if (empty($errors)) {
      $p_id = $db->escape((int)$product['id']);
      $s_qty = $db->escape((int)$_POST['quantity']);
      $s_total = $db->escape($_POST['total']);
      $date = $db->escape($_POST['date']);
      $s_date = date("Y-m-d", strtotime($date));

      // Save the old quantity before updating
      $old_qty = (int)$sale['qty'];

      // Calculate the difference in quantity
      $qty_difference = $s_qty - $old_qty;

      // First, update the sales record
      $sql = "UPDATE sales SET";
      $sql .= " product_id = '{$p_id}', qty = {$s_qty}, price = '{$s_total}', total = '{$s_total}', date = '{$s_date}'";
      $sql .= " WHERE id = '{$sale['id']}'";
      $result = $db->query($sql);

      if ($result && $db->affected_rows() === 1) {
          // Update product quantity
          $sql_product = "UPDATE products SET quantity = quantity - {$qty_difference} WHERE id = '{$p_id}'";
          $result_product = $db->query($sql_product);

          if ($result_product) {
              $session->msg('s', "Sale updated successfully.");
              redirect('edit_sale.php?id=' . $sale['id'], false);
          } else {
              $session->msg('d', 'Failed to update product quantity!');
              redirect('sales.php', false);
          }
      } else {
          $session->msg('d', 'Sorry, failed to update sale!');
          redirect('sales.php', false);
      }
  } else {
      $session->msg("d", $errors);
      redirect('edit_sale.php?id=' . (int)$sale['id'], false);
  }
}
?>

<!-- HTML Form Code (same as before) -->


<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-6">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<div class="workboard__heading">
    <h1 class="workboard__title">Edit Sale</h1>
</div>

<div class="workpanel product__main">
    <div class="overall-info">
        <div class="row">
            <div class="col xs-12">
                <form class="general--form access__form info" method="post" action="edit_sale.php?id=<?php echo (int)$sale['id']; ?>">
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
                                            name="update_sale"
                                            value="Save" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="prodname" class="form__label">Product Name</label>
                                <div class="form__set">
                                    <input type="text" class="form-control" id="sug_input" name="title" value="<?php echo remove_junk($product['name']); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="prodprice" class="form__label">Sale Price</label>
                                <div class="form__set">
                                    <input type="tel" name="price" value="<?php echo remove_junk($sale['price']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="prodqty" class="form__label">Quantity</label>
                                <div class="form__set">
                                    <input type="number" id="prodqty" name="quantity" value="<?php echo (int)$sale['qty']; ?>" placeholder="Product Quantity" required>
                                </div>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="prodtotprice" class="form__label">Total Price</label>
                                <div class="form__set">
                                    <input type="tel" name="total" value="<?php echo remove_junk($sale['total']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="proddate" class="form__label">Date</label>
                                <div class="form__set">
                                    <input type="date" name="date" value="<?php echo remove_junk($sale['date']); ?>" required>
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
