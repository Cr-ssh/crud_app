<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .page-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 80px);
            padding: 20px;
            box-sizing: border-box;
        }

        .container {
            max-width: 900px;
            width: 100%;
            padding: 30px;
            background-color: #000;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
            text-align: center;
        }

        .header img {
            height: 80px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 1.8rem;
            color: #fff;
            margin-bottom: 20px;
        }

        h2 {
            color: #fff;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }

        nav ul li {
            margin: 10px 0;
        }

        nav ul li a {
            text-decoration: none;
            color: #b30000;
            font-weight: bold;
            font-size: 1.1rem;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        .dashboard-widgets {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .widget-card {
            background-color: #111;
            padding: 20px;
            margin: 10px;
            border-radius: 10px;
            width: 200px;
            box-shadow: 0 2px 10px rgba(255, 255, 255, 0.1);
            font-weight: bold;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="container">
            <div class="header">
                <img src="img/KRA_Logo.png" alt="KRA Logo">
                <h1>Kenya Revenue Authority Portal</h1>
            </div>

            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>!</h2>

            <div class="dashboard-widgets">
                <div class="widget-card">Total Taxpayers: 124</div>
                <div class="widget-card">Tax Returns Today: 36</div>
                <div class="widget-card">Last Login: <?php echo date("F j, Y, g:i a"); ?></div>
            </div>

            <nav>
                <ul>
                    <li><a href="records.php">Manage Records</a></li>
                    <li><a href="tax_calculator.php">Tax Calculator</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </div>
</body>
</html>
