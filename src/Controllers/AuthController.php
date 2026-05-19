<?php

namespace LibrarySystem\Controllers;

use LibrarySystem\Core\Auth;

class AuthController {
    private Auth $auth;

    public function __construct(Auth $auth) {
        $this->auth = $auth;
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $result = $this->auth->register(
            $data['username'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['first_name'] ?? '',
            $data['last_name'] ?? ''
        );

        $this->jsonResponse($result, $result['success'] ? 201 : 400);
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $result = $this->auth->login(
            $data['email'] ?? '',
            $data['password'] ?? ''
        );

        $this->jsonResponse($result, $result['success'] ? 200 : 401);
    }

    public function logout(): void {
        $this->jsonResponse(['message' => 'Logged out successfully'], 200);
    }

    private function jsonResponse($data, $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
