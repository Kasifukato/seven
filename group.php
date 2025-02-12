<?php
$page_title = 'All Group';
require_once('includes/load.php');
page_require_level(1);
$all_groups = find_all('user_groups');

// Navigation to add_product.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_group'])) {
	header("Location: add_group.php");
	exit;
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="workboard__heading">
	<h1 class="workboard__title">Groups</h1>
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
								<input type="submit" class="button primary-tint" value="Add Group" name="add_group">
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
								<th class="name">Group Name</th>
								<th class="username">Group Level</th>
								<th class="status">Status</th>
								<th class="action">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($all_groups as $a_group): ?>
								<tr>
									<td class="text-center"><?php echo count_id();?></td>
									<td><?php echo remove_junk(ucwords($a_group['group_name']))?></td>
									<td class="text-center">
										<?php echo remove_junk(ucwords($a_group['group_level']))?>
									</td>
									<td class="text-center">
										<?php if($a_group['group_status'] === '1'): ?>
											<span class="label label-success"><?php echo "Active"; ?></span>
										<?php else: ?>
											<span class="label label-danger"><?php echo "Deactive"; ?></span>
										<?php endif;?>
									</td>
									<td class="text-center">
										<div class="btn-group">
											<a href="edit_group.php?id=<?php echo (int)$a_group['id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
												<span class="icon-edit"></span>
											</a>
											<a href="delete_group.php?id=<?php echo (int)$a_group['id'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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