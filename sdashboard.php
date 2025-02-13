<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once('includes/load.php');

if (!$session->isUserLoggedIn()) { 
    redirect('index.php', false);
}

// Get logged-in user's name and validate user role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Supplier') {
    $session->msg("d", "Access denied!");
    redirect('admin.php', false);
    exit;
}

$user_name = $_SESSION['user_name'] ?? '';

if (empty($user_name)) {
    $session->msg("d", "User not found.");
    redirect('index.php', false);
}

// Escape the name to prevent SQL injection
$user_name_escaped = $db->escape($user_name);

// Fetch the supplier ID using the name
$supplier_query = "SELECT id FROM suppliers WHERE LOWER(name) = LOWER('{$user_name_escaped}') LIMIT 1";
$supplier_result = $db->query($supplier_query);

if ($supplier_result && $supplier = $supplier_result->fetch_assoc()) {
    $supplier_id = (int)$supplier['id'];
} else {
    $session->msg("d", "No supplier record found.");
    redirect('index.php', false);
}

// Fetch Pending Orders Count
$pending_query = "SELECT COUNT(*) AS pending_count FROM orders WHERE supplier_id = {$supplier_id} AND status = 'Pending'";
$pending_result = $db->query($pending_query);
$pending_count = ($pending_result) ? $pending_result->fetch_assoc()['pending_count'] : 0;



// Fetch Recent Orders
$recent_orders_query = "
    SELECT 
        o.id,
        p.name AS product_name,
        o.required_quantity,
        o.sent_quantity,
        o.status,
        o.order_date
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.supplier_id = {$supplier_id}
    ORDER BY o.order_date DESC
    LIMIT 10";
$recent_orders_result = $db->query($recent_orders_query);

$page_title = 'Supplier Dashboard';
include_once('layouts/nheader.php'); 
?>

<div class="adm-dashboard__main">
    <?php echo display_msg($msg); ?>
    <div class="workboard__heading">
        <h1 class="workboard__title">Dashboard</h1>
    </div>
    <div class="workpanel">
        <div class="row">
            <div class="col xs-12">
                <div class="date">
                    <input type="date" value="<?php echo date('Y-m-d'); ?>" />
                </div>
            </div>
        </div>
        <div class="insights">
            <div class="row">
                <!-- Pending Orders -->
                <div class="col xs-12 sm-3">
                    <div class="panel">
                        <div class="expenses">
                            <div class="middle">
                                <div class="left">
                                    <h3>Pending</h3>
                                    <h1><?php echo $pending_count; ?></h1>
                                </div>
                                <div class="progress">
                                    <span class="icon-edit"></span>
                                </div>
                            </div>
                            <small class="text-muted">Total pending orders</small>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- Recent Orders Table -->
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
                                    <th>Date</th>
                                   
                                </tr>
                            </thead>
                            <tbody id="orders-body">
                                <?php if ($recent_orders_result && $recent_orders_result->num_rows > 0): ?>
                                    <?php while ($order = $recent_orders_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo remove_junk(first_character($order['product_name'])); ?></td>
                                            <td><?php echo (int)$order['required_quantity']; ?></td>
                                            <td><?php echo (int)$order['sent_quantity']; ?></td>
                                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                            
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No recent orders found</td>
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
<script>
function updateDashboard() {
    // Update the counts
    fetch('fetch_order_counts.php')
        .then(response => response.json())
        .then(data => {
            document.querySelector('.expenses h1').textContent = data.pending;
            document.querySelector('.income h1').textContent = data.supplied;
        })
        .catch(error => console.error('Error fetching data:', error));
    
    // Update the recent orders table
    fetch('fetch_recent_orders.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#orders-body');
            tbody.innerHTML = '';
            
            if (data.length > 0) {
                data.forEach(order => {
                    const actionButton = order.status.toLowerCase() === 'sent' 
                        ? `<a href="delete_order.php?id=${order.id}" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you want to delete this order?');"><span class="icon-trash"></span></a>`
                        : `<a href="form.php?id=${order.id}" class="btn btn-xs btn-warning" data-toggle="tooltip" title="View"><span class="icon-eye"></span></a>`;
                    
                    tbody.innerHTML += `
                        <tr>
                            <td>${order.product_name}</td>
                            <td>${order.required_quantity}</td>
                            <td>${order.sent_quantity}</td>
                            <td>${order.status}</td>
                            <td>${new Date(order.order_date).toLocaleDateString('en-US', {
                                month: 'short',
                                day: 'numeric',
                                year: 'numeric'
                            })}</td>
                            
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No recent orders found</td></tr>';
            }
        })
        .catch(error => console.error('Error fetching recent orders:', error));
}

// Refresh dashboard every 10 seconds
setInterval(updateDashboard, 10000);
</script>