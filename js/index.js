document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('toggle-columns').addEventListener('click', function() {
        document.querySelector('.left').classList.toggle('collapsed');
        document.querySelector('.right').classList.toggle('collapsed');
    });
    //display most recent posts on page load. This is for registered and non-registered users
    let urlParams = new URLSearchParams(window.location.search);
    let dataId = urlParams.get('dataId');
    if(dataId) {
        displayPostsOnLoad(dataId, 'none');
    }else{
        let initialPostDepartment = "all"
        displayPostsOnLoad(initialPostDepartment, 'none');
    }


    //display all departments on load
    displayDepartmentsOnLoad();

    if(document.getElementById('admin-search-form')) {
        document.getElementById('admin-search-form').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent the default form submission

            // Fetch the search query from the input field for adminsa
            let searchQuery = document.getElementById('admin-search-bar').value;
            let searchType = document.getElementById('userInformationAdminSelect').value;

            handleAdminSearch(searchQuery, searchType);
        });
    }
});

function handleAdminSearch(query, type) {
    // Display modal based on admin input
    if (query === '') {
        document.getElementById('admin-search-bar').style.setProperty('border-color', 'red', 'important');
    } else {
        document.getElementById('admin-search-bar').style.removeProperty('border-color');

        let xhr = new XMLHttpRequest();
        xhr.open('POST', '../processing/search_users.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                console.log(xhr);
                let responseData = JSON.parse(xhr.responseText);
                displayUsersModal(responseData.users);
            } else {
                console.error('Error searching users:', xhr.statusText);
            }
        };

        xhr.onerror = function() {
            console.error('Error searching users:', xhr.statusText);
        };

        // Send the query and type as POST data
        let postData = `searchTerm=${encodeURIComponent(query)}&searchType=${encodeURIComponent(type)}`;
        xhr.send(postData);
    }
}

function displayUsersModal(users) {
    const modalBody = document.getElementById('userModalBody');

    // Clear previous content
    modalBody.innerHTML = '';

    // Populate modal with fetched users
    users.forEach(user => {
        const userContainer = document.createElement('div');
        userContainer.classList.add('user-container');
        userContainer.style.display = 'flex';
        userContainer.style.alignItems = 'center';
        userContainer.style.margin = '0.25em';

        const userElement = document.createElement('p');
        userElement.textContent = user.username;
        userElement.style.flexGrow = '1';
        userContainer.appendChild(userElement);
        // Check if the user is enabled
        if (user.isEnabled == 1) {
            // If enabled, display disable button
            const disableButton = document.createElement('button');
            disableButton.textContent = 'Disable';
            disableButton.classList.add('btn', 'btn-sm', 'btn-danger', 'disable-btn');
            disableButton.style.marginLeft = '10px';
            disableButton.setAttribute('data-user-id', user.userID);
            disableButton.addEventListener('click', disableUser);
            userContainer.appendChild(disableButton);
        } else {
            // If disabled, display enable button
            const enableButton = document.createElement('button');
            enableButton.textContent = 'Enable';
            enableButton.classList.add('btn', 'btn-sm', 'btn-success', 'enable-btn');
            enableButton.style.marginLeft = '10px';
            enableButton.setAttribute('data-user-id', user.userID);
            enableButton.addEventListener('click', enableUser);
            userContainer.appendChild(enableButton);
        }


        modalBody.appendChild(userContainer);
    });

    // Show the modal
    const userModal = new bootstrap.Modal(document.getElementById('userModal'));
    userModal.show();
}

