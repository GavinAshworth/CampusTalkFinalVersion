document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('loginform').addEventListener('submit', submission);

    const logoutButton = document.getElementById("logout");
    if (logoutButton) {
        // Logout button click event
        document.getElementById("logout").addEventListener("click", function (e) {
            e.preventDefault();
            // Make an AJAX request to logout.php
            fetch('../processing/logout.php', {
                method: 'GET',
            })
                .then(response => {
                    // Check if the response is not ok
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    // Since logout.php will redirect, we don't need to parse the response
                    // Handle the redirect directly
                    window.location.href = response.url; // Redirect to the specified URL after logout
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Handle network errors here
                    alert('An error occurred while logging out. Please try again.');
                });
        });
    }


});

function submission(e) {
    resetLabels();

    let password = document.getElementsByName('password')[0].value;
    let username = document.getElementsByName('username')[0].value;

    if (username === "") {    // make sure username is entered
        document.getElementById('usernamelabel').textContent = "Please enter a username";
        document.getElementById('usernamelabel').style.color = "red";
    } else if (password === "") {  // make sure pw is entered
        document.getElementById('passwordlabel').textContent = "Please enter a password";
        document.getElementById('passwordlabel').style.color = "red";
    } else {
        // if both are entered then process the info in php
        sendData();
    }
}
function sendData() {
    let formData = new FormData(document.getElementById("loginform"));

    // Send the form data to the login processing file
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../processing/login.php', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                try {
                    let response = JSON.parse(xhr.responseText);
                    if (response.error === "no result") {
                        resetLabels();
                        document.getElementById('usernamelabel').textContent = "Incorrect username";
                        document.getElementById('usernamelabel').style.color = "red";
                        document.getElementById('passwordlabel').textContent = "Password";
                        document.getElementById('passwordlabel').style.color = "black";
                    } else if (response.error === 'incorrect password') {
                        document.getElementById('usernamelabel').textContent = "Username";
                        document.getElementById('usernamelabel').style.color = "black";
                        document.getElementById('passwordlabel').textContent = "Incorrect password";
                        document.getElementById('passwordlabel').style.color = "red";
                    } else if (response.success) {
                        // Redirect to the previous page upon successful registration
                        window.location.href = response.redirect;
                    }
                } catch (error) {
                    console.log(xhr.responseText);
                    // Handle the case where the response is not valid JSON
                }
            } else {
                console.error('AJAX error: ' + xhr.status + ' - ' + xhr.statusText);
            }
        }
    };
    xhr.send(formData);
}
//this functions resets the labels to username and password on a click so that it's not still displaying the previous error message
function resetLabels() {
    document.getElementById('usernamelabel').textContent = "Username";
    document.getElementById('usernamelabel').style.color = "black";

    document.getElementById('passwordlabel').textContent = "Password";
    document.getElementById('passwordlabel').style.color = "black";
}

// these functions are responsible for display or hidding the login/logout buttons when a user logs in or out
function login() {
    document.getElementById("loginbutton").style.display = 'none';
    document.getElementById("logout").style.display = 'inline';
}
function logout() {
    document.getElementById('loginbutton').style.display = 'block';
    document.getElementById("logout").style.display = 'none';
}