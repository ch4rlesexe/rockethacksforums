<?php
session_start();

$ini_array = parse_ini_file("myproperties.ini", true);

$servername = $ini_array['DB']['DBHOST'];
$username = $ini_array['DB']['DBUSER'];
$password = $ini_array['DB']['DBPASS'];
$dbname = $ini_array['DB']['DBNAME'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT id, name, password, role FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role']; 
        
        header('Location: dashboard.php');
        exit();
    } else {
        header('Location: login.php?error=1');
        exit();
    }
} else {
    header('Location: login.php?error=1');
    exit();
}

$stmt->close();
$conn->close();
?>
