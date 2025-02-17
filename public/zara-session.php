<?php
function zara_get_cookie_session_id() {
    if (!isset($_COOKIE['zara_session_id'])) {
        $session_id = uniqid('zara_', true);
        setcookie('zara_session_id', $session_id, time() + (86400 * 30), "/"); // 30 days
    } else {
        $session_id = $_COOKIE['zara_session_id'];
    }
    return $session_id;
}