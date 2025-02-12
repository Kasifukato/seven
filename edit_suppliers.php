<?php
$page_title = 'Edit Supplier';
require_once('includes/load.php');
// Check what level user has permission to view this page
page_require_level(3);

// Validate supplier ID
if (isset($_GET['id'])) {
    $supplier_id = (int)$_GET['id'];
    $supplier = find_by_id('suppliers', $supplier_id);

    if (!$supplier) {
        $session->msg("d", "Supplier not found.");
        redirect('manage_suppliers.php');
    }
} else {
    $session->msg("d", "Missing supplier ID.");
    redirect('manage_suppliers.php');
}

// Fetch products for dropdown (now just for display)
$products = find_all('products');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_supplier'])) {
        $email = remove_junk($db->escape($_POST['email']));
        $contact = remove_junk($db->escape($_POST['contact']));

        if (empty($email) || empty($contact)) {
            $session->msg("d", "Please fill in all fields.");
        } else {
            $query = "UPDATE suppliers SET ";
            $query .= "email='{$email}', contact='{$contact}' ";
            $query .= "WHERE id='{$supplier_id}'";

            if ($db->query($query)) {
                $session->msg("s", "Supplier updated successfully.");
                redirect('manage_suppliers.php', false);
            } else {
                $session->msg("d", "Failed to update supplier.");
            }
        }
    } elseif (isset($_POST['discard'])) {
        redirect('manage_suppliers.php', false);
    }
}
?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="workboard__heading">
  <h1 class="workboard__title">Edit Supplier</h1>
</div>
<div class="workpanel product__main">
  <div class="overall-info">
    <div class="row">
      <div class="col xs-12">
        <form class="general--form access__form info" method="post" action="edit_suppliers.php?id=<?php echo $supplier_id; ?>">
          <div class="info">
            <div class="row">
              <div class="col xs-12 sx-6">
                <span>Edit Supplier Details</span>
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
                    name="update_supplier"
                    value="Save" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col xs-12 sm-3">
              <div class="form__module">
                <label for="name" class="form__label">Supplier Name</label>
                <div class="form__set">
                 <input type="text" class="form-control" id="name" name="name" value="<?php echo remove_junk($supplier['name']); ?>" readonly style="background-color: #f0f0f0;">
               </div>
             </div>
           </div>
           <div class="col xs-12 sm-3">
            <div class="form__module">
              <label for="email" class="form__label">Email</label>
              <div class="form__set">
                <input
                type="email"
                name="email" value="<?php echo remove_junk($supplier['email']); ?>">
              </div>
            </div>
          </div>
          <div class="col xs-12 sm-3">
            <div class="form__module">
              <label for="contact" class="form__label">Contact</label>
              <div class="form__set">
                <input
                type="tel"
                id="contact"
                name="contact"
                value="<?php echo remove_junk($supplier['contact']); ?>">
              </div>
            </div>
          </div>
          <div class="col xs-12 sm-3">
            <div class="form__module">
              <label for="product_id" class="form__label">Product</label>
              <div class="form__set">
                <select name="product_id" class="form-control" disabled style="background-color: #f0f0f0;">
                  <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>" <?php if ($supplier['product_id'] == $product['id']) echo "selected"; ?>>
                      <?php echo $product['name']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <!-- Hidden input to preserve the product_id value -->
                <input type="hidden" name="product_id" value="<?php echo $supplier['product_id']; ?>">
              </div>
            </div>
          </div>
          <div class="col xs-12 sm-3">
            <div class="form__module">
              <label for="date" class="form__label">Date</label>
              <div class="form__set">
                <input
                type="date"
                id="date"
                name="date" 
                value="<?php echo remove_junk($supplier['joined_date']); ?>"
                readonly 
                style="background-color: #f0f0f0;">
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