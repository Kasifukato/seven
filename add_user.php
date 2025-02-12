<?php
$page_title = 'Add User';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(1);
$groups = find_all('user_groups');

// Handle Discard button click
if (isset($_POST['discard'])) {
    // Redirect to users.php
    redirect('users.php', false);
    exit;
}

// Handle Add User button click
if (isset($_POST['add_user'])) {
    $req_fields = array('full-name', 'username', 'password', 'level');
    validate_fields($req_fields);

    if (empty($errors)) {
        $name = remove_junk($db->escape($_POST['full-name']));
        $username = remove_junk($db->escape($_POST['username']));
        $password = remove_junk($db->escape($_POST['password']));
        $user_level = (int)$db->escape($_POST['level']);
        $password = sha1($password);

        $query = "INSERT INTO users (name, username, password, user_level, status) ";
        $query .= "VALUES ('{$name}', '{$username}', '{$password}', '{$user_level}', '1')";

        if ($db->query($query)) {
            // Success
            $session->msg('s', "User account has been created!");
            redirect('add_user.php', false);
        } else {
            // Failed
            $session->msg('d', 'Sorry, failed to create account!');
            redirect('add_user.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('add_user.php', false);
    }
}
?>
<?php include_once('layouts/header.php'); ?>
				<div class="workboard__heading">
					<h1 class="workboard__title">Users</h1>
				</div>
				<div class="workpanel">
					<div class="overall-info">
						<form class="general--form access__form" method="post" action="add_user.php">
							<div class="row">
								<div class="col xs-12">
									<div class="info">
										<div class="row">
											<div class="col xs-12 sx-6">
												<span>New User</span>
											</div>
											<div class="col xs-12 sx-6">
												<div class="site-panel">
													<div class="form__action">
														<input type="submit" name="discard" class="button tertiary-line" value="Discard">
													</div>
													<div class="form__action">
														<input type="submit" name="add_user" class="button primary-tint" value="Save">
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col xs-12 sm-3">
											<div class="form__module">
												<label for="name" class="form__label">Name</label>
												<div class="form__set">
													<input type="text" name="full-name" id="name" placeholder="Name">
												</div>
											</div>
										</div>
										<div class="col xs-12 sm-3">
											<div class="form__module">
												<label for="usrname" class="form__label">Username</label>
												<div class="form__set">
													<input type="text" name="username" id="usrname" placeholder="Username">
												</div>
											</div>
										</div>
										<div class="col xs-12 sm-3">
											<div class="form__module">
												<label for="usrpwd" class="form__label">Password</label>
												<div class="form__set">
													<input type="Password" name ="password" id="usrpwd" placeholder="Password">
												</div>
											</div>
										</div>
										<div class="col xs-12 sm-3">
											<div class="form__module">
												<label for="usrrole" class="form__label">User Role</label>
												<div class="form__set">
													<!-- <input type="text" id="usrrole" placeholder="Admin"> -->
                          <select class="form-control" name="level">
                  <?php foreach ($groups as $group ):?>
                   <option value="<?php echo $group['group_level'];?>"><?php echo ucwords($group['group_name']);?></option>
                <?php endforeach;?>
                </select>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
        <?php include_once('layouts/footer.php'); ?>