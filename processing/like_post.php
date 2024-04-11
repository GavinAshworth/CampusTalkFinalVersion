<?php
// Start the session
session_start();

// Include database connection
include("../includes/connection.php");

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(array("error" => "User is not logged in"));
    exit;
}
$conn = connectToDB();

$postId = $_POST['postId'];

$userId = $_SESSION['userID'];

// Check if the like already exists in the database
$stmt = $conn->prepare("SELECT * FROM likes WHERE userId = ? AND postId = ?");
$stmt->bind_param("ii", $userId, $postId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User has already liked the post
    // Delete the like from the database
    $stmt = $conn->prepare("DELETE FROM likes WHERE userId = ? AND postId = ?");
    $stmt->bind_param("ii", $userId, $postId);

    if ($stmt->execute()) {
        echo json_encode(array("isLiked" => false));
    } else {
        echo json_encode(array("isLiked" => true, "error" => $stmt->error));
    }
} else {
    // Insert new like into the database
    $stmt = $conn->prepare("INSERT INTO likes (userId, postId) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $postId);

    if ($stmt->execute()) {
        echo json_encode(array("isLiked" => true));
    } else {
        echo json_encode(array("isLiked" => false, "error" => $stmt->error));
    }
}

$stmt->close();
$conn->close();

