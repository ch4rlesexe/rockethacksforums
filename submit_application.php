<?php
session_start();

$ini_array = parse_ini_file("myproperties.ini", true);

$servername = $ini_array['DB']['DBHOST'];
$username = $ini_array['DB']['DBUSER'];
$password = $ini_array['DB']['DBPASS'];
$dbname = $ini_array['DB']['DBNAME'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$submission_deadline = "2024-12-31 23:59:59";

if (new DateTime() > new DateTime($submission_deadline)) {
    die("<div class='form-container'><p>Application submissions are closed as of $submission_deadline.</p></div>");
}

$check_query = "SELECT id FROM applications WHERE user_id = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    die("<div class='form-container'><p>You have already submitted an application.</p></div>");
}

$question1 = htmlspecialchars($_POST['question1']);
$question2 = htmlspecialchars($_POST['question2']);

// File uploads
$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$file_name = basename($_FILES['file-upload']['name']);
$target_file = $upload_dir . uniqid() . '-' . $file_name;
$file_uploaded = false;

if ($file_name) {
    if (move_uploaded_file($_FILES['file-upload']['tmp_name'], $target_file)) {
        chmod($target_file, 0644); 
        $file_uploaded = true;
    } else {
        echo "Error uploading file. Please check directory permissions.";
        exit();
    }
}

$sql = "INSERT INTO applications (user_id, question1, question2, file_path, status, submitted_at) 
        VALUES (?, ?, ?, ?, 'Submitted', NOW())";
$stmt = $conn->prepare($sql);
$file_path = $file_uploaded ? $target_file : null;
$stmt->bind_param('isss', $user_id, $question1, $question2, $file_path);

if ($stmt->execute()) {
    header('Location: dashboard.php?status=submitted');
    exit();
} else {
    echo "Error: " . htmlspecialchars($stmt->error);
}

$stmt->close();
$conn->close();
?>
