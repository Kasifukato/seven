<?php
$page_title = 'Order Form';
require_once('includes/load.php');
page_require_level(4);  // adjust level if needed

// Check for valid order ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $session->msg('d', "Missing order ID");
    redirect('supplier_order.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['sent_quantity'])) {
        $sent_quantity = $db->escape($_POST['sent_quantity']);
        $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Validate inputs
        if (empty($sent_quantity) || empty($order_id)) {
            $session->msg('d', 'Please fill all required fields.');
            redirect("form.php?id=$order_id");
            exit;
        }

        // Update the sent_quantity
        $update_query = "UPDATE orders 
                        SET sent_quantity = '{$sent_quantity}',
                            status = 'Sent' 
                        WHERE id = '{$order_id}'";

        if ($db->query($update_query)) {
            $session->msg('s', 'Sent quantity updated successfully.');
            redirect('supplier_order.php');
            exit;
        } else {
            $session->msg('d', 'Failed to update sent quantity.');
            redirect("form.php?id=$order_id");
            exit;
        }
    }
}

// Get order details
$order_id = (int)$_GET['id'];
$query = "SELECT o.id, o.required_quantity, p.name AS product_name, 
          s.name AS supplier_name, s.contact AS supplier_contact
          FROM orders o
          JOIN products p ON o.product_id = p.id
          JOIN suppliers s ON o.supplier_id = s.id
          WHERE o.id = '{$order_id}'";

$result = $db->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS-InView</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div id="wrapper">
        <main id="site__main" class="site__main inview__main">
            <section class="workboard inventorypg">
                <div class="workpanel">
                    <div class="overall-info">
                        <div class="row">
                            <div class="col xs-12">
                                <div class="info">
                                    <span>IMS-Vault Vision</span>
                                </div>
                                <div class="row">
                                    <div class="col xs-12">
                                        <div class="horizonal--nav">
                                            <ul>
                                                <span>Send Order Details</span>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="info--counter">
                                    <div class="left__panel">
                                        <div class="primary__details product__details">
                                            <div class="meta--header">
                                                <span>Primary Details</span>
                                            </div>
                                            <div class="listing_table product_infotable">
                                                <table>
                                                    <?php
                                                    if ($result && $row = $result->fetch_assoc()) {
                                                    ?>
                                                        <tr>
                                                            <th scope="row">Product name</th>
                                                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Required Qty</th>
                                                            <td><?php echo htmlspecialchars($row['required_quantity']); ?></td>
                                                        </tr>
                                                    <?php
                                                    } else {
                                                        echo "<tr><td colspan='2'>No details found.</td></tr>";
                                                    }
                                                    ?>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="supplier__details product__details">
                                            <div class="meta--header">
                                                <span>Suppliers Details</span>
                                            </div>
                                            <div class="listing_table product_infotable">
                                                <table>
                                                    <?php
                                                    if (isset($row)) {
                                                    ?>
                                                        <tr>
                                                            <th scope="row">Supplier name</th>
                                                            <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Contact Number</th>
                                                            <td><?php echo htmlspecialchars($row['supplier_contact']); ?></td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="product__details">
                                    <div class="meta--header">
                                        <span>Sent Quantity</span>
                                    </div>
                                    <div class="col xs-12 sm-2">
                                        <form class="general--form access__form login__form" method="post" action="form.php?id=<?php echo $_GET['id']; ?>">
                                            <div class="form__module">
                                                <div class="form__set">
                                                    <input type="tel" id="sent_quantity" name="sent_quantity" required>
                                                    <input type="hidden" name="order_id" value="<?php echo $_GET['id']; ?>">
                                                </div>
                                            </div>  
                                            <ul class="form__action">
                                                <li><input type="submit" class="button primary-tint" value="Submit"></li>
                                            </ul>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script type="text/javascript" src="test.js"></script>
</body>
</html>