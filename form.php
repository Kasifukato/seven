<?php
$page_title = 'Order Form';
require_once('includes/load.php');

$token = isset($_GET['token']) ? $db->escape($_GET['token']) : null;

$query = "SELECT o.id, o.required_quantity, p.name AS product_name, 
s.name AS supplier_name, s.contact AS supplier_contact
FROM orders o
JOIN products p ON o.product_id = p.id
JOIN suppliers s ON o.supplier_id = s.id
WHERE o.token = '{$token}'";

$result = $db->query($query);



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the request is for updating the sent quantity
    if (isset($_POST['sent_quantity'])) {
        // Escape and sanitize inputs
        $sent_quantity = $db->escape($_POST['sent_quantity']);
        
        // Validate form inputs
        if (empty($sent_quantity) || empty($token)) {
            $session->msg('d', 'Please fill all required fields.');
            redirect('inventorypg.php'); // Replace with the correct redirection URL
        }

        // Check if the token exists and is valid
        $query = "SELECT id, sent_quantity, expiry_time FROM orders WHERE token = '{$token}'";
        $result = $db->query($query);

        if ($result && $db->num_rows($result) > 0) {
            $order = $db->fetch_assoc($result);

            // Check token expiry
            if (strtotime($order['expiry_time']) < time()) {
                $session->msg('d', 'The token has expired.');
                redirect('inventorypg.php'); // Replace with the correct redirection URL
            } else {
                // Check if sent_quantity is already filled
                if (!empty($order['sent_quantity'])) {
                    $session->msg('d', 'The sent quantity has already been filled.');
                    redirect('thankyou.php'); // Redirect to the thank you page if already filled
                } else {
                    // Update the sent_quantity field
                    $order_id = $order['id'];
                    $update_query = "UPDATE orders SET sent_quantity = '{$sent_quantity}' WHERE id = '{$order_id}'";

                    if ($db->query($update_query)) {
                        $session->msg('s', 'Sent quantity updated successfully.');
                        redirect('thankyou.php'); // Redirect to thankyou.php after successful update
                    } else {
                        $session->msg('d', 'Failed to update sent quantity.');
                        redirect('inventorypg.php'); // Replace with the correct redirection URL
                    }
                }
            }
        } else {
            $session->msg('d', 'Invalid or missing token.');
            redirect('inventorypg.php'); // Replace with the correct redirection URL
        }
    }
}

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
                    // Assuming $result contains the query result
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
                        // If no data is found
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
														<span >Sent Quantity</span>
													</div>
													<div class="col xs-12 sm-2">
              <form class="general--form access__form login__form" method="post" action="">
              <div class="form__module">
          <div class="form__set">
          <input type="tel" id="sent_quantity" name="sent_quantity">
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
              </div>
            </div>
          </section>
        </main>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="test.js"></script>
</body>
</html>