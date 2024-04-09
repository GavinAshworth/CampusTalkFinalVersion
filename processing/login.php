<?php
require_once("../includes/connection.php");

// This is the function that logs a user in, it creates a session, give it a timeout and stores the users information in the session so we can use it in other places
function login($username, $userID, $isAdmin, $isEnabled)
{
    // Start or resume the session if it's not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Store user information in session variables
    $_SESSION['username'] = $username;
    $_SESSION['userID'] = $userID;
    $_SESSION['isAdmin'] = $isAdmin;
    $_SESSION['isEnabled'] = $isEnabled;

    // Session will timeout in an hour
    $timeout_seconds = 3600;
    $_SESSION['timeout'] = time() + $timeout_seconds;

    // Redirect the user to a logged-in page or any other desired location
    // header("Location: logged_in.php");
    // exit(); // Ensure that script execution stops after redirecting
}

// get the referrer info so that if it is set we can send the user back to the previous page upon completion of processing
$referrer = isset ($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

// start connecting to the db
try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset ($_POST['username']) && isset ($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $conn = connectToDB();

        // throw an error incase it can't connect to the db
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $query = "SELECT userID, userpassword, isAdmin, isEnabled FROM Users WHERE username = ?;";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);

        $stmt->execute();

        $stmt->store_result();

        $numberOfRows = $stmt->num_rows;

        if ($numberOfRows == 0) {
            echo json_encode(array("error" => "no result", "redirect" => "$referrer"));
        } else {


            $stmt->bind_result($userID, $userpassword, $isAdmin, $isEnabled);

            $stmt->fetch();

            if ($userpassword == md5($password)) {
                login($username, $userID, $isAdmin, $isEnabled);
                $stmt->close();
                $conn->close();
                echo json_encode(array("success" => "welcome", "redirect" => "$referrer"));
            } else {
                $stmt->close();
                $conn->close();
                echo json_encode(array("error" => "incorrect password", "redirect" => "$referrer"));
            }
        }
    } else {
        echo json_encode(array("error" => "missing data", "redirect" => "$referrer"));
    }
} catch (Exception $e) {
    // Handle exception
    echo json_encode(array("error" => $e->getMessage(), "redirect" => "$referrer"));
}

