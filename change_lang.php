<?php
session_start();

if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    if (in_array($lang, ['id', 'en'])) {
        $_SESSION['lang'] = $lang;
    }
}

// Redirect back to the previous page or home
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
header("Location: " . $referer);
exit;
