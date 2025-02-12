<?php
$page_title = 'Edit Account';
require_once('includes/load.php');
page_require_level(4);

// Update user image
if (isset($_POST['submit'])) {
    // Check if file is uploaded
    if ($_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
        $photo = new Media();
        $user_id = (int)$_POST['user_id'];
        $photo->upload($_FILES['file_upload']);
        
        if ($photo->process_user($user_id)) {
            $session->msg('s', 'Photo has been uploaded.');
            redirect('edit_account.php');
        } else {
            // Debug file upload
            var_dump($_FILES['file_upload'], $photo->errors);
            exit;
        }
    } else {
        // Handle file upload error
        $session->msg('d', 'Error uploading file: ' . $_FILES['file_upload']['error']);
        redirect('edit_account.php');
    }
}

// Update user other info
if (isset($_POST['update'])) {
    $req_fields = array('name', 'username');
    validate_fields($req_fields);
    
    if (empty($errors)) {
        $id = (int)$_SESSION['user_id'];
        $name = remove_junk($db->escape($_POST['name']));
        $username = remove_junk($db->escape($_POST['username']));
        
        $sql = "UPDATE users SET name = '{$name}', username = '{$username}' WHERE id = '{$id}'";
        $result = $db->query($sql);
        
        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', "Account updated.");
            redirect('edit_account.php', false);
        } else {
            $session->msg('d', 'Sorry, failed to update!');
            redirect('edit_account.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_account.php', false);
    }
}

// Reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    header("Location: change_password.php");
    exit;
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="workboard__heading">
  <h1 class="workboard__title">Account Setting</h1>
</div>

<div class="workpanel profile__main">
  <div class="row">
    <div class="col xs-12 sm-9">
      <form class="general--form access__form" method="POST" action="edit_account.php?id=<?php echo (int)$user['id'];?>" class="clearfix">
        <div class="row">
          <div class="col xs-12 sm-4">
            <div class="form__module">
              <label for="name" class="form__label">Name</label>
              <div class="form__set">
                <input type="text" id="name" placeholder="Name" name="name" value="<?php echo remove_junk(ucwords($user['name'])); ?>">
              </div>
            </div>
          </div>
          <div class="col xs-12 sm-4">
            <div class="form__module">
              <label for="Username" class="form__label">Username</label>
              <div class="form__set">
                <input type="tel" id="Username" placeholder="Username" name="username" value="<?php echo remove_junk(ucwords($user['username'])); ?>">
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col xs-12 sm-5">
            <div class="site-panel">
              <div class="form__action">
                <input type="submit" class="button tertiary-line" value="Reset Password" name="reset">
              </div>
              <div class="form__action">
                <input type="submit" class="button primary-tint" name="update" value="Update">
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="col xs-12 sm-3">
      <div class="profile__img">
        <div class="profile--img">
          <img class="img-circle img-size-2" src="uploads/users/<?php echo $user['image'];?>" alt="">
        </div>

        <form class="general--form access__form" action="edit_account.php" method="POST" enctype="multipart/form-data">
          <div class="upload">
            <label for="file-upload" class="primary-line">Choose File</label>
            <input type="file" name="file_upload" id="file-upload">
            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
            <input type="submit" name="submit" class="primary-tint" value="Upload">
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
