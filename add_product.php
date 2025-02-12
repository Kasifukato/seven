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

        // Check if username already exists (case-insensitive)
        $username_check = "SELECT * FROM users WHERE LOWER(username) = LOWER('{$username}')";
        $result = $db->query($username_check);
        if ($result->num_rows > 0) {
            $msg = "Username already exists. Please choose a different username.";
            $session->msg('d', $msg);
            redirect('add_user.php', false);
            exit;
        }

        // If user role is supplier (level 4), perform additional checks
        if ($user_level == 4) {
            // Check if user with this name already exists as a supplier user
            $existing_supplier_user = "SELECT * FROM users WHERE LOWER(name) = LOWER('{$name}') AND user_level = 4";
            $user_result = $db->query($existing_supplier_user);
            if ($user_result->num_rows > 0) {
                $msg = "A supplier user account already exists with this name: " . $name;
                $session->msg('d', $msg);
                redirect('add_user.php', false);
                exit;
            }

            // Check if supplier exists in suppliers table (case-insensitive)
            $supplier_check = "SELECT * FROM suppliers WHERE LOWER(name) = LOWER('{$name}')";
            $result = $db->query($supplier_check);
            if ($result->num_rows == 0) {
                $msg = 'Supplier "' . $name . '" does not exist in the suppliers database. Please add supplier first.';
                $session->msg('d', $msg);
                redirect('add_user.php', false);
                exit;
            }
        }

        $password = sha1($password);
        
        // Set current timestamp for last_login
        $current_time = date('Y-m-d H:i:s');

        $query = "INSERT INTO users (";
        $query .= "name, username, password, user_level, status, last_login, role";
        $query .= ") VALUES (";
        $query .= "'{$name}', '{$username}', '{$password}', '{$user_level}', '1', '{$current_time}', ";
        
        // Set role based on user_level
        switch($user_level) {
            case 1:
                $query .= "'Admin'";
                break;
            case 2:
                $query .= "'Special'";
                break;
            case 3:
                $query .= "'User'";
                break;
            case 4:
                $query .= "'Supplier'";
                break;
            default:
                $query .= "'User'";
        }
        
        $query .= ")";

        if ($db->query($query)) {
            // Success
            $msg = "User account has been created!";
            $session->msg('s', $msg);
            redirect('add_user.php', false);
        } else {
            // Failed
            $msg = 'Sorry, failed to create account!';
            $session->msg('d', $msg);
            redirect('add_user.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('add_user.php', false);
    }
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>
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
                                    <input type="text" class="form-control" name="full-name" id="name" placeholder="Name">
                                </div>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="username" class="form__label">Username</label>
                                <div class="form__set">
                                    <input type="text" class="form-control" name="username" id="username" placeholder="Username">
                                </div>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="password" class="form__label">Password</label>
                                <div class="form__set">
                                    <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                                </div>
                            </div>
                        </div>
                        <div class="col xs-12 sm-3">
                            <div class="form__module">
                                <label for="level" class="form__label">User Role</label>
                                <div class="form__set">
                                    <select class="form-control" name="level" id="level">
                                        <?php foreach ($groups as $group): ?>
                                            <option value="<?php echo $group['group_level']; ?>">
                                                <?php echo ucwords($group['group_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
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