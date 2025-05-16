<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        // Verify password using password_verify
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            header('Location: ../pages/dashboard.php');
            exit;
        } else {
            $error = "Incorrect username or password.";
        }
    } else {
        $error = "Incorrect username or password.";
    }
    $stmt->close();
    $conn->close();
} else {
    header('Location: ../pages/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login Error</title>
</head>
<body>
    <p><?= htmlspecialchars($error ?? 'An error occurred') ?></p>
    <p><a href="../pages/login.php">Go back to login</a></p>
</body>
</html>
