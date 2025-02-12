<?php
ob_start();
require_once('includes/load.php');
if ($session->isUserLoggedIn(true)) {
  redirect( 'home.php', false);
}
?>
<?php include_once('layouts/header.php'); ?>


<div class="tpl--access">
  <main class="site__main">
    <?php echo display_msg($msg); ?>
    <div class="access__portal">
      <div class="main__logo">
        <img src="images/logo.svg" alt=" logo" width="155" height="60" onerror="this.onerror=null; this.src='images/logo.png'">
      </div>
      <?php echo display_msg($msg); ?>
      <form class="general--form access__form login__form" method="post" action="auth.php">
        <div class="form__module">
          <div class="form__set">
            <input type="text" id="logEmail" name="username" placeholder="Username">
          </div>
        </div>
        <div class="form__module">
          <div class="form__set">
            <input type="password" id="logPassword" name="password" placeholder="password">
          </div>
        </div>
        <div class="form__rufp">
          <div class="form__remb">
            <input id="rembUser" type="checkbox" name="rembInfo" value="remberUser" checked>
            <label for="rembUser">Remember me</label>
          </div>
          <div class="form_nav"><a href="fget.html">Forgot password?</a></div>
        </div>
        <ul class="form__action">
          <li><input type="submit" class="button primary-tint" value="Login"></li>
        </ul>
      </form>
    </div>
  </main>
</div>

<?php include_once('layouts/footer.php'); ?>