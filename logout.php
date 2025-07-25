<?php
// logout.php
session_start();
session_unset();
session_destroy();

// Optional: prevent browser from caching previous pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page
header("Location: login.php");
exit();
