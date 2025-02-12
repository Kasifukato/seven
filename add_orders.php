<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = 'Add Order';
require_once('includes/load.php');
page_require_level(2);

// Fetch all suppliers and their corresponding products
$query = "SELECT suppliers.name AS supplier_name, products.name AS product_name , products.id as p_id, suppliers.email, suppliers.id
          FROM suppliers 
          JOIN products ON products.id = suppliers.product_id";
$result = $db->query($query);

// Initialize an empty array to hold supplier-product data
$supplier_product_data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $supplier_product_data[] = [
            'supplier_name' => $row['supplier_name'],
            'product_name' => $row['product_name'],
            'product_id' =>$row['p_id'],
            'supplier_email' => $row['email'],
            'supplier_id' => $row['id']
        ];
    }
}

require 'mail_sender.php';
// Convert the PHP array into a JSON string
$supplier_product_data_json = json_encode($supplier_product_data);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['add_orders'])) {
      $supplier = $db->escape($_POST['supplier-id']);
      $quantity = $db->escape($_POST['product-quantity']);
      $product_id = null;
      $email = null;
      $supplier_id = null;
      $product_name = null;
      $sent_quantity = 0;

      // Get the product name for the selected supplier
      foreach ($supplier_product_data as $data) {
          if ($data['supplier_name'] === $supplier) {
              $product_id = $data['product_id'];
              $email = $data['supplier_email'];
              $supplier_id = $data['supplier_id'];
              $product_name = $data['product_name'];
              break;
          }
      }

      // Validate form inputs
      if (empty($supplier) || empty($quantity) || !$product_id) {
          $session->msg('d', 'Please fill all required fields.');
          redirect('add_orders.php');
      } else {
          // Generate a random token and expiry time
          $token = bin2hex(random_bytes(16)); // 32-character random token
          $expiry_time = date('Y-m-d H:i:s', strtotime('+24 hour')); // Expiry time 1 hour from now

          // Save order to database
          $query = "INSERT INTO orders (supplier_id, product_id, required_quantity, token, expiry_time, sent_quantity) 
                    VALUES ('{$supplier_id}', '{$product_id}', '{$quantity}', '{$token}', '{$expiry_time}', '{$sent_quantity}')";

          if ($db->query($query)) {
              // Prepare email details
              $recipientEmail = $email; // Replace with the supplier's email
              $recipientName = $supplier;
              $saleFormUrl = "http://localhost/ims_php/form.php?token={$token}"; // Replace with the actual URL

              if (sendEmail($recipientEmail, $recipientName, $product_name, $quantity, $saleFormUrl)) {
                $session->msg('s', "Order added successfully and email sent to {$recipientEmail}.");
              } else {
                $session->msg('d', "Order added successfully, but the email could not be sent to {$recipientEmail}.");
              }
              redirect('add_orders.php');

          } else {
              $session->msg('d', 'Failed to add order.');
          }
      }
  } elseif (isset($_POST['discard'])) {
      $session->msg('i', 'Order creation discarded.');
      redirect('add_orders.php');
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
    <h1 class="workboard__title">Add Order</h1>
</div>
<div class="workpanel inventorypg">
    <div class="overall-info">
        <div class="row">
            <div class="col xs-12">
                <form class="general--form access__form info" method="post" action="" class="clearfix">
                    <div class="info">
                        <div class="row">
                            <div class="col xs-12 sx-6">
                                <span>New Order</span>
                            </div>
                            <div class="col xs-12 sx-6">
                                <div class="site-panel">
                                    <div class="form__action">
                                        <input type="submit" class="button tertiary-line" value="Discard" name="discard" />
                                    </div>
                                    <div class="form__action">
                                        <input type="submit" class="button primary-tint" name="add_orders" value="Save" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="supplier-id" class="form__label">Supplier</label>
                                <select class="form-control" id="supplier-id" name="supplier-id">
                                    <option value="">Select Supplier</option>
                                    <?php
                                    // Populate the dropdown with supplier names from the query result
                                    foreach ($supplier_product_data as $supplier_data): ?>
                                        <option value="<?php echo $supplier_data['supplier_name']; ?>">
                                            <?php echo $supplier_data['supplier_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="product-id" class="form__label">Product</label>
                                <input id="product_name" type="text" value='Select supplier' disabled>
                            </div>
                        </div>

                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="product-quantity" class="form__label">Quantity</label>
                                <input type="number" id="product-quantity" name="product-quantity" placeholder="Product Quantity" class="form-control" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
<script>
    // The supplier-product data in JSON format
    const supplierProductData = <?php echo $supplier_product_data_json; ?>;

    // Get references to the HTML elements
    const supplierSelect = document.getElementById('supplier-id');
    const productInput = document.getElementById('product_name');

    // Event listener for when the supplier is changed
    supplierSelect.addEventListener('change', function() {
        const selectedSupplier = supplierSelect.value;
        const product = getProductBySupplier(selectedSupplier);
        
        // If a matching supplier is found, update the product input field
        if (product) {
            productInput.value = product;
        } else {
            productInput.value = 'Select supplier'; // Default text
        }
    });

    // Function to get the product name by supplier name
    function getProductBySupplier(supplierName) {
        const supplier = supplierProductData.find(supplier => supplier.supplier_name === supplierName);
        return supplier ? supplier.product_name : null;
    }
</script>
