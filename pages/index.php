<?php require_once('../includes/header.php'); ?>
<link href="../css/index.css" rel="stylesheet">
<script src="../js/index.js"></script>
</head>
<body>
<?php require_once('../includes/nav.php'); ?>
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
    <!--        <a href="Post.html"> </a>-->
    <div class="main-content">
        <button id="toggle-columns">Toggle Columns</button>
        <div class="posts-container">
            <!--Filled by get_posts and index.js -->
        </div>
    </div>
    <!-- display the admin bar if admin    -->
    <?php if (isset($_SESSION['isAdmin']) && ($_SESSION['isAdmin'] == 1)) { ?>
        <div class="right">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Admin Controls</h5>
                </div>
                <div class="card-body">
                    <form action="javascript:void(0);" method="POST" id="admin-search-form" class="d-flex flex-column">
                        <div class="mb-3">
                            <label for="userInformationAdminSelect" class="form-label" id="departmentLabel">Search For User By</label>
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
</main>

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

<?php require_once('../includes/footer.php'); ?>