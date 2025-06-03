<?php
session_start();
require_once 'config.php'; // Include database connection

header('Content-Type: application/json');

// Ensure the user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Match the keys sent from JavaScript exactly
$tax_type = $_POST['tax_type'] ?? '';
$income = floatval($_POST['income'] ?? 0);
$tax = floatval($_POST['tax_amount'] ?? 0);
$name = trim($_POST['name'] ?? '');

// Basic validation
if (!$tax_type || !is_numeric($income) || !is_numeric($tax)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data received']);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO tax_history (user_id, name, tax_type, income, tax_amount) VALUES (?, ?, ?, ?, ?)");

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("issdd", $user_id, $name, $tax_type, $income, $tax);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Calculation saved successfully.']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
