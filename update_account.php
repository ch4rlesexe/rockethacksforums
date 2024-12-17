<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$config = parse_ini_file('myproperties.ini');
$servername = $config['DBHOST'];
$username = $config['DBUSER'];
$password = $config['DBPASS'];
$dbname = $config['DBNAME'];

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

$logged_in_user_id = $_SESSION['user_id'];
$new_name = $_POST['name'] ?? '';
$new_email = $_POST['email'] ?? '';
$new_password = $_POST['password'] ?? '';

$update_query = "UPDATE users SET name = ?, email = ?";
$params = [$new_name, $new_email];
$param_types = "ss";

if (!empty($new_password)) {
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $update_query .= ", password = ?";
    $params[] = $hashed_password;
    $param_types .= "s";
}

$update_query .= " WHERE id = ?";
$params[] = $logged_in_user_id;
$param_types .= "i";

$stmt = $conn->prepare($update_query);
$stmt->bind_param($param_types, ...$params);

if ($stmt->execute()) {
    echo "<script>alert('Account updated successfully.'); window.location.href = 'dashboard.php';</script>";
} else {
    echo "<script>alert('Error updating account: " . htmlspecialchars($stmt->error) . "'); history.back();</script>";
}

$stmt->close();
$conn->close();
?>
