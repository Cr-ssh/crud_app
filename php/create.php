<?php
include 'config.php';

$name = trim($_POST['name']);
$kra_pin = trim($_POST['kra_pin']);
$category = trim($_POST['category']);
$status = trim($_POST['status']);

$response = [];

if (empty($name)) {
    $response['success'] = false;
    $response['message'] = 'Name is required';
    echo json_encode($response);
    exit;
}

$sql = "INSERT INTO records (name, kra_pin, category, status) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $kra_pin, $category, $status);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "Record created successfully";
    $response['newRecordId'] = $stmt->insert_id;  // Add this line
} else {
    $response['success'] = false;
    $response['message'] = "Error creating record: " . $conn->error;
}

echo json_encode($response);
$stmt->close();
$conn->close();
?>
