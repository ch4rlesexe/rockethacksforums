<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #0b0f19;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }

        .form-container {
            background-color: #1c2230;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            width: 400px;
            animation: fadeIn 1.5s ease-out;
        }

        h2 {
            text-align: center;
            color: #4a90e2;
            margin-bottom: 20px;
            font-size: 2rem;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #ccc;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #444;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #2b3443;
            color: #fff;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
            background-color: #28a745;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .error-message {
            color: #ff4d4f;
            text-align: center;
            margin-bottom: 10px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Sign Up</h2>
        <?php
        $config = parse_ini_file('myproperties.ini');

        if (!isset($config['DBHOST'], $config['DBUSER'], $config['DBPASS'], $config['DBNAME'])) {
            die("Error: Missing database configuration in myproperties.ini");
        }

        $servername = $config['DBHOST'];
        $username = $config['DBUSER'];
        $password = $config['DBPASS'];
        $dbname = $config['DBNAME'];

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        session_start();

        $error_message = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
                $error_message = "All fields are required.";
            } elseif ($password !== $confirm_password) {
                $error_message = "Passwords do not match.";
            } else {
                $check_email_query = "SELECT id FROM users WHERE email = ?";
                $stmt = $conn->prepare($check_email_query);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error_message = "Email is already registered.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $insert_query = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())";
                    $stmt = $conn->prepare($insert_query);
                    $stmt->bind_param("sss", $name, $email, $hashed_password);

                    if ($stmt->execute()) {
                        $_SESSION['user_id'] = $stmt->insert_id;
                        $_SESSION['user_name'] = $name;
                        $_SESSION['application_status'] = "Not Started";
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        $error_message = "Error: " . htmlspecialchars($stmt->error);
                    }
                }
                $stmt->close();
            }
        }
        $conn->close();
        ?>

        <?php if (!empty($error_message)): ?>
            <div class="error-message">Error: <?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit" class="btn">Sign Up</button>
        </form>
    </div>
</body>
</html>
