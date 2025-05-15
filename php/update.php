<?php
include 'config.php';

$id = $_POST['id'];
$name = trim($_POST['name']);
$kra_pin = trim($_POST['kra_pin']);
$category = trim($_POST['category']);
$status = trim($_POST['status']);

$response = [];

if (empty($id) || empty($name)) {
    $response['success'] = false;
    $response['message'] = 'ID and Name are required';
    echo json_encode($response);
    exit;
}

$sql = "UPDATE records SET name=?, kra_pin=?, category=?, status=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $name, $kra_pin, $category, $status, $id);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "Record updated successfully";
} else {
    $response['success'] = false;
    $response['message'] = "Error updating record: " . $conn->error;
}

echo json_encode($response);
$stmt->close();
$conn->close();
?>
