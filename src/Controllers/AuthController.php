<?php

namespace LibrarySystem\Controllers;

use LibrarySystem\Core\Auth;
use LibrarySystem\Models\User;

class AuthController {
    private Auth $auth;
    private User $userModel;

    public function __construct(Auth $auth, User $userModel) {
        $this->auth = $auth;
        $this->userModel = $userModel;
    }

    public function register() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['username']) || empty($input['email']) || empty($input['password'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Missing required fields']);
        }

        $result = $this->auth->register(
            $input['username'],
            $input['email'],
            $input['password'],
            $input['first_name'] ?? '',
            $input['last_name'] ?? ''
        );
        
        return json_encode($result);
    }

    public function login() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['email']) || empty($input['password'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Missing credentials']);
        }

        $result = $this->auth->login($input['email'], $input['password']);
        return json_encode($result);
    }

    public function verify() {
        $headers = getallheaders();
        $token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
        
        if (empty($token)) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Token not provided']);
        }

        $payload = $this->auth->verifyToken($token);
        if (!$payload) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Invalid token']);
        }

        return json_encode(['success' => true, 'user' => $payload]);
    }
}
