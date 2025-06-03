<?php
session_start();
require_once 'php/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT 
            tc.*, 
            r.name AS record_name 
        FROM tax_calculations tc
        LEFT JOIN records r ON tc.record_id = r.id
        WHERE tc.user_id = ? 
        ORDER BY tc.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Saved Tax Calculations</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Your Saved Calculations</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Income</th>
                    <th>Tax</th>
                    <th>Rate</th>
                    <th>Saved At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['record_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['tax_type']) ?></td>
                        <td><?= number_format($row['income'], 2) ?></td>
                        <td><?= number_format($row['tax_amount'], 2) ?></td>
                        <td><?= htmlspecialchars($row['tax_rate']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
