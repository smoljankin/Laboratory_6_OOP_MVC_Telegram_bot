<?php 

namespace App\Core;

class SessionManager {
    public function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public function terminate() {
        $_SESSION = [];
        session_destroy();
    }
}
