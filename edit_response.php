<?php
$config = parse_ini_file('myproperties.ini');

if (!isset($config['DBHOST'], $config['DBUSER'], $config['DBPASS'], $config['DBNAME'])) {
    die("Error: Missing database configuration in myproperties.ini");
}

$servername = $config['DBHOST'];
$username = $config['DBUSER'];
$password = $config['DBPASS'];
$dbname = $config['DBNAME'];

session_start();
$logged_in_user_id = $_SESSION['user_id'] ?? null;

if (!$logged_in_user_id) {
    echo "<div class='form-container'><p>Error: User not logged in.</p></div>";
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<div class='form-container'><p>Connection failed: " . htmlspecialchars($conn->connect_error) . "</p></div>");
}

$response_id = $_GET['id'] ?? null;
if (!$response_id) {
    die("<div class='form-container'><p>Invalid request parameters.</p></div>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_question1 = $_POST['question1'] ?? '';
    $updated_question2 = $_POST['question2'] ?? '';
    $new_file_path = null;

    if (!empty($_FILES['file']['name'])) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = basename($_FILES['file']['name']);
        $target_file = $upload_dir . uniqid() . '-' . $file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            chmod($target_file, 0644);
            $new_file_path = $target_file;
        } else {
            echo "<div class='form-container'><p>Error uploading file. Please check directory permissions.</p></div>";
            exit;
        }
    }

    $update_query = "UPDATE applications SET question1 = ?, question2 = ?";
    $params = ["ssi", $updated_question1, $updated_question2, $response_id, $logged_in_user_id];

    if ($new_file_path) {
        $update_query .= ", file_path = ?";
        $params[0] = "sssi";
        $params[] = $new_file_path;
    }

    $update_query .= " WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param(...$params);

    if ($stmt->execute()) {
        echo "<div class='form-container'><p>Application updated successfully.</p></div>";
    } else {
        echo "<div class='form-container'><p>Error updating application: " . htmlspecialchars($stmt->error) . "</p></div>";
    }
    $stmt->close();
}

$select_query = "SELECT question1, question2, file_path FROM applications WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($select_query);
$stmt->bind_param("ii", $response_id, $logged_in_user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Application</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 400px;
            margin: 20px;
        }

        label, textarea, input {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        textarea, input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            resize: vertical;
        }

        .btn {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: block;
            width: 100%;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Your Application</h2>
        <form method="post" enctype="multipart/form-data">
            <label for="question1">Edit Question 1:</label>
            <textarea id="question1" name="question1" required><?= htmlspecialchars($data['question1']) ?></textarea>

            <label for="question2">Edit Question 2:</label>
            <textarea id="question2" name="question2" required><?= htmlspecialchars($data['question2']) ?></textarea>

            <label for="file">Upload New File (Optional):</label>
            <?php if (!empty($data['file_path'])): ?>
                <p>Current File: <a href="<?= htmlspecialchars($data['file_path']) ?>" target="_blank">View File</a></p>
            <?php endif; ?>
            <input type="file" id="file" name="file">

            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>
</body>
</html>
