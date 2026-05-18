<?php

namespace LibrarySystem\Core;

class Auth {
    private Database $db;
    private string $jwtSecret;
    private int $jwtExpiration;

    public function __construct(Database $db, $jwtSecret, $jwtExpiration = 86400) {
        $this->db = $db;
        $this->jwtSecret = $jwtSecret;
        $this->jwtExpiration = $jwtExpiration;
    }

    public function register($username, $email, $password, $firstName = '', $lastName = ''): array {
        if ($this->userExists($email, $username)) {
            return ['success' => false, 'message' => 'User already exists'];
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $data = [
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role' => 'member',
            'status' => 'active'
        ];

        try {
            $userId = $this->db->insert('users', $data);
            return ['success' => true, 'user_id' => $userId, 'message' => 'User registered successfully'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function login($email, $password): array {
        $user = $this->db->queryOne('SELECT * FROM users WHERE email = ? OR username = ?', [$email, $email]);

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account is not active'];
        }

        $token = $this->generateJWT($user);
        return [
            'success' => true,
            'token' => $token,
            'user' => $this->sanitizeUser($user)
        ];
    }

    public function verifyToken($token): ?array {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return null;
            }

            list($header, $payload, $signature) = $parts;
            $expectedSignature = hash_hmac('sha256', "$header.$payload", $this->jwtSecret, true);
            $expectedSignature = rtrim(strtr(base64_encode($expectedSignature), '+/', '-_'), '=');

            if (!hash_equals($signature, $expectedSignature)) {
                return null;
            }

            $decodedPayload = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
            if ($decodedPayload['exp'] < time()) {
                return null;
            }

            return $decodedPayload;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function generateJWT($user): string {
        $now = time();
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'iat' => $now,
            'exp' => $now + $this->jwtExpiration
        ]);

        $encodedHeader = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
        $encodedPayload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
        $signature = hash_hmac('sha256', "$encodedHeader.$encodedPayload", $this->jwtSecret, true);
        $encodedSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        return "$encodedHeader.$encodedPayload.$encodedSignature";
    }

    private function userExists($email, $username): bool {
        $user = $this->db->queryOne(
            'SELECT id FROM users WHERE email = ? OR username = ?',
            [$email, $username]
        );
        return $user !== null;
    }

    private function sanitizeUser($user): array {
        unset($user['password']);
        return $user;
    }
}
