<?php
/**
 * IRB Digital System
 * Front Controller
 */

// Enable Error Reporting for development mode
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load routing
require_once __DIR__ . '/../routes/web.php';
