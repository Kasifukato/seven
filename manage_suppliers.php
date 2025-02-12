<?php
$page_title = 'Manage Suppliers';
require_once('includes/load.php');
page_require_level(3);

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $query = "DELETE FROM suppliers WHERE id = '{$delete_id}'";
    if ($db->query($query)) {
        $session->msg("s", "Supplier deleted successfully.");
    } else {
        $session->msg("d", "Failed to delete supplier.");
    }
    redirect('manage_suppliers.php', false);
}

// Fetch supplier data from the database
$suppliers = [];
$sql = "SELECT 
            suppliers.id, 
            suppliers.name, 
            suppliers.email, 
            suppliers.contact, 
            suppliers.product_id, 
            products.name AS product_name, 
            suppliers.joined_date 
        FROM suppliers 
        JOIN products ON products.id = suppliers.product_id 
        ORDER BY suppliers.joined_date DESC";
$result = $db->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }
}

// Navigation to add_suppliers.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_Suppliers'])) {
    header("Location: add_suppliers.php");
    exit;
}
?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
    <div class="col-md-6">
        <?php echo display_msg($msg); ?>
    </div>
</div>
<div class="workboard__heading">
    <h1 class="workboard__title">Manage Suppliers</h1>
</div>
<div class="workpanel report__main">
    <div class="row">
        <div class="col xs-12">
            <div class="meta-info">
                <div class="row">
                    <div class="col xs-12 sm-6">
                        <h2 class="subheading">List of Suppliers</h2>
                    </div>
                    <div class="col xs-12 sm-6">
                        <form method="POST">
                            <div class="site-panel">
                                <div class="form__module">
                                    <div class="form__action">
                                        <span class="icon-add"></span>
                                        <input type="submit" class="button primary-tint" value="Add Supplier" name="add_Suppliers">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col xs-12">
                        <div class="questionaries__showcase" id="question_popup" style="display: flex;">
                            <div class="tbl-wrap">
                                <table id="sales__table">
                                    <thead>
                                        <tr>
                                            <th class="S.N">SN.</th>
                                            <th class="name">Supplier Name</th>
                                            <th class="email">Email</th>
                                            <th class="contact">Contact</th>
                                            <th class="prod">Product</th>
                                            <th class="joined">Joined Date</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($suppliers as $key => $supplier): ?>
                                            <tr>
                                                <td class="text-center"> <?php echo $key + 1; ?> </td>
                                                <td> <?php echo htmlspecialchars($supplier['name']); ?> </td>
                                                <td class="text-center"> <?php echo htmlspecialchars($supplier['email']); ?> </td>
                                                <td class="text-center"> <?php echo htmlspecialchars($supplier['contact']); ?> </td>
                                                <td class="text-center"> <?php echo htmlspecialchars($supplier['product_name']); ?> </td> <!-- Use product_name -->
                                                <td class="text-center"> <?php echo htmlspecialchars($supplier['joined_date']); ?> </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <a href="edit_suppliers.php?id=<?php echo $supplier['id']; ?>" class="btn btn-warning btn-xs" title="Edit" data-toggle="tooltip">
                                                            <span class="icon-edit"></span>
                                                        </a>
                                                        <a href="manage_suppliers.php?delete_id=<?php echo $supplier['id']; ?>" class="btn btn-danger btn-xs" title="Delete" onclick="return confirm('Are you sure you want to delete this supplier?');">
                                                            <span class="icon-trash"></span>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                 </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
