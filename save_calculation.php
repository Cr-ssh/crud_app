<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'] ?? '';
$tax_type = $_POST['tax_type'] ?? '';
$income = floatval($_POST['income'] ?? 0);
$tax_amount = floatval($_POST['tax_amount'] ?? 0);
$tax_rate = $_POST['tax_rate'] ?? '';
$record_id = $_POST['record_id'] ?? null; // ✅ Add this line

// Save the tax calculation
$sql = "INSERT INTO tax_calculations (user_id, name, tax_type, income, tax_amount, tax_rate, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo "Prepare failed: " . $conn->error;
    exit;
}

$stmt->bind_param("issdds", $user_id, $name, $tax_type, $income, $tax_amount, $tax_rate);

if ($stmt->execute()) {
    // ✅ Update tax_amount in records table if record_id is provided
    if ($record_id !== null) {
        $updateStmt = $conn->prepare("UPDATE records SET tax_amount = ? WHERE id = ?");
        $updateStmt->bind_param("di", $tax_amount, $record_id);
        $updateStmt->execute();
        $updateStmt->close();
    }

    echo "Success";
} else {
    http_response_code(500);
    echo "Execute failed: " . $stmt->error;
}

$stmt->close();
?>
