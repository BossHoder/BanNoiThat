function isAdmin(){
    return isset($_SESSION['perm']) && $_SESSION['perm'] == 1;
}

function isAllowedPage($pageName) {
    // Array of allowed pages for regular users
    $allowedPages = ['index.php', 'cart.php', 'signin.php', 'signup.php', 'checkout.php', 'logout.php', 'menu.php', 'signupprocess.php', 'signup.php', 'ViewByType.php', 'login_process.php']; // Add your allowed pages

    // Admins have access to all pages
    if (isAdmin()) {
        return true;
    }

    // Check if the current page is in the allowed list
    return in_array($pageName, $allowedPages);
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.btn-submit').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default form submission
        
        // Show loading popup
        const popup = document.getElementById('popup');
        showPopup('Loading...', false); // Show loading popup

        setTimeout(() => {
            // Simulate success/error - replace with your actual logic
            const isSuccess = Math.random() < 0.8; // 80% chance of success

            if (isSuccess) {
                showPopup("You're successful!", true);
                // Redirect or perform other actions on success
                document.forms[0].submit(); //submit the form after success popup
            } else {
                showPopup("An error occurred.", false);
            }
        }, 2000);
    });
});

function showPopup(message, isSuccess) {
    const popup = document.getElementById('popup');
    const popupMessage = document.getElementById('popup-message');
    popupMessage.textContent = message;

    popup.classList.add(isSuccess ? 'success' : 'error');
    popup.style.display = 'flex'; // Ensure popup is shown as flexbox

    setTimeout(() => {
        popup.style.display = 'none'; // Hide popup after 2 seconds
        popup.classList.remove('success', 'error'); // Remove both classes
    }, 2000);
}
