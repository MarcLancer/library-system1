<?php

namespace LibrarySystem\Models;

use LibrarySystem\Core\Database;

class User {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAll($page = 1, $perPage = 20, $role = null): array {
        $offset = ($page - 1) * $perPage;
        $sql = 'SELECT * FROM users WHERE 1=1';
        $params = [];

        if ($role) {
            $sql .= ' AND role = ?';
            $params[] = $role;
        }

        $sql .= ' ORDER BY created_at DESC LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = $offset;

        $users = $this->db->query($sql, $params);
        $total = $this->getTotal($role);

        // Remove passwords
        foreach ($users as &$user) {
            unset($user['password']);
        }

        return [
            'data' => $users,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    public function getById($id): ?array {
        $user = $this->db->queryOne('SELECT * FROM users WHERE id = ?', [$id]);
        if ($user) {
            unset($user['password']);
        }
        return $user;
    }

    public function update($id, $data): int {
        return $this->db->update('users', $data, 'id = ?', [$id]);
    }

    public function updatePassword($id, $newPassword): int {
        return $this->update($id, ['password' => password_hash($newPassword, PASSWORD_BCRYPT)]);
    }

    public function changeStatus($id, $status): int {
        return $this->update($id, ['status' => $status]);
    }

    public function changeRole($id, $role): int {
        return $this->update($id, ['role' => $role]);
    }

    public function getProfile($id): ?array {
        return $this->getById($id);
    }

    private function getTotal($role = null): int {
        $sql = 'SELECT COUNT(*) as total FROM users WHERE 1=1';
        $params = [];

        if ($role) {
            $sql .= ' AND role = ?';
            $params[] = $role;
        }

        $result = $this->db->queryOne($sql, $params);
        return $result['total'] ?? 0;
    }
}
