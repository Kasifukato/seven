<?php
$page_title = 'Add Group';
require_once('includes/load.php');
  // Checkin What level user has permission to view this page
page_require_level(1);

// Handle Discard button click
if (isset($_POST['discard'])) {
    // Redirect to group.php
    redirect('group.php', false);
    exit;
}

if(isset($_POST['add'])){

	$req_fields = array('group-name','group-level');
	validate_fields($req_fields);

	if(find_by_groupName($_POST['group-name']) === false ){
		$session->msg('d','<b>Sorry!</b> Entered Group Name already in database!');
		redirect('add_group.php', false);
	}elseif(find_by_groupLevel($_POST['group-level']) === false) {
		$session->msg('d','<b>Sorry!</b> Entered Group Level already in database!');
		redirect('add_group.php', false);
	}
	if(empty($errors)){
		$name = remove_junk($db->escape($_POST['group-name']));
		$level = remove_junk($db->escape($_POST['group-level']));
		$status = remove_junk($db->escape($_POST['status']));

		$query  = "INSERT INTO user_groups (";
			$query .="group_name,group_level,group_status";
			$query .=") VALUES (";
			$query .=" '{$name}', '{$level}','{$status}'";
			$query .=")";
			if($db->query($query)){
          //sucess
				$session->msg('s',"Group has been creted! ");
				redirect('add_group.php', false);
			} else {
          //failed
				$session->msg('d',' Sorry failed to create Group!');
				redirect('add_group.php', false);
			}
		} else {
			$session->msg("d", $errors);
			redirect('add_group.php',false);
		}
	}
	?>
	<?php include_once('layouts/header.php'); ?>
	<div class="workboard__heading">
		<h1 class="workboard__title">Add Group</h1>
	</div>
	<?php echo display_msg($msg); ?>
	<div class="workpanel">
		<form class="general--form access__form" method="post" action="add_group.php">
			<div class="overall-info">
				<div class="row">
					<div class="col xs-12">
						<div class="info">
							<div class="row">
								<div class="col xs-12 sx-6">
									<span>Add User Group</span>
								</div>
								<div class="col xs-12 sx-6">
									<form action="get">
										<div class="site-panel">
											<div class="form__action">
												<input type="submit" class="button tertiary-line" name="discard" value="Discard">
											</div>
											<div class="form__action">
												<input type="submit"  name="add" class="button primary-tint" value="Save">
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col xs-12 sm-3">
								<div class="form__module">
									<label for="prodname" class="form__label">Group Name</label>
									<div class="form__set">
										<input type="text" id="prodname" placeholder="Group Name">
									</div>
								</div>
							</div>
							<div class="col xs-12 sm-3">
								<div class="form__module">
									<label for="saleqty" class="form__label">Group Level</label>
									<div class="form__set">
										<input type="tel" id="saleqty" placeholder="Group Level">
									</div>
								</div>
							</div>
							<div class="col xs-12 sm-3">
								<div class="form__module">
									<label for="saletot" class="form__label">Status</label>
									<div class="form__set">
										<select name="" id="">
											<option value="1">Active </option>
											<option value="0">In-Active </option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<?php include_once('layouts/footer.php'); ?>