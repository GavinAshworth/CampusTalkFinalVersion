<?php require_once('../includes/header.php'); ?>
<link href="../css/index.css" rel="stylesheet">
<script src="../js/index.js"></script>
</head>
<body>
<?php require_once ('../includes/nav.php'); ?>
<main>
    <div class="left" aria-label="Left Sidebar">
        <div class="sidebar-content">
            <div class="left-sidebar-topic">
                <p class="sidebar-item left-item">
                    <a href="index.php">Home</a>
                </p>
            </div>
            <div class="left-sidebar-topic sidebar-border-top" aria-label="Department Section">
                <p class="topic-header">
                    <span>Department</span>
                </p>
                <div id="department-container" aria-label="Department List">

                </div>
            </div>
            <div class="left-sidebar-topic sidebar-border-top" aria-label="Help Section">
                <p class="sidebar-item left-item topic-header">
                    <a href="#">Help</a>
                </p>
                <p class="sidebar-item left-item ">
                    <a href="#">Report A Problem</a>
                </p>
                <p class="sidebar-item left-item ">
                    <a href="#">Contact Us</a>
                </p>
            </div>
        </div>
    </div>
    <!--        <a href="Post.html"> </a>-->
    <div class="main-content" aria-label="Main Content">
        <button id="toggle-columns" aria-label="Toggle Columns Button">Toggle Columns</button>
        <div class="posts-container" aria-label="Posts Container">
            <!--Filled by get_posts and index.js -->
        </div>
    </div>
    <!-- display the admin bar if admin    -->
    <?php if (isset($_SESSION['isAdmin']) && ($_SESSION['isAdmin'] == 1)) { ?>
    <div class="right" aria-label="Admin Section">
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
                    <button type="submit" class="btn btn-primary" aria-label="Search Button">Search</button>
                </form>
            </div>
            <div class="card-body">
                <form action="../processing/addcourse.php" method="POST" id="add-course" class="d-flex flex-column">
                    <div class="mb-3">
                        <div class="mb-3">
                            <label for="course-to-add" class="form-label" id="course-add-label">Enter
                                Course Name</label>
                        </div>
                        <div class="mb-3">
                            <input class="form-control" id="course-to-add" type="search" placeholder="Name"
                                   name="course-to-add" required>
                        </div>

                        <div class="mb-3">
                            <label for="departmentSelect" class="form-label" id="departmentLabel">Select Department</label>
                            <select class="form-select" id="departmentSelect" name="department" aria-label="Department Selection">
                                <?php
                                $conn = connectToDB();
                                // Check connection
                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
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
                    </div>
                    <button type="submit" class="btn btn-primary" aria-label="Add Course Button">Add</button>
                </form>
            </div>
            <!-- This form is for removing courses -->
            <div class="card-body">
                    <form action="javascript:void(0);" method="POST" id="remove-course"
                          class="d-flex flex-column">
                        <div class="mb-3">
                            <div class="mb-3">
                                <label for="course-to-remove" class="form-label" id="course-remove-label">Remove Course</label>
                            </div>
                            <div class="mb-3">
                                <input class="form-control" id="course-to-remove" type="search" placeholder="Search Course"
                                       name="course-to-remove" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" aria-label="Search Course Button">Search</button>
                    </form>
                </div>
        </div>
        <?php } ?>

        <?php if (isset($_SESSION['userID']) && ($_SESSION['isAdmin'] === 0)) { ?>

            <!-- Join course bar -->
            <div class="right" aria-label="Join Course Section">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Join a Course</h5>
                    </div>
                    <div class="card-body">
                        <form action="javascript:void(0);" method="POST" id="join-course-form"
                              class="d-flex flex-column">
                            <div class="mb-3">
                                <label for="course-search-bar" class="form-label">Search a Course</label>
                            </div>
                            <div class="mb-3">
                                <input class="form-control" id="course-search-bar" type="search" placeholder="Search"
                                       name="courseName">
                            </div>
                            <button type="submit" class="btn btn-primary" aria-label="Search Course Button">Search</button>
                        </form>
                    </div>
                    <?php
                    $userid = $_SESSION['userID'];
                    try {
                        $query = "SELECT uc.courseID, name FROM UserCourse AS uc JOIN Course AS c ON uc.courseID = c.courseID WHERE uc.userID = ?;";

                        $conn = connectToDB();
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $userid);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $coursesResult = $result->fetch_all(MYSQLI_ASSOC);
                    } catch (Exception $e) {
                        // Handle exception
                        echo json_encode(array("error" => $e->getMessage(), "redirect" => "$referrer"));
                    }
                    ?>

                    <?php if (count($coursesResult) > 0) { ?>
                        <div class="card-header">
                            <h5 class="card-title mb-0">Your Courses</h5>
                        </div>
                        <!-- Display the courses this user is a part of -->
                        <div class="card-body">

                            <?php
                            foreach ($coursesResult as $course) {
                                // access individual comment data
                                $cid = $course['courseID'];
                                $cname = $course['name'];
                                // output the comment data
                                ?>
                                <div class="course-container" aria-label="Course: <?php echo $cname; ?>">
                                    <a href="../pages/course.php?cid=<?php echo $cid; ?>" class="course-link">
                                        <?php echo $cname; ?>
                                    </a>
                                </div>
                                <?php
                            } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
</main>

<!-- Modal to display users when admin searches for them -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Search Results</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userModalBody" aria-label="User Search Results">
                <!-- Users will be populated here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close Button">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal to display courses when user searches for them or when admin wants to remove -->
<div class="modal fade" id="course-modal" tabindex="-1" aria-labelledby="course-modal-label">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="course-modal-label">Search Results</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="course-modal-body" aria-label="Course Search Results">
                <!-- Courses will be populated here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close Button">Close</button>
            </div>
        </div>
    </div>
</div>

<?php require_once ('../includes/footer.php'); ?>
