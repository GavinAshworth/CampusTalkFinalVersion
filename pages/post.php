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
            <div class = "sidebar-content">
                <div class = "left-sidebar-topic">
                    <p class = "sidebar-item left-item">
                        <a href="index.php">Home</a>
                    </p>
                </div>
                <div class = "left-sidebar-topic sidebar-border-top">
                    <p class = "topic-header">
                        <span>Department</span>
                    </p>
                    <div id = "department-container">

                    </div>
                </div>
                <div class = "left-sidebar-topic sidebar-border-top">
                    <p class = "sidebar-item left-item topic-header">
                        <a href="#">Help</a>
                    </p>
                    <p class = "sidebar-item left-item ">
                        <a href="#">Report A Problem</a>
                    </p>
                    <p class = "sidebar-item left-item ">
                        <a href="#">Contact Us</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="main-content">
            <button id="toggle-columns">Toggle Columns</button>
            <div class="posts-container">
                <?php
                if (isset ($_GET['postId'])) {
                    $postId = $_GET['postId'];

                    $query = "SELECT * FROM Posts JOIN Users ON Users.userID = Posts.userID WHERE postID = ?;";
                    $commentsQuery = "SELECT * FROM Comments JOIN Users ON Users.userID = Comments.UserID WHERE postID = ?;";

                    try {
                        $conn = connectToDB();

                        // throw an error in case it can't connect to the db
                        if ($conn->connect_error) {
                            throw new Exception("Connection failed: " . $conn->connect_error);
                        }

                        // prepare and execute the query to fetch posts
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $postId);
                        $stmt->execute();

                        // fetch posts
                        $postsResult = $stmt->get_result();
                        $posts = $postsResult->fetch_all(MYSQLI_ASSOC);

                        // prepare and execute the query to fetch comments
                        $stmt = $conn->prepare($commentsQuery);
                        $stmt->bind_param("i", $postId);
                        $stmt->execute();

                        // fetch comments
                        $commentsResult = $stmt->get_result();
                        $comments = $commentsResult->fetch_all(MYSQLI_ASSOC);

                        // close the database connection
                        $conn->close();
                        ?>
                        <!-- load hte post info -->
                        <div class="post-container" data-id="<?php echo $posts[0]['postID'] ?>">
                            <div class="user-pic-username-container">
                                <?php if ($posts[0]['profileimage']) { ?>
                                    <img src="data:image/png;base64,<?php echo base64_encode($posts[0]['profileimage']); ?>" alt="User Profile Pic">
                                <?php } ?>
                                <h5 class="usernames"><?php echo htmlspecialchars($posts[0]['username']); ?></h5>
                            </div>
                            <h2 class="post-title">
                                <?php if (isset ($_SESSION['isAdmin']) && ($_SESSION['isAdmin'] == 1)) {
                                    echo "<div><button class=\"btn btn-primary edit-btn\" id=\"delete-post\">Delete Post</button></div>";
                                    echo "<div><button class=\"btn btn-primary edit-btn\" id=\"editButton\" data-bs-toggle=\"modal\" data-bs-target=\"#editPost\">Edit</button></div>";
                                } ?>
                                <?php echo htmlspecialchars($posts[0]['title']); ?>
                            </h2>
                            <p class="post-text">
                                <?php echo htmlspecialchars($posts[0]['content']); ?>
                            </p>

                            <!-- Render the image if it isn't null -->
                            <?php if($posts[0]['postImage'] != null) { ?>

                                <p class="image-container post-img">
                                    <img src="data:image/png;base64,<?php echo base64_encode($posts[0]['postImage']); ?>">
                                </p>

                            <?php }?>

                            <div class="user-input-post">
                                <input class="form-control comment-input" id="commentBox" type="text"
                                    placeholder="Write a comment...">
                                <button class="btn btn-primary like-btn" id="commentButton">Comment</button>
                            </div>
                        </div>
                        <?php
                        $reversedComments = array_reverse($comments);
                        //load the comments
                        foreach ($reversedComments as $comment) {
                            // access individual comment data
                            $commentId = $comment['commentID'];
                            $commentContent = $comment['content'];
                            $username = $comment['username'];
                            // output the comment data
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
                        // show error message
                        echo "Error: " . $e->getMessage();
                    }
                }

                ?>
            </div>
        </div>
        <!-- display the admin bar if admin    -->
        <?php if (isset ($_SESSION['isAdmin']) && ($_SESSION['isAdmin'] == 1)) { ?>
            <div class="right">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Admin Controls</h5>
                    </div>
                    <div class="card-body">
                        <form action="javascript:void(0);" method="POST" id="admin-search-form" class="d-flex flex-column">
                            <div class="mb-3">
                                <label for="userInformationAdminSelect" class="form-label" id="departmentLabel">Search For
                                    User By</label>
                                <select class="form-select" id="userInformationAdminSelect" name="userInformationAdmin">
                                    <option value="0">Username</option>
                                    <option value="1">Email</option>
                                    <option value="2">Post</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <input class="form-control" id="admin-search-bar" type="search" placeholder="Search">
                            </div>
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- this will be the modal that allows an admin to edit the post -->
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
                                    value="<?php echo htmlspecialchars($posts[0]['title']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="postContent" class="form-label" id="content-label">Content</label>
                                <textarea class="form-control" id="postContent" rows="3"
                                    name="content"><?php echo htmlspecialchars($posts[0]['content']); ?></textarea>
                            </div>
                            <input type="hidden" name="hiddenpostid" value="<?php echo $posts[0]['postID'] ?>">
                        </form>
                    </div>
                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="edit-post-button">Save Post</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal to display users when admin searches for them -->
        <div class="modal fade" id="userModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Search Results</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
                    </div>
                    <div class="modal-body" id="userModalBody">
                        <!-- Users will be populated here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require_once ('../includes/footer.php'); ?>