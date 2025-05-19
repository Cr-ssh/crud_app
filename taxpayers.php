<?php
include 'php/config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Taxpayer Management</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <img src="img/kra-logo.png" alt="KRA Logo" height="60">
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="taxpayers.php">Taxpayer Management</a>
            <a href="tax_calculator.php">Tax Calculator</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <h1>Manage Taxpayers</h1>
    <!-- Include your CRUD table and form here -->
    <?php include 'manage_taxpayers.php'; ?>
</body>
</html>
