<?php
require_once '../../config/db.php';

function isAuthenticated() {
    return isset($_SESSION['user']);
}