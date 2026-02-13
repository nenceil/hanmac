<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'id';

// Load language file
if ($current_lang == 'en') {
    include_once 'lang_en.php';
} else {
    include_once 'lang_id.php';
}

// Function to get translated text
function __($key) {
    global $lang;
    return isset($lang[$key]) ? $lang[$key] : $key;
}
