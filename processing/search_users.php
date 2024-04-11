<?php
require_once('../includes/connection.php');
session_start();


$conn = connectToDB();
$users = []; //users will be stored here

try {
    $searchType = $_POST['searchType'];
    $searchTerm = $_POST['searchTerm'];

    if ($searchType == 0) {
        // Search by username
        $stmt = $conn->prepare("SELECT username, isEnabled, userID FROM users WHERE username LIKE ?");
        $searchTermLike = "%" . $searchTerm . "%";
        $stmt->bind_param("s", $searchTermLike);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

    } elseif ($searchType == 1) {
        // Search by email
        $stmt = $conn->prepare("SELECT username, isEnabled, userID FROM users WHERE email LIKE ?");
        $searchTermLike = "%" . $searchTerm . "%";
        $stmt->bind_param("s", $searchTermLike);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

    } elseif ($searchType == 2) {
        // Search by post title and then fetch users
        $stmt = $conn->prepare("SELECT userID FROM posts WHERE title LIKE ?");
        $searchTermLike = "%" . $searchTerm . "%";
        $stmt->bind_param("s", $searchTermLike);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch user details for the matched userIDs
        $userIDs = [];
        while ($row = $result->fetch_assoc()) {
            $userIDs[] = $row['userID'];
        }

        // Search users by the fetched userIDs
        $userIDsStr = implode(",", $userIDs);
        $stmtUsers = $conn->prepare("SELECT username, isEnabled, userID FROM users WHERE userID IN ($userIDsStr)");
        $stmtUsers->execute();
        $resultUsers = $stmtUsers->get_result();

        while ($row = $resultUsers->fetch_assoc()) {
            $users[] = $row;
        }
    }

    // Close the statements and connection
    $stmt->close();
    if (isset($stmtUsers)) {
        $stmtUsers->close();
    }
    $conn->close();

    // Send the user data back to JavaScript as JSON
    echo json_encode(['users' => $users]);

} catch (Exception $e) {
    // Log the error to a file or output it directly
    error_log($e->getMessage()); // Log the error message to the server's error log
    echo json_encode(['error' => 'An error occurred while processing your request.']); // Output a generic error message
}

