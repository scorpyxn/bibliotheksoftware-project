<?php
session_start();

// destroy session
session_destroy();

// redirect to index page
header("Location: index.php");
exit;
