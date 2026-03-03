<?php
/**
 * Shared Includes - Frontend
 */
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/essentials.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}