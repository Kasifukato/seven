<?php
$page_title = 'All User';
require_once('includes/load.php');
page_require_level(1);
$all_users = find_all_user();

// Navigation to add_product.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
	header("Location: add_user.php");
	exit;
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="workboard__heading">
	<h1 class="workboard__title">Users</h1>
</div>
<div class="workpanel">
	<div class="meta-info">
		<div class="row">
			<div class="col xs-12">
				<form method="POST">
					<div class="site-panel">
						<div class="form__module">
							<div class="form__action">
								<span class="icon-add"></span>
								<input type="submit" class="button primary-tint" value="Add User" name="add_user">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col xs-12">
			<div class="questionaries__showcase" id="question_popup" style="display: flex;">
				<div class="tbl-wrap">
					<table id="tracking__table">
						<thead>
							<tr>
								<th class="dtser">S.N</th>
								<th class="name">Name</th>
								<th class="username">Username</th>
								<th class="userrole">User Role</th>
								<th class="status">Status</th>
								<th class="last login">Last Login</th>
								<th class="action">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($all_users as $a_user): ?>
								<tr>
									<td class="text-center"><?php echo count_id();?></td>
									<td><?php echo remove_junk(ucwords($a_user['name']))?></td>
									<td><?php echo remove_junk(ucwords($a_user['username']))?></td>
									<td class="text-center"><?php echo remove_junk(ucwords($a_user['group_name']))?></td>
									<td class="text-center">
										<?php if($a_user['status'] === '1'): ?>
											<span class="label label-success"><?php echo "Active"; ?></span>
										<?php else: ?>
											<span class="label label-danger"><?php echo "Deactive"; ?></span>
										<?php endif;?>
									</td>
									<td><?php echo read_date($a_user['last_login'])?></td>
									<td class="text-center">
										<div class="btn-group">
											<!-- <a href="edit_user.php?id=<?php echo (int)$a_user['id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
												<span class="icon-edit"></span>
											</a> -->
											<a href="delete_user.php?id=<?php echo (int)$a_user['id'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
												<span class="icon-trash"></span>
											</a>
										</div>
									</td>
								</tr>
							<?php endforeach;?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include_once('layouts/footer.php'); ?>