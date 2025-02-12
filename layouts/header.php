<?php $user = current_user(); 
      // $notifications = get_user_notifications($user['id']);

require_once('includes/load.php');
      
$query = "
SELECT 
    products.name, 
    products.quantity, 
    categories.threshold 
FROM products 
INNER JOIN categories 
    ON products.categorie_id = categories.id 
WHERE products.quantity <= categories.threshold
";

$result = $db->query($query);

$notification_count = $result->num_rows;      
?>
      <!DOCTYPE html>
      <html lang="en">

      <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php if (!empty($page_title))
        echo remove_junk($page_title);
        elseif (!empty($user))
          echo ucfirst($user['name']);
        else echo "Inventory Management System"; ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/main.css">
        <style>
          .site__header-frame {
            display: flex;
            align-items: center;
            justify-content: flex-end;
          }

          .notification_dropdown-btn {
            font-size: 0.9rem;
            width: 30px;
            height: 30px;
            background-color: #00435B;
            color: #fff;
            align-items: center;
            justify-content: center;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            border-radius: 50%;
          }

/* Notification Dropdown */
.notification_dropdown {
  position: relative;
  margin-right: 20px; /* Space between notifications and profile */
}

.notification_dropdown .notification-count {
  position: absolute;
  top: -5px;
  right: -5px;
  background-color: red;
  color: white;
  font-size: 10px;
  padding: 2px 5px;
  border-radius: 50%;
}

.notification_dropdown-list {
  display: none;
  list-style: none;
  position: absolute;
  right: 0;
  top: 40px; /* Adjust to align dropdown */
  background-color: white;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 300px;
  padding: 0;
  z-index: 1000;
  border-radius: 10px;
}

.notification_dropdown-list.active {
  display: block;
}


.notification_item {

  padding: 5px 25px;
  border-bottom: 0.5px solid #dedede;
}

.notification_item:last-child {
  border-bottom: none;
}

.notification_item:hover {
  background-color: #f5f5f5;
}

</style>
</head>

<body>
  <?php if ($session->isUserLoggedIn(true)): ?>
    <div id="wrapper">
      <header class="site__header">
        <div class="site__logo">
          <a href="admin.php"><span class="logo_bold">IMS</span></a>
        </div>
        <div class="site__header-frame">
          <div class="notification_dropdown">
           <div onclick="toggle()" class="notification_dropdown-btn">
            <i class="icon-notification"></i>
            <span class="notification-count" id = "notification_count"><?php echo $notification_count; ?></span>
          </div>
          
          <ul class="notification_dropdown-list" id = "notification_dropdown">
          <?php
          // Assuming $result is the result of your query
          while ($row = mysqli_fetch_assoc($result)) {
              echo "<li class = 'notification_item'>" . $row['name'] . " is below the threshold. Current Quantity: " . $row['quantity'] . "</li>";
          }
          ?>
          </ul>
        </div>
        <div class="profile-dropdown">
          <div onclick="toggle()" class="profile-dropdown-btn">
            <div class="profile-img">
              <img src="uploads/users/<?php echo !empty($user['image']) ? $user['image'] : 'default.jpg'; ?>" alt="Profile Image" />
            </div>
          </div>
          <ul class="profile-dropdown-list">
            <li class="profile-dropdown-list-item">
              <a href="edit_user.php?id=<?php echo (int)$user['id']; ?>">
                <i class="icon-user"></i>
                Profile
              </a>
            </li>
            <li class="profile-dropdown-list-item">
              <a href="edit_account.php" title="edit account">
                <i class="icon-settings"></i>
                Settings
              </a>
            </li>
            <hr />
            <li class="profile-dropdown-list-item">
              <a href="logout.php">
                <i class="icon-logout"></i>
                Log out
              </a>
            </li>
          </ul>
        </div>
      </div>
    </header>
    <main id="site__main" class="site__main">
      <div class="sidebar worksidebar">
        <?php if ($user['user_level'] === '1'): ?>
          <!-- admin menu -->
          <?php include_once('admin_menu.php'); ?>
        <?php elseif ($user['user_level'] === '2'): ?>
          <!-- Special user -->
          <?php include_once('special_menu.php'); ?>

        <?php elseif ($user['user_level'] === '3'): ?>
          <!-- User menu -->
          <?php include_once('user_menu.php'); ?>

        <?php endif; ?>

      </div>
    <?php endif; ?>

    <section class="workboard">