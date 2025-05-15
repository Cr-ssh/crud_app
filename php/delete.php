<?php
include 'config.php';

$id = $_POST['id'];
$response = [];

if (empty($id)) {
    $response['success'] = false;
    $response['message'] = 'ID is required';
    echo json_encode($response);
    exit;
}

$sql = "DELETE FROM records WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "Record deleted successfully";
} else {
    $response['success'] = false;
    $response['message'] = "Error deleting record: " . $conn->error;
}

echo json_encode($response);
$stmt->close();
$conn->close();
?>
