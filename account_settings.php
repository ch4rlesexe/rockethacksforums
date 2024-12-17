<?php
session_start();

$ini_array = parse_ini_file("myproperties.ini", true);
$servername = $ini_array['DB']['DBHOST'] ?? 'localhost';
$username = $ini_array['DB']['DBUSER'] ?? '';
$password = $ini_array['DB']['DBPASS'] ?? '';
$dbname = $ini_array['DB']['DBNAME'] ?? '';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = '';
$user_email = '';

$query = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $user_name = $row['name'];
    $user_email = $row['email'];
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['name'] ?? '';
    $new_email = $_POST['email'] ?? '';
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($new_password) && $new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
    } else {
        $update_query = "UPDATE users SET name = ?, email = ?";
        $params = [$new_name, $new_email];
        $types = "ss";

        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_query .= ", password = ?";
            $params[] = $hashed_password;
            $types .= "s";
        }

        $update_query .= " WHERE id = ?";
        $params[] = $user_id;
        $types .= "i";

        $stmt = $conn->prepare($update_query);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo "<script>alert('Account updated successfully.'); window.location.href = 'dashboard.php';</script>";
        } else {
            echo "<script>alert('Error updating account: " . htmlspecialchars($stmt->error) . "');</script>";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
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
            width: 400px;
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

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #444;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #1e2433;
            color: #fff;
        }

        input::placeholder {
            color: #bbb;
        }

        .btn {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            margin-bottom: 10px;
            background-color: #007bff;
            color: #fff;
            font-size: 1em;
            font-weight: bold;
            text-align: center;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .btn.logout {
            background-color: #dc3545;
        }

        .btn.logout:hover {
            background-color: #c82333;
        }

        .btn.secondary {
            background-color: #6c757d;
        }

        .btn.secondary:hover {
            background-color: #5a6268;
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
        <h2>Account Settings</h2>
        <form method="POST" action="account_settings.php">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user_name) ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_email) ?>" required>

            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current">

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter new password">

            <button type="submit" class="btn">Save Changes</button>
        </form>
        <a href="dashboard.php" class="btn secondary">Back to Dashboard</a>
        <a href="logout.php" class="btn logout">Log Out</a>
    </div>
</body>
</html>
