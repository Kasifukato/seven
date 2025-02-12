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

$supplier_product_data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $supplier_product_data[] = [
            'supplier_name' => $row['supplier_name'],
            'product_name' => $row['product_name'],
            'product_id' => $row['p_id'],
            'supplier_email' => $row['email'],
            'supplier_id' => $row['id']
        ];
    }
}

require 'mail_sender.php';

// Convert the PHP array into a JSON string for JavaScript
$supplier_product_data_json = json_encode($supplier_product_data);

// Pre-fill supplier and product fields if redirected from "Send Order"
$supplier_prefilled = isset($_GET['supplier']) ? $_GET['supplier'] : '';
$product_prefilled = isset($_GET['product']) ? $_GET['product'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_orders'])) {
        $supplier = $db->escape($_POST['supplier-id']);
        $quantity = $db->escape($_POST['product-quantity']);
        $product_id = null;
        $email = null;
        $supplier_id = null;
        $product_name = null;
        $sent_quantity = 0;

        // Validate quantity
        if (!is_numeric($quantity) || $quantity <= 0) {
            $session->msg('d', 'Quantity must be greater than zero.');
            redirect('add_orders.php', false);
            exit;
        }

        // Find the product ID, email, and supplier ID based on the selected supplier
        foreach ($supplier_product_data as $data) {
            if ($data['supplier_name'] === $supplier) {
                $product_id = $data['product_id'];
                $email = $data['supplier_email'];
                $supplier_id = $data['supplier_id'];
                $product_name = $data['product_name'];
                break;
            }
        }

        if (empty($supplier) || empty($quantity) || !$product_id) {
            $session->msg('d', 'Please fill all required fields.');
            redirect('add_orders.php', false);
            exit;
        } else {
            $query = "INSERT INTO orders (supplier_id, product_id, required_quantity, sent_quantity, status) 
                      VALUES ('{$supplier_id}', '{$product_id}', '{$quantity}', '{$sent_quantity}', 'Pending')";

            if ($db->query($query)) {
                $new_order_id = $db->insert_id;
                $saleFormUrl = "http://localhost/ims_php/form.php?id={$new_order_id}";

                if (sendEmail($email, $supplier, $product_name, $quantity, $saleFormUrl)) {
                    // Redirect back to the same page after success
                    header('Location: add_orders.php?status=success&order_id=' . $new_order_id);
                    exit;
                } else {
                    $session->msg('d', 'Order saved, but email sending failed.');
                    redirect('add_orders.php', false);
                }
            } else {
                $session->msg('d', 'Failed to add order.');
                redirect('add_orders.php', false);
            }
            exit;
        }
    }
}
?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<!-- Display Success Message after Redirect -->
<?php
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    echo "<p>Order added successfully!" . htmlspecialchars($_GET['order_id']) . "</p>";
}
?>

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
                                <select class="form-control" id="supplier-id" name="supplier-id" required>
                                    <option value="">Select Supplier</option>
                                    <?php foreach ($supplier_product_data as $supplier_data): ?>
                                        <option value="<?php echo $supplier_data['supplier_name']; ?>" 
                                            <?php echo ($supplier_data['supplier_name'] === $supplier_prefilled) ? 'selected' : ''; ?>>
                                            <?php echo $supplier_data['supplier_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="product-id" class="form__label">Product</label>
                                <input id="product_name" type="text" 
                                       value="<?php echo $product_prefilled ? htmlspecialchars($product_prefilled) : 'Select supplier'; ?>" 
                                       disabled>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="product-quantity" class="form__label">Quantity</label>
                                <input type="number" 
                                       id="product-quantity" 
                                       name="product-quantity" 
                                       placeholder="Product Quantity" 
                                       class="form-control"
                                       min="1"
                                       required
                                       oninput="validateQuantity(this)" />
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

    const supplierSelect = document.getElementById('supplier-id');
    const productInput = document.getElementById('product_name');

    supplierSelect.addEventListener('change', function() {
        const selectedSupplier = supplierSelect.value;
        const product = getProductBySupplier(selectedSupplier);
        productInput.value = product ? product : 'Select supplier';
    });

    function getProductBySupplier(supplierName) {
        const supplier = supplierProductData.find(supplier => supplier.supplier_name === supplierName);
        return supplier ? supplier.product_name : null;
    }

    // Function to validate quantity input
    function validateQuantity(input) {
        if (input.value <= 0) {
            input.value = 1;
        }
    }
</script>