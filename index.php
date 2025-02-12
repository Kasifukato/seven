<?php
ob_start();
require_once('includes/load.php');
if ($session->isUserLoggedIn(true)) {
  redirect('home.php', false);
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="tpl--access">
  <main class="site__main">
     <div class="access__portal">
      <div class="main__logo">
        <img src="images/logo.svg" alt="logo" width="155" height="60" onerror="this.onerror=null; this.src='images/logo.png'">
      </div>
      <?php echo display_msg($msg); ?>
      <form class="general--form access__form login__form" method="post" action="auth.php">
    <div class="form__module">
        <div class="form__set">
            <input type="text" id="logEmail" name="username" placeholder="Username" required>
        </div>
    </div>
    <div class="form__module">
        <div class="form__set">
            <input type="password" id="logPassword" name="password" placeholder="Password" required>
        </div>
    </div>
    <div class="form__module">
        <div class="form__set">
            <select id="userRole" name="role" required>
                <option value="Admin">Admin</option>
                <option value="special">Special</option>
                <option value="User">User</option>
                <option value="Supplier">Supplier</option>
            </select>
        </div>
    </div>
    <ul class="form__action">
        <li><input type="submit" class="button primary-tint" value="Login"></li>
    </ul>
</form>

    </div>
  </main>
</div>

<?php include_once('layouts/footer.php'); ?>
