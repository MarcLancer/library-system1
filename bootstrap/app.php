<?php

use LibrarySystem\Core\Database;
use LibrarySystem\Core\Auth;
use LibrarySystem\Models\Book;
use LibrarySystem\Models\Loan;
use LibrarySystem\Models\User;

$databaseConfig = require __DIR__ . '/../config/database.php';
$appConfig = require __DIR__ . '/../config/app.php';

// Initialize Database
$database = new Database($databaseConfig);

// Initialize Auth
$auth = new Auth(
    $database,
    $appConfig['JWT_SECRET'],
    $appConfig['JWT_EXPIRATION']
);

// Initialize Models
$bookModel = new Book($database);
$loanModel = new Loan($database);
$userModel = new User($database);

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