function enableUser(){
    let userID = this.getAttribute('data-user-id');
    let newIsEnabledValue = 1;
    toggleEnabled(userID,newIsEnabledValue);
}
function disableUser(){
    let userID = this.getAttribute('data-user-id');
    let newIsEnabledValue = 0;
    toggleEnabled(userID,newIsEnabledValue);
}
function toggleEnabled(userID, newIsEnabledValue){

        let xhr = new XMLHttpRequest();
        xhr.open('POST', '../processing/enable_disable_user.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            let responseData = JSON.parse(xhr.responseText);

            // Find the button associated with the user ID
            const button = document.querySelector(`button[data-user-id="${userID}"]`);

            if (newIsEnabledValue == 0) { // Enabling
                button.classList.remove('btn-danger', 'disable-btn');
                button.classList.add('btn-success', 'enable-btn');
                button.textContent = 'Enable';
                button.removeEventListener('click', disableUser);
                button.addEventListener('click', enableUser);
            } else { // Disabling
                button.classList.remove('btn-success', 'enable-btn');
                button.classList.add('btn-danger', 'disable-btn');
                button.textContent = 'Disable';
                button.removeEventListener('click', enableUser);
                button.addEventListener('click', disableUser);
            }

        } else {
            console.error('Error disabling/enabling users', xhr.statusText);
        }
    };

        xhr.onerror = function() {
            console.error('Error disabling/enabling users', xhr.statusText);
        };

        // Send the query and type as POST data
        let enabledData = `userID=${encodeURIComponent(userID)}&newIsEnabledValue=${encodeURIComponent(newIsEnabledValue)}`;
        xhr.send(enabledData);

}
function displayPostsOnLoad(department, key){
    let postsContainer = document.querySelector('.posts-container');
    postsContainer.innerHTML = '';
    // Make an AJAX request to fetch posts data from get_posts.php
    let xhr = new XMLHttpRequest();
    xhr.open('GET', `../processing/get_posts.php?department=${department}&key=${key}`, true);
    xhr.onload = function() {
        console.log(xhr);
        console.log(xhr.responseText);
        if (xhr.status >= 200 && xhr.status < 400) {
            // Parse the JSON response. This will return an array of posts
            let posts = JSON.parse(xhr.responseText);
            
            // Iterate through the posts and display them
            posts.forEach(function(post) {
                // Create HTML elements for post title, content, and image
                let link = document.createElement('div');
                link.classList.add('post-link');

                link.addEventListener('click', function(event) {
                    if (!event.target.classList.contains('like-btn') && !event.target.classList.contains('like-count')) {
                        let postId = post.postID; // Assuming post.postID contains the ID
                        sessionStorage.setItem('postId', postId);
                        // Redirect to post.php
                        window.location.href = 'post.php?postId=' + postId;                    
                    }
                });

                // create a div for username and user image
                let userInfoContainer = document.createElement('div');
                userInfoContainer.classList.add("user-pic-username-container");
                
                // profile pic
                let userProfilePic = document.createElement('img');
                userProfilePic.src = 'data:image/png;base64,' + post.profilepic;

                // username
                let username = document.createElement('h5');
                username.classList.add('usernames');
                username.innerHTML = post.username;
                console.log(post.username);

                if(post.profilepic) userInfoContainer.appendChild(userProfilePic);
                userInfoContainer.appendChild(username);
                

                let commentInput = document.createElement('input');
                commentInput.classList.add('form-control', 'comment-input');
                commentInput.setAttribute('type', 'text');
                commentInput.setAttribute('placeholder', 'Write a comment...');

                let postContainer = document.createElement('div');
                postContainer.classList.add('post-container');
                postContainer.setAttribute('data-id', post.postID);

                let titleElement = document.createElement('h2');
                titleElement.classList.add('post-title');
                titleElement.innerHTML = post.title + " - <span style='font-size: 0.5em;'>" + post.departmentName + "</span>";

                let contentElement = document.createElement('p');
                contentElement.classList.add('post-text');
                contentElement.textContent = post.content;

                let imageContainer = document.createElement('p');
                imageContainer.classList.add('image-container', 'post-img');

                let imageElement = document.createElement('img');
                imageElement.src = 'data:image/png;base64,' + post.postImage;

                let userInputContainer = document.createElement('div');
                userInputContainer.classList.add('user-input-post');


                let likeButton = document.createElement('button');
                likeButton.classList.add('btn', 'btn-primary', 'like-btn');
                likeButton.setAttribute('data-post-id', post.postID);
                likeButton.textContent = 'Like';
                //if the post has alreayd been liked we display that
                if(post.isLiked){
                    likeButton.classList.add('liked');
                }

                let likeCount = document.createElement('span');
                likeCount.classList.add('badge', 'bg-secondary', 'like-count');
                likeCount.textContent = post.numLikes;
                likeButton.appendChild(likeCount);

                likeButton.addEventListener('click', function(event) {
                    let clickedButton = event.currentTarget;
                    let likeCount = clickedButton.querySelector('.like-count');
                    likePost(clickedButton.getAttribute('data-post-id'), likeCount);
                });

                userInputContainer.appendChild(commentInput);

                userInputContainer.appendChild(likeButton);
                if(post.postImage){
                    imageContainer.appendChild(imageElement);
                }

                postContainer.appendChild(userInfoContainer);
                postContainer.appendChild(titleElement);


                postContainer.appendChild(contentElement);
                postContainer.appendChild(imageContainer);
                postContainer.appendChild(userInputContainer);
                link.appendChild(postContainer);
                postsContainer.appendChild(link);
            });
        }    else {
            console.error('Error fetching posts:', xhr.statusText);
        }
    };
    xhr.onerror = function() {
        console.error('Error fetching posts:', xhr.statusText);
    };
    xhr.send();
}


function displayDepartmentsOnLoad(){
    let departmentContainer = document.querySelector('#department-container');
    // Make an AJAX request to fetch posts data from get_posts.php
    let xhr = new XMLHttpRequest();
    xhr.open('GET', '../processing/get_departments.php', true);
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 400) {
            // Parse the JSON response. This will return an array of posts
            let departments = JSON.parse(xhr.responseText);

            // Iterate through the posts and display them
            departments.forEach(function(department) {
                // Create HTML elements for post title, content, and image
                let departmentP = document.createElement('p');
                departmentP.classList.add('sidebar-item', 'left-item', 'department');

                let link = document.createElement('a');
                link.setAttribute('data-id', department.departmentID);
                link.setAttribute('href','#');
                link.textContent = department.departmentName;
                link.onclick = function() {
                    displayPostsOnLoad(this.getAttribute('data-id'), 'none');
                };


                // Append elements to the container
                departmentP.appendChild(link);
                departmentContainer.appendChild(departmentP);
            });
        } else {
            console.error('Error fetching departments:', xhr.statusText);
        }
    };
    xhr.onerror = function() {
        console.error('Error fetching posts:', xhr.statusText);
    };
    xhr.send();
}
function likePost(postId, likeCount) {
    fetch('../processing/like_post.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `postId=${postId}`,
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                // Alert the user to log in
                let loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            } else if (data.isLiked) {
                // Highlight the like button
                highlightLikeButton(postId);
                //increment like count
                let currentLikes = parseInt(likeCount.textContent, 10);
                likeCount.textContent = currentLikes + 1;
            } else {
                // Unhighlight the like button
                unhighlightLikeButton(postId);
                //decrement like count
                let currentLikes = parseInt(likeCount.textContent, 10);
                likeCount.textContent = currentLikes + -1;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while liking the post. Please try again.');
        });
}


function highlightLikeButton(postId) {
    const likeButton = document.querySelector(`.like-btn[data-post-id="${postId}"]`);
    likeButton.classList.add('liked');
}

function unhighlightLikeButton(postId) {
    const likeButton = document.querySelector(`.like-btn[data-post-id="${postId}"]`);
    likeButton.classList.remove('liked');
}
