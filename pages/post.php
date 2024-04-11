<?php require_once ('../includes/header.php'); ?>

<head>
    <link href="../css/index.css" rel="stylesheet">
    <link href="../css/postpage.css" rel="stylesheet">
    <script src="../js/postinfo.js"></script>
</head>

<body>
<?php require_once ('../includes/nav.php'); ?>
<main>
    <div class="left">
        <div class="sidebar-content">
            <div class="left-sidebar-topic">
                <p class="sidebar-item left-item">
                    <a href="index.php" aria-labelledby="homeLabel">Home</a>
                </p>
            </div>
            <div class="left-sidebar-topic sidebar-border-top">
                <p class="topic-header">
                    <span aria-labelledby="departmentLabel">Department</span>
                </p>
                <div id="department-container" aria-labelledby="departmentLabel">

                </div>
            </div>
            <div class="left-sidebar-topic sidebar-border-top">
                <p class="sidebar-item left-item topic-header">
                    <a href="#" aria-labelledby="helpLabel">Help</a>
                </p>
                <p class="sidebar-item left-item ">
                    <a href="#" aria-labelledby="reportProblemLabel">Report A Problem</a>
                </p>
                <p class="sidebar-item left-item ">
                    <a href="#" aria-labelledby="contactUsLabel">Contact Us</a>
                </p>
            </div>
        </div>
    </div>
    <div class="main-content">
        <button id="toggle-columns">Toggle Columns</button>
        <div class="posts-container">
            <?php
            if (isset($_GET['postId'])) {
                $postId = $_GET['postId'];

                $query = "SELECT * FROM Posts JOIN Users ON Users.userID = Posts.userID WHERE postID = ?;";
                $commentsQuery = "SELECT * FROM Comments JOIN Users ON Users.userID = Comments.UserID WHERE postID = ?;";

                try {
                    $conn = connectToDB();

                    if ($conn->connect_error) {
                        throw new Exception("Connection failed: " . $conn->connect_error);
                    }

                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $postId);
                    $stmt->execute();

                    $postsResult = $stmt->get_result();
                    $posts = $postsResult->fetch_all(MYSQLI_ASSOC);

                    $stmt = $conn->prepare($commentsQuery);
                    $stmt->bind_param("i", $postId);
                    $stmt->execute();

                    $commentsResult = $stmt->get_result();
                    $comments = $commentsResult->fetch_all(MYSQLI_ASSOC);

                    $conn->close();
                    ?>
                    <div class="post-container" data-id="<?php echo $posts[0]['postID'] ?>">
                        <div class="user-pic-username-container" aria-labelledby="userDetailsLabel">
                            <?php if ($posts[0]['profileimage']) { ?>
                                <img src="data:image/png;base64,<?php echo base64_encode($posts[0]['profileimage']); ?>"
                                     alt="User Profile Pic">
                            <?php } ?>
                            <h5 class="usernames">
                                <?php echo htmlspecialchars($posts[0]['username']); ?>
                            </h5>
                        </div>
                        <h2 class="post-title" aria-labelledby="postTitleLabel">
                            <?php if (isset($_SESSION['isAdmin']) && ($_SESSION['isAdmin'] == 1)) {
                                echo "<div><button class=\"btn btn-primary edit-btn\" id=\"delete-post\" aria-labelledby=\"deletePostLabel\">Delete Post</button></div>";
                                echo "<div><button class=\"btn btn-primary edit-btn\" id=\"editButton\" data-bs-toggle=\"modal\" data-bs-target=\"#editPost\" aria-labelledby=\"editPostLabel\">Edit</button></div>";
                            } ?>
                            <?php echo htmlspecialchars($posts[0]['title']); ?>
                        </h2>
                        <p class="post-text" aria-labelledby="postContentLabel">
                            <?php echo htmlspecialchars($posts[0]['content']); ?>
                        </p>
                        <?php if ($posts[0]['postImage'] != null) { ?>
                            <p class="image-container post-img" aria-labelledby="postImageLabel">
                                <img src="data:image/png;base64,<?php echo base64_encode($posts[0]['postImage']); ?>">
                            </p>
                        <?php } ?>
                        <div class="user-input-post">
                            <input class="form-control comment-input" id="commentBox" type="text"
                                   placeholder="Write a comment..." aria-labelledby="commentBoxLabel">
                            <button class="btn btn-primary like-btn" id="commentButton" aria-labelledby="commentButtonLabel">Comment</button>
                        </div>
                    </div>
                    <?php
                    $reversedComments = array_reverse($comments);
                    foreach ($reversedComments as $comment) {
                        $commentId = $comment['commentID'];
                        $commentContent = $comment['content'];
                        $username = $comment['username'];
                        ?>
                        <div class="comment-container" data-id="post.postID">
                            <h5 class="username">
                                <?php echo $username; ?>
                            </h5>
                            <p class="comment-text">
                                <?php echo $commentContent; ?>
                            </p>
                        </div>
                        <?php
                    }
                } catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            }

            ?>
        </div>
    </div>
    <div class="modal fade" id="editPost" tabindex="-1" aria-labelledby="editPostModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPostModalLabel">Edit Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="javascript:void(0);" method="POST" id="edit-post-form">
                        <div class="mb-3">
                            <label for="edit-post-title" class="form-label" id="edit-title-label">Title</label>
                            <input type="text" class="form-control" id="edit-post-title" name="title"
                                   value="<?php echo htmlspecialchars($posts[0]['title']); ?>" aria-labelledby="editTitleLabel">
                        </div>
                        <div class="mb-3">
                            <label for="postContent" class="form-label" id="content-label">Content</label>
                            <textarea class="form-control" id="postContent" rows="3"
                                      name="content"><?php echo htmlspecialchars($posts[0]['content']); ?></textarea>
                        </div>
                        <input type="hidden" name="hiddenpostid" value="<?php echo $posts[0]['postID'] ?>">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-labelledby="closeButtonLabel">Close</button>
                    <button type="button" class="btn btn-primary" id="edit-post-button" aria-labelledby="saveButtonLabel">Save Post</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Search Results</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="userModalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-labelledby="closeUserModalLabel">Close</button>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once ('../includes/footer.php'); ?>
</body>

</html>
