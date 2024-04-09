<?php
session_start();

// Include login.php
include ("../processing/login.php");

// Include connection.php so we can load departments into create post modal
require_once ('../includes/connection.php');
// Check if the current page is index.php. If not that means someone is only user profile page or admin page
// Thus we will hide the create post button and search bar
$isIndexPage = strpos($_SERVER['REQUEST_URI'], 'index.php') !== false;
?>

<!--Bootstrap nav bar-->

<head>
    <script src="../js/register.js"></script>
    <script src="../js/login.js"></script>
    <script src="../js/createpost.js"></script>
    <script src="../js/profilemodal.js"></script>
    <script src="../js/nav.js"></script>
</head>
<nav class="navbar navbar-expand-md navbar-dark bg-primary fixed-top" style="background-color: #27374D !important;">
    <div class="container-fluid">
        <a class="navbar-brand" href="../pages/index.php">CampusTalk</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item">
                    <!--   later we will add a variable here that will cause index.php to load posts based on likes -->
                    <a class="nav-link" href="../pages/index.php">Hot Topics</a>
                </li>
                <li class="nav-item">
                    <!--   later we will add a variable here that will cause index.php to load posts randomly -->
                    <a class="nav-link" href="../pages/index.php">Random</a>
                </li>
            </ul>
            <div class="d-flex">
                <?php if ($isIndexPage) { ?>
                    <form class="d-flex me-2 flex-grow-1" id="nav-search-form">
                        <input class="form-control me-2 flex-grow-1" id="nav-search-bar" type="search" placeholder="Search">
                    </form>
                <?php } ?>

                <?php if (isset ($_SESSION['username'])) { ?>
                    <!-- Dropdown for logged-in users -->
                    <?php if ($isIndexPage) { ?>
                        <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#createPostModal"
                            id="createpost">Create Post</button>
                    <?php } ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    data-bs-target="#profilemodal">Profile Settings</a></li> <!-- Display a modal -->
                            <li><a class="dropdown-item" href="#" id="logout">Sign Out</a></li>
                        </ul>
                    </div>
                <?php } else { ?>
                    <!-- Login button for nnon logged-in users -->
                    <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal"
                        id="loginbutton">Login</button>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>
<!-- Profile modal -->
<!-- Data will get sent to profile.php-->
<div class="modal fade" id="profilemodal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profilemodalLabel">Personal Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align: center;">
                <?php
                $userid = $_SESSION['userID'];

                echo "<script>console.log('UserID:', $userid);</script>";

                $query = "SELECT * FROM Users WHERE userID = ?;";
                $conn = connectToDB();
                $stmt = $conn->prepare($query);

                $stmt->bind_param("i", $userid);

                $stmt->execute();

                $result = $stmt->get_result();
                $userinfo = $result->fetch_assoc();

                $stmt->close();
                $conn->close();
                ?>
                <!-- Render the image if it isn't null -->
                <?php if ($userinfo['profileimage'] != null) { ?>
                    <p id="profile-pic">
                        <img id="profilepic" src="data:image/png;base64,<?php echo base64_encode($userinfo['profileimage']); ?>">
                    <p>
                        Click edit info to add a profile picture
                    </p>
                    </p>
                <?php } else { ?>
                    <div class="mb-3 profileinfo">
                        <p>
                            Click edit info to add a profile picture
                        </p>
                    </div>
                <?php } ?>
                <div class="mb-3 profileinfo">
                    <p> <b> Username: </b>
                        <?php echo $userinfo['username']; ?>
                    </p>
                </div>
                <div class="mb-3 profileinfo">
                    <p> <b> Firstname: </b>
                        <?php echo $userinfo['firstname']; ?>
                    </p>
                </div>
                <div class="mb-3 profileinfo">
                    <p> <b> Lastname: </b>
                        <?php echo $userinfo['lastname']; ?>
                    </p>
                </div>
                <div class="mb-3 profileinfo">
                    <p> <b> Email: </b>
                        <?php echo $userinfo['email']; ?>
                    </p>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal" data-bs-toggle="modal"
                    data-bs-target="#edit-info-form-modal">Edit Info</button>
                <button type="button" class="btn btn-link" data-bs-dismiss="modal" data-bs-toggle="modal"
                    data-bs-target="#new-password-modal">Change Password</button>

            </div>
        </div>
    </div>
</div>
<!-- Form for editing information -->
<!-- profilemodal.js handles this -->
<div class="modal fade" id="edit-info-form-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-info-form-label">Edit Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- The action is to ensure that javascript catches the form on submit -->
                <form action="javascript:void(0);" method="POST" id="edit-info-form">
                    <div class="mb-3">
                        <label for="username" class="form-label" id="errorMessage">Username</label>
                        <input type="text" class="form-control" name="new-username"
                            value="<?php echo $userinfo['username']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" class="form-control" name="new-fname"
                            value="<?php echo $userinfo['firstname']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="new-lname"
                            value="<?php echo $userinfo['lastname']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" name="new-email"
                            value="<?php echo $userinfo['email']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="profile-image" class="form-label" id="image-label">Upload Image</label>
                        <input type="file" class="form-control" name="new-profileimage" id="new-profileimage"
                            accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- This modal is for a password change, it is handled by js -->
<div class="modal fade" id="new-password-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="new-password-modal-label">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0);" id="new-password-form" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label" id="new-password-label">Password</label>
                        <input type="text" class="form-control" id="new-password" name="new-password">
                    </div>
                    <div class="mb-3">
                        <label for="new-password-confirm" class="form-label" id="new-password-confirm-label">Confirm Password</label>
                        <input type="password" class="form-control" id="new-password-confirm" name="new-password-confirm">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Login Popup Modal -->
<!-- Data will get sent to login.php-->
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Your login form goes here -->
                <!-- The action is to ensure that javascript catches the form on submit -->
                <form action="javascript:void(0);" id="loginform" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label" id="usernamelabel">Username</label>
                        <input type="text" class="form-control" id="username" name="username">
                    </div>
                    <div class="mb-3">
                        <label for="pw" class="form-label" id="passwordlabel">Password</label>
                        <input type="password" class="form-control" id="pw" name="password">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
            <div class="modal-footer">
                <p class="text-center">Don't have an account? <button type="button" class="btn btn-link"
                        data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#signupModal">Sign up</button>
                </p>
            </div>
        </div>
    </div>
</div>
<!-- This is the modal for creating a post -->
<div class="modal fade" id="createPostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPostModalLabel">Create Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <form action="javascript:void(0);" method="POST" id="create-post-form">
                    <div class="mb-3">
                        <label for="postTitle" class="form-label" id="title-label">Title</label>
                        <input type="text" class="form-control" id="post-title" name="title">
                    </div>
                    <div class="mb-3">
                        <label for="postContent" class="form-label" id="content-label">Content</label>
                        <textarea class="form-control" id="postContent" rows="3" name="content"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="postImage" class="form-label" id="image-label">Upload Image</label>
                        <input type="file" class="form-control" id="postImage" accept="image/*" name="imageupload">
                    </div>
                    <div class="mb-3">
                        <label for="departmentSelect" class="form-label" id="departmentLabel">Select a
                            Department</label>
                        <select class="form-select" id="departmentSelect" name="department">
                            <option value="0">Select a Department</option>
                            <?php
                            $conn = connectToDB();
                            // Check connection
                            if ($conn->connect_error) {
                                die ("Connection failed: " . $conn->connect_error);
                            }
                            $sql = "SELECT * FROM Department";
                            $result = $conn->query($sql);
                            $departments = [];

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $departmentId = $row['departmentID'];
                                    $departmentName = $row['name'];
                                    echo '<option value="' . $departmentId . '">' . $departmentName . '</option>';
                                }
                            }
                            close_db($conn);
                            ?>
                        </select>
                    </div>

                </form>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submit-post">Save Post</button>
            </div>
        </div>
    </div>
</div>

<!-- Signup Popup Modal -->
<!-- Data will get sent to signup.php-->
<div class="modal fade" id="signupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signupModalLabel">Sign Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- The action is to ensure that javascript catches the form on submit -->
                <form action="javascript:void(0);" method="POST" id="signupform">
                    <div class="mb-3">
                        <label for="username" class="form-label" id="errorMessage">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" class="form-control" name="firstname" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="lastname" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmpassword" class="form-label">Confirm Your Password</label>
                        <input type="password" class="form-control" name="confirmpassword" id="confirmpassword"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="profile-image" class="form-label" id="image-label">Upload Image</label>
                        <input type="file" class="form-control" name="profileimage" id="profileimage" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Sign Up</button>
                </form>
            </div>
            <div class="modal-footer">
                <p class="text-center">Already have an account? <button type="button" class="btn btn-link"
                        data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">Log In</button></p>
            </div>
        </div>
    </div>
</div>