<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to safely count rows
function getCount($conn, $query) {
    $result = $conn->query($query);
    if (!$result) {
        echo "<p><strong>Query Failed:</strong> $query<br><strong>Error:</strong> " . $conn->error . "</p>";
        return 0;
    }
    $row = $result->fetch_row();
    return $row[0] ?? 0;
}

// Count Data
$totalTaxpayers = getCount($conn, "SELECT COUNT(*) FROM users");
$todayReturns = getCount($conn, "SELECT COUNT(*) FROM tax_history WHERE DATE(created_at) = CURDATE()");
$activities = getCount($conn, "SELECT COUNT(*) FROM tax_history");
$notifications = getCount($conn, "SELECT COUNT(*) FROM tax_calculations");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KRA Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #fff;
            font-family: Arial, sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #b70000;
            padding: 1rem;
            color: white;
            text-align: center;
        }
        .dashboard-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            text-align: center;
        }
        .stat-grid {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }
        .stat-box {
            background-color: #000;
            color: #fff;
            padding: 2rem;
            border-radius: 12px;
            width: 200px;
            transition: transform 0.3s ease;
        }
        .stat-box:hover {
            transform: scale(1.05);
        }
        .stat-box h2 {
            font-size: 2.5rem;
            margin: 0;
        }
        .stat-box p {
            margin-top: 0.5rem;
            font-size: 1.1rem;
        }
        .footer {
            margin-top: 3rem;
            text-align: center;
            font-size: 0.95rem;
            color: #666;
        }
    </style>
</head>
<body>

<?php include("navbar.php"); ?>

<div class="dashboard-container">
    <div class="stat-grid">
        <div class="stat-box">
            <h2><?= $totalTaxpayers ?></h2>
            <p>Total Taxpayers</p>
        </div>
        <div class="stat-box">
            <h2><?= $todayReturns ?></h2>
            <p>Today's Tax Returns</p>
        </div>
        <div class="stat-box">
            <h2><?= $activities ?></h2>
            <p>Tax History Records</p>
        </div>
        <div class="stat-box">
            <h2><?= $notifications ?></h2>
            <p>Tax Calculations</p>
        </div>
    </div>
</div>

<div class="footer">
    Logged in as: <strong><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></strong>
</div>

</body>
</html>
