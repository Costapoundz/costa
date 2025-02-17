<?php
// Start the session
session_start();

// Destroy all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect the user to the index page or homepage
header("Location: ../index.html"); // Replace 'index.php' with your desired location
exit;
?>


<script>
    window.history.forward(); // Prevents going back immediately

    function preventBack() {
        window.history.forward();
    }

    setTimeout(preventBack, 0);

    window.onunload = function () {
        window.history.forward();
    };
</script>
