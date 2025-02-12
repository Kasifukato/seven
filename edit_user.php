<?php
$page_title = 'Edit User';
require_once('includes/load.php');
page_require_level(1);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  $session->msg("d", "Invalid user ID.");
  redirect('users.php');
}

$e_user = find_by_id('users', (int)$_GET['id']);
$groups = find_all('user_groups');
if (!$e_user) {
  $session->msg("d", "Missing user ID.");
  redirect('users.php');
}

// Update User basic info
if (isset($_POST['update'])) {
  $req_fields = array('name', 'username', 'level');
  validate_fields($req_fields);

  if (empty($errors)) {
    $id = (int)$e_user['id'];
    $name = remove_junk($db->escape($_POST['name']));
    $username = remove_junk($db->escape($_POST['username']));
    $level = (int)$db->escape($_POST['level']);
    $status = remove_junk($db->escape($_POST['status']));

    $sql = "UPDATE users SET name = ?, username = ?, user_level = ?, status = ? WHERE id = ?";
    $stmt = $db->con->prepare($sql);
    $stmt->bind_param("ssiii", $name, $username, $level, $status, $id);

    if ($stmt->execute()) {
      $session->msg('s', "Account Updated");
      redirect('edit_user.php?id=' . (int)$e_user['id'], false);
    } else {
      error_log("Database Error: " . $db->con->error);
      $session->msg('d', 'Failed to update account!');
    }
  } else {
    foreach ($errors as $error) {
      echo "<p style='color: red;'>$error</p>";
    }
  }
}

// Update user password
if (isset($_POST['update-pass'])) {
  $req_fields = array('password');
  validate_fields($req_fields);

  if (empty($errors)) {
    $id = (int)$e_user['id'];
    $password = remove_junk($db->escape($_POST['password']));
    $h_pass = password_hash($password, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $db->con->prepare($sql);
    $stmt->bind_param("si", $h_pass, $id);

    if ($stmt->execute()) {
      $session->msg('s', "User password has been updated");
      redirect('edit_user.php?id=' . (int)$e_user['id'], false);
    } else {
      error_log("Database Error: " . $db->con->error);
      $session->msg('d', 'Failed to update password!');
    }
  } else {
    foreach ($errors as $error) {
      echo "<p style='color: red;'>$error</p>";
    }
  }
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col xs-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="workboard__heading">
  <h1 class="workboard__title">Update <?php echo remove_junk(ucwords($e_user['name'])); ?> Account</h1>
</div>
<div class="workpanel profile__main adm-dashboard__main">
  <div class="row">
    <!-- User Details Form -->
    <div class="col xs-12 sm-6">
      <form class="general--form access__form" method="post" action="edit_user.php?id=<?php echo (int)$e_user['id']; ?>" class="clearfix">
        <div class="row">
          <!-- Name Field -->
          <div class="col xs-12 sm-6">
            <div class="form__module">
              <label for="name" class="form__label">Name</label>
              <div class="form__set">
                <input 
                type="text" 
                id="name" 
                name="name" 
                placeholder="Full Name (e.g., John Doe)" 
                value="<?php echo remove_junk(ucwords($e_user['name'])); ?>" 
                required>
              </div>
            </div>
          </div>
          <!-- Username Field -->
          <div class="col xs-12 sm-6">
            <div class="form__module">
              <label for="username" class="form__label">Username</label>
              <div class="form__set">
                <input 
                type="text" 
                id="username" 
                name="username" 
                placeholder="Username (e.g., johndoe123)" 
                value="<?php echo remove_junk(ucwords($e_user['username'])); ?>" 
                required>
              </div>
            </div>
          </div>
          <!-- User Role Field -->
          <div class="col xs-12 sm-6">
            <div class="form__module">
              <label for="level" class="form__label">User Role</label>
              <select class="form-control" id="level" name="level" required>
                <?php foreach ($groups as $group) : ?>
                  <option 
                  value="<?php echo $group['group_level']; ?>" 
                  <?php if ($group['group_level'] === $e_user['user_level']) echo 'selected'; ?>>
                  <?php echo ucwords($group['group_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <!-- Status Field -->
        <div class="col xs-12 sm-6">
          <div class="form__module">
            <label for="status" class="form__label">Status</label>
            <select class="form-control" id="status" name="status" required>
              <option value="1" <?php if ($e_user['status'] === '1') echo 'selected'; ?>>Active</option>
              <option value="0" <?php if ($e_user['status'] === '0') echo 'selected'; ?>>Deactive</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col xs-12 sm-5">
          <div class="site-panel">
            <div class="form__action">
              <input type="submit" class="button primary-tint" name="update" value="Update">
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="row">
  <!-- User Details Form -->
  <div class="col xs-12">
    <div class="recent-orders">
      <h2 class="subheading">Change <?php echo remove_junk(ucwords($e_user['name'])); ?> password</h2>
    </div>
  </div>
  <div class="col xs-12 sm-6">
    <form class="general--form access__form" action="edit_user.php?id=<?php echo (int)$e_user['id'];?>" method="post">
      <div class="row">
        <!-- Name Field -->
        <div class="col xs-12 sm-6">
          <div class="form__module">
            <label class="form__label"for="password" >Password</label>
            <div class="form__set">
              <input type="password" name="password" placeholder="User's new password">
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col xs-12 sm-5">
          <div class="site-panel">
            <div class="form__action">
              <input type="submit" class="button primary-tint" name="update-pass" value="Change">
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
</div>


<?php include_once('layouts/footer.php'); ?>
