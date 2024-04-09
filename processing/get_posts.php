<?php
require_once('../includes/connection.php');
session_start();
function getPosts() {
    try {
        $conn = connectToDB();
        //check if key is in get request
        if (isset($_GET['key'])){
            $key = $_GET['key'];
        }
        // Check if 'department' is set in the GET request
        if (isset($_GET['department'])) {
            $department = $_GET['department'];
            if($key !== 'none'){
                $stmt = $conn->prepare("SELECT * FROM Posts JOIN Users ON Users.userID = Posts.userID WHERE title like ? or content like ? ORDER BY Posts.createdAt DESC LIMIT 1000;");
                $searchTermLike = "%" . $key . "%";
                $stmt->bind_param("ss", $searchTermLike,$searchTermLike);
                $stmt->execute();
                $result3 = $stmt->get_result();
            }
            else if ($department !== 'all') {
                $sql = "SELECT * FROM Posts JOIN Users ON Users.userID = Posts.userID WHERE departmentID = $department ORDER BY Posts.createdAt DESC LIMIT 1000;";
            } else {
                $sql = "SELECT * FROM Users JOIN Posts ON Users.userID = Posts.userID ORDER BY Posts.createdAt DESC LIMIT 1000;";
            }
        } else {
            $sql = "SELECT * FROM Posts JOIN Users ON Users.userID = Posts.userID ORDER BY Posts.createdAt DESC LIMIT 1000;";
        }
        // Check connection
        if ($conn->connect_error) {
            echo "<script> console.log('error connecting'); </script>";
            die("Connection failed: " . $conn->connect_error);
        }
        if($key !== 'none'){
            $result = $result3;
        }
        else{
            $result = $conn->query($sql);
        }

        $posts = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Convert the binary image data to base64 encoding
                $profilepic = base64_encode($row['profileimage']);
                $imageData = base64_encode($row['postImage']);
                $postId = $row['postID'];
                // Find department from department id
                $deptId = $row['departmentID'];
                $sql1 = "SELECT * FROM department WHERE departmentID = " . $deptId;
                $result1 = $conn->query($sql1);

                $departmentName = '';
                if ($result1->num_rows > 0) {
                    $deptRow = $result1->fetch_assoc();
                    $departmentName = $deptRow['name'];
                }
                //See if user liked post

                $isLiked = false;
                //check if the post has been liked by the user
                // Check if the user is logged in
                if (isset($_SESSION['userID'])) {
                    $userId = $_SESSION['userID'];

                    // Check if the like already exists in the database
                    $stmt1 = $conn->prepare("SELECT * FROM likes WHERE userId = ? AND postId = ?");
                    $stmt1->bind_param("ii", $userId, $postId);
                    $stmt1->execute();
                    $result1 = $stmt1->get_result();

                    if ($result1->num_rows > 0) {
                        // User has already liked the post
                        $isLiked = true;
                    }
                }
                //get the number of likes on the post
                $stmt2 = $conn->prepare("SELECT * FROM LIKES WHERE postID = ?");
                $stmt2->bind_param("i",$postId);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                $numLikes = $result2->num_rows;
                // Construct the post object including image data and department name
                $post = [
                    'postID' => $row['postID'],
                    'title' => $row['title'],
                    'content' => $row['content'],
                    'userID' => $row['userID'],
                    'departmentID' => $row['departmentID'],
                    'departmentName' => $departmentName, // Include the department name
                    'courseID' => $row['courseID'],
                    'postImage' => $imageData, // Include the image data
                    'createdAt' => $row['createdAt'],
                    'username' => $row['username'],
                    'profilepic' => $profilepic,
                    'isLiked' => $isLiked,
                    'numLikes' => $numLikes
                ];
                $posts[] = $post;

                //check if the post has been liked by the user

            }
        }

        close_db($conn);
        return $posts;
    } catch (Exception $e) {
        return json_encode(array("error" => $e->getMessage()));
    }
}

//echo the json encoded string
echo json_encode(getPosts());
