<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Validate inputs
$recordId = isset($_POST['record_id']) ? intval($_POST['record_id']) : 0;
$taxAmount = isset($_POST['tax_amount']) ? floatval($_POST['tax_amount']) : 0;

if ($recordId <= 0 || $taxAmount < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Update the record
$stmt = $conn->prepare("UPDATE records SET tax_amount = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit;
}
$stmt->bind_param("di", $taxAmount, $recordId);
$success = $stmt->execute();
$stmt->close();

echo json_encode(['success' => $success]);
?>
