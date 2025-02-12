<?php 
include_once('includes/load.php');

// Check if required fields are submitted
$req_fields = array('username', 'password', 'role');
validate_fields($req_fields);

$username = trim(remove_junk($_POST['username']));
$password = trim(remove_junk($_POST['password']));
$selected_role = ucfirst(strtolower($_POST['role'])); // Normalize role

if (empty($errors)) {
    // Fetch user details from the database using $db->query()
    $sql = "SELECT * FROM users WHERE username = '{$username}' LIMIT 1";
    $result = $db->query($sql);
    $user = $db->fetch_assoc($result);

    if (!$user) {
        $session->msg("d", "Invalid Username or Password.");
        redirect('index.php', false);
    }

    // Hash the submitted password with SHA1 to match stored hash
    $submitted_password = sha1($password);

    // Compare the hashed passwords
    if ($submitted_password !== $user['password']) {
        $session->msg("d", "Invalid Username or Password.");
        redirect('index.php', false);
    }

    // Check if selected role matches the database role
    $db_role = ucfirst(strtolower($user['role'])); // Normalize role from the database

    if ($selected_role !== $db_role) {
        $session->msg("d", "Incorrect Role selected. Please select the correct role.");
        redirect('index.php', false);
    }

    // Login User
    $session->login($user['id']);
    updateLastLogIn($user['id']);

    // Store session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $db_role; 

    // If the user is a supplier, fetch supplier_id
    if ($db_role == "Supplier") {
        $supplier_query = "SELECT id FROM suppliers WHERE name = '{$user['name']}' LIMIT 1";
        $supplier_result = $db->query($supplier_query);
        $supplier = $db->fetch_assoc($supplier_result);

        if ($supplier) {
            $_SESSION['supplier_id'] = $supplier['id'];
        } else {
            $_SESSION['supplier_id'] = null;
        }
    } else {
        $_SESSION['supplier_id'] = null;
    }

    // Redirect Based on Role
    if ($db_role == "Supplier") {
        redirect('sdashboard.php', false);
    } else {
        redirect('admin.php', false);
    }
} else {
    $session->msg("d", $errors);
    redirect('index.php', false);
}
?>