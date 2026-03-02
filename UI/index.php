<?php
session_start();

/*
|--------------------------------------------------------------------------
| LOAD CORE FILES
|--------------------------------------------------------------------------
*/

// Load Database
require_once __DIR__ . '/../app/config/database.php';

// Load Router
require_once __DIR__ . '/../routers/web.php';