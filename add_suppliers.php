<?php
$page_title = 'Add Suppliers';
require_once('includes/load.php');
// Check what level user has permission to view this page
page_require_level(3);
$all_products = find_all('products');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_supplier'])) {
        $name = remove_junk($db->escape($_POST['name']));
        $email = remove_junk($db->escape($_POST['email']));
        $contact = remove_junk($db->escape($_POST['contact']));
        $product = remove_junk($db->escape($_POST['product'])); // Update field name here
        $date = remove_junk($db->escape($_POST['date']));

        if (empty($name) || empty($email) || empty($contact) || empty($product) || empty($date)) {
            $session->msg("d", "Please fill in all fields.");
        } else {
            $query = "INSERT INTO suppliers (name, email, contact, product_id, joined_date) VALUES ('{$name}', '{$email}', '{$contact}', '{$product}', '{$date}')";
            if ($db->query($query)) {
                $session->msg("s", "Supplier added successfully.");
                redirect('manage_suppliers.php', false);
            } else {
                $session->msg("d", "Failed to add supplier.");
            }
        }
    } elseif (isset($_POST['discard'])) {
        redirect('manage_suppliers.php', false);
    }
}

// Fetch existing suppliers to display dynamically
$suppliers = $db->query("SELECT * FROM suppliers ORDER BY joined_date DESC");

?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="workboard__heading">
  <h1 class="workboard__title">Add Suppliers</h1>
</div>
<div class="workpanel product__main">
  <div class="overall-info">
    <div class="row">
      <div class="col xs-12">
        <form class="general--form access__form info" method="post" action="add_suppliers.php">
          <div class="info">
            <div class="row">
              <div class="col xs-12 sx-6">
                <span>Add Supplier Details</span>
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
                    name="save_supplier"
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
                 <input type="text" class="form-control" id="name" name="name" placeholder="Supplier Name">
               </div>
             </div>
           </div>
           <div class="col xs-12 sm-3">
            <div class="form__module">
              <label for="email" class="form__label">Email</label>
              <div class="form__set">
                <input
                type="email"
                name="email" placeholder="abc@example.com">
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
                placeholder="+977 9822-" />
              </div>
            </div>
          </div>
          <div class="col xs-12 sm-3">
            <div class="form__module">
            <label for="product" class="form__label">Product</label> <!-- Update name to "product" -->
                <select class="form-control" id="product" name="product"> <!-- Update name to "product" -->
                  <option value="">Select Product</option>
                  <?php foreach ($all_products as $product): ?>
                    <option value="<?php echo (int)$product['id'] ?>">
                      <?php echo $product['name'] ?></option>
                    <?php endforeach; ?>
                  </select>
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
                name="date" >
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
