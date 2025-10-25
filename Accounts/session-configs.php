<?php
// Prevent session errors on hosting
if (session_status() === PHP_SESSION_NONE) {
    // Configure session settings for hosting environment
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 1); 
    ini_set('session.cookie_samesite', 'Lax');
    
    session_start();
}
?>