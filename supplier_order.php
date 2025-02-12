<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$page_title = 'Supplier Orders';
require_once('includes/load.php');
page_require_level(4);

// Ensure only suppliers can access this page
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Supplier') {
    $session->msg("d", "Access denied!");
    redirect('admin.php', false);
    exit;
}

// --- Get Logged-in User Info --- //
$user_id = $_SESSION['user_id'];

// Get the user's name from session or database
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $user_name = $_SESSION['user_name'];
} else {
    $user_query = "SELECT name FROM users WHERE id = " . (int)$user_id . " LIMIT 1";
    $user_result = $db->query($user_query);
    if ($user_result && $user_row = $user_result->fetch_assoc()) {
        $user_name = $user_row['name'];
        $_SESSION['user_name'] = $user_name;  // store for future use
    } else {
        $session->msg("d", "User record not found.");
        redirect('admin.php', false);
        exit;
    }
}

// --- Look Up the Supplier Record --- //
// IMPORTANT: The value in the suppliers table for the name must match the user's name exactly.
// (If possible, consider using a unique field such as email or a dedicated supplier_id.)
$user_name_escaped = $db->escape($user_name);

// Use a case‑insensitive match to help avoid issues with capitalization.
$sql = "SELECT id, name FROM suppliers WHERE LOWER(name) = LOWER('{$user_name_escaped}') LIMIT 1";
$result = $db->query($sql);
if ($result && $supplier = $result->fetch_assoc()) {
    $supplier_id = (int)$supplier['id'];
} else {
    $session->msg("d", "No supplier record found for user: " . $user_name);
    redirect('admin.php', false);
    exit;
}

// --- Fetch Orders for This Supplier --- //
$query = "SELECT o.id, p.name AS product_name, o.required_quantity, o.sent_quantity, o.status 
          FROM orders o
          JOIN products p ON o.product_id = p.id 
          WHERE o.supplier_id = {$supplier_id}
          ORDER BY o.id DESC";
$result = $db->query($query);

$orders = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
} else {
    $session->msg("d", "Database error: " . $db->error);
    redirect('admin.php', false);
    exit;
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="adm-dashboard__main">
  <?php echo display_msg($msg); ?>
  <div class="workboard__heading">
    <h1 class="workboard__title">Supplier Orders</h1>
  </div>
  <div class="workpanel">
    <div class="recent-orders">
      <div class="row">
        <div class="col xs-12">
          <h2 class="subheading">Recent Orders</h2>
          <div class="tbl-wrap">
            <table id="orders-table">
              <thead>
                <tr>
                  <th>Product Name</th>
                  <th>Required Quantity</th>
                  <th>Sent Quantity</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="orders-body">
                <?php if (!empty($orders)) : ?>
                  <?php foreach ($orders as $order): ?>
                    <tr>
                      <td><?php echo remove_junk(first_character($order['product_name'])); ?></td>
                      <td><?php echo (int)$order['required_quantity']; ?></td>
                      <td><?php echo (int)$order['sent_quantity']; ?></td>
                      <td><?php echo htmlspecialchars($order['status']); ?></td>
                      <td>
                        <div class="btn-group">
                          <?php if (strcasecmp($order['status'], 'sent') === 0) : ?>
                            <!-- If status is sent, show delete button -->
                            <a href="delete_order.php?id=<?php echo (int)$order['id']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you want to delete this order?');">
                              <span class="icon-trash"></span>
                            </a>
                          <?php else : ?>
                            <!-- Otherwise, show view button -->
                            <a href="form.php?id=<?php echo (int)$order['id']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="View">
                              <span class="icon-eye"></span>
                            </a>
                          <?php endif; ?>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5">No orders found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>

<!-- Optional JavaScript for AJAX order submission -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    let form = document.getElementById("add-order-form");
    if (form) {
        form.addEventListener("submit", function(event) {
            event.preventDefault();

            let formData = new FormData(this);
            fetch("add_orders.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    let newRow = `
                        <tr>
                            <td>${data.order.product_name}</td>
                            <td>${data.order.required_quantity}</td>
                            <td>${data.order.sent_quantity}</td>
                            <td>${data.order.status}</td>
                            <td>
                                <div class="btn-group">
                                    ${ data.order.status.toLowerCase() === 'sent' ? `
                                      <a href="delete_order.php?id=${data.order.id}" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you want to delete this order?');">
                                          <span class="icon-trash"></span>
                                      </a>
                                    ` : `
                                      <a href="form.php?id=${data.order.id}" class="btn btn-xs btn-warning" data-toggle="tooltip" title="View">
                                          <span class="icon-eye"></span>
                                      </a>
                                    `}
                                </div>
                            </td>
                        </tr>`;
                    
                    document.getElementById("orders-body").insertAdjacentHTML("afterbegin", newRow);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    }
});
</script>
