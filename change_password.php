<?php
$page_title = 'Change Password';
require_once('includes/load.php');
  // Checkin What level user has permission to view this page
page_require_level(3);
?>
<?php $user = current_user(); ?>
<?php
if(isset($_POST['update'])){

  $req_fields = array('new-password','old-password','id' );
  validate_fields($req_fields);

  if(empty($errors)){

   if(sha1($_POST['old-password']) !== current_user()['password'] ){
     $session->msg('d', "Your old password not match");
     redirect('change_password.php',false);
   }

   $id = (int)$_POST['id'];
   $new = remove_junk($db->escape(sha1($_POST['new-password'])));
   $sql = "UPDATE users SET password ='{$new}' WHERE id='{$db->escape($id)}'";
   $result = $db->query($sql);
   if($result && $db->affected_rows() === 1):
    $session->logout();
    $session->msg('s',"Login with your new password.");
    redirect('index.php', false);
  else:
    $session->msg('d',' Sorry failed to updated!');
    redirect('change_password.php', false);
  endif;
} else {
  $session->msg("d", $errors);
  redirect('change_password.php',false);
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IMS-Reset</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <div id="wrapper">
<div class="tpl--access">
  <main class="site__main">
    <div class="access__portal">
      <div class="access__form-title">
        <h1 class="form-heading">Create A Strong Password</h1>
        <h2 class="sub-heading">Your password must be at least 6 characters and should include a combination of numbers, letters</h2>
      </div>
      <form class="general--form access__form login__form" method="POST" action="change_password.php">
        <div class="form__module">
          <div class="form__set">
            <input type="password" id="CPassword" placeholder="Old Password" name="old-password">
          </div>
        </div>
        <div class="form__module">
          <div class="form__set">
            <input type="password" id="NPassword" placeholder="New Password" name="new-password">
          </div>
        </div>
        <div class="form__action">
          <input type="hidden"  name="id" value="<?php echo (int)$user['id'];?>">
          <input type="submit" name="update" class="button primary-tint" value="Reset">
        </form>
      </div>
    </main>
  </div>
    </div>
</body>
</html>