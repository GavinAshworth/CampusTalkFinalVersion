<?php
require_once('../includes/connection.php');
session_start();


$conn = connectToDB();

try {
    $userID = $_POST['userID'];
    $newIsEnabledValue = $_POST['newIsEnabledValue'];


        $stmt = $conn->prepare("UPDATE users SET isEnabled = ? WHERE userID = ?");
        $stmt->bind_param("ii", $newIsEnabledValue,$userID);
        $stmt->execute();

        $_SESSION['isEnabled'] = $newIsEnabledValue;

    // Close the statements and connection
    $stmt->close();
    $conn->close();

    // Send the user data back to JavaScript as JSON
    echo json_encode(['isEnabledConfirmation' => $newIsEnabledValue]);

} catch (Exception $e) {
    // Log the error to a file or output it directly
    error_log($e->getMessage()); // Log the error message to the server's error log
    echo json_encode(['error' => 'An error occurred while processing your request.']); // Output a generic error message
}

