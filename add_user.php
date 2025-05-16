<?php
include 'php/config.php'; // adjust this path if needed

$username = 'admin'; // change this to the username you want
$password = 'admin123'; // change this to the password you want

// Hash the password before storing it
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $hashed_password);

if ($stmt->execute()) {
    echo "User added successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
