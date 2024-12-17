<?php
ob_start();

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
    die("<div class='form-container'><p>Error: User not logged in.</p></div>");
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<div class='form-container'><p>Connection failed: " . htmlspecialchars($conn->connect_error) . "</p></div>");
}

$response_id = $_GET['id'] ?? null;
if (!$response_id) {
    die("<div class='form-container'><p>Invalid application ID.</p></div>");
}

$redirect_page = 'user_application.php';

$check_ownership_query = "SELECT question1, question2, file_path FROM applications WHERE id = ? AND user_id = ?";
$ownership_stmt = $conn->prepare($check_ownership_query);
$ownership_stmt->bind_param("ii", $response_id, $logged_in_user_id);
$ownership_stmt->execute();
$result = $ownership_stmt->get_result();

if ($result->num_rows === 0) {
    die("<div class='form-container'><p>Error: Unauthorized access. You cannot edit this application.</p></div>");
}

$data = $result->fetch_assoc();
$ownership_stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_question1 = $_POST['question1'] ?? '';
    $updated_question2 = $_POST['question2'] ?? '';
    $new_file_path = null;

    if (!empty($_FILES['file-upload']['name'])) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $file_name = uniqid() . '-' . basename($_FILES['file-upload']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['file-upload']['tmp_name'], $target_file)) {
            chmod($target_file, 0644);
            $new_file_path = $target_file;
        } else {
            die("<div class='form-container'><p>Error uploading file.</p></div>");
        }
    }

    $update_fields = "question1 = ?, question2 = ?";
    $params = ["ssi", $updated_question1, $updated_question2, $response_id];

    if ($new_file_path) {
        $update_fields .= ", file_path = ?";
        $params[0] = "sssi";
        $params[] = $new_file_path;
    }

    $update_query = "UPDATE applications SET $update_fields WHERE id = ? AND user_id = ?";
    $params[] = $logged_in_user_id;
    $params[0] .= "i";

    $stmt = $conn->prepare($update_query);
    $stmt->bind_param(...$params);

    if ($stmt->execute()) {
        header("Location: $redirect_page");
        exit;
    } else {
        echo "<div class='form-container'><p>Error updating application: " . htmlspecialchars($stmt->error) . "</p></div>";
    }
    $stmt->close();
}

$conn->close();
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Application</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #0b0f19;
            color: #ffffff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .stars {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://www.transparenttextures.com/patterns/stardust.png');
            animation: twinkle 3s infinite alternate;
            z-index: 0;
        }

        @keyframes twinkle {
            0% { opacity: 0.8; }
            100% { opacity: 1; }
        }

        .form-container {
            background-color: rgba(28, 34, 48, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            width: 500px;
            position: relative;
            z-index: 1;
            animation: fadeIn 1.5s ease-out;
        }

        h2 {
            text-align: center;
            color: #4a90e2;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #444;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #1e2433;
            color: #fff;
        }

        input::placeholder, textarea::placeholder {
            color: #bbb;
        }

        .btn {
            background-color: #007bff;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            width: 100%;
            text-align: center;
            margin-top: 10px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-decoration: none;
            display: inline-block;
            box-sizing: border-box;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="stars"></div>

    <div class="form-container">
        <h2>Edit Application</h2>
        <form method="post" enctype="multipart/form-data">
            <label for="question1">Question 1:</label>
            <input type="text" id="question1" name="question1" value="<?= htmlspecialchars($data['question1']) ?>" required>

            <label for="question2">Question 2:</label>
            <textarea id="question2" name="question2" required><?= htmlspecialchars($data['question2']) ?></textarea>

            <label for="file-upload">Upload New File (Optional):</label>
            <?php if (!empty($data['file_path'])): ?>
                <p>Current File: <a href="<?= htmlspecialchars($data['file_path']) ?>" target="_blank">View File</a></p>
            <?php endif; ?>
            <input type="file" id="file-upload" name="file-upload">

            <button type="submit" class="btn">Save Changes</button>
        </form>
        <a href="user_application.php" class="btn">Back to Applications</a>
    </div>
</body>
</html>
