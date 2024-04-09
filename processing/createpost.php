<?php
include ("../includes/connection.php");

// get the referrer info so that if it is set we can send the user back to the previous page upon completion of processing
$referrer = isset ($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
session_start();

$userid = $_SESSION['userID'];
$isEnabled = $_SESSION['isEnabled'] ;

try {
    // Check if user is enabled
    if ($isEnabled == 1) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset ($_POST['title']) && isset ($_POST['department'])) {
            $conn = connectToDB();

            $title = $_POST['title'];
            $content = $_POST['content'];
            $dept = $_POST['department'];

            // Handle image upload separately
            if (isset ($_FILES['imageupload']) && $_FILES['imageupload']['error'] === UPLOAD_ERR_OK) {
                $image = $_FILES['imageupload']['name'];
                // Process and move the uploaded file to desired location
                $image = file_get_contents($_FILES['imageupload']['tmp_name']);
            } else {
                // Handle case where image is not uploaded
                $image = null;
            }

            // Insert data into the database
            $stmt = $conn->prepare("INSERT INTO Posts(title, content, userID, departmentID, postImage) VALUES(?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiib", $title, $content, $userid, $dept, $image);
            $stmt->send_long_data(4, $image); // Send the image data as a parameter
            $stmt->execute();
            $stmt->close();
            $conn->close();

            // Respond with success message
            echo json_encode(array("success" => "Post created successfully", "redirect" => $referrer));
        } else {
            echo json_encode(array("error" => "Invalid request or missing parameters", "redirect" => $referrer));
        }
    } else {
        echo json_encode(array("error" => "You have been disabled by an admin and cannot create a post", "redirect" => $referrer));
    }
} catch (Exception $e) {
    echo json_encode(array("error" => $e->getMessage(), "redirect" => "$referrer"));
}

