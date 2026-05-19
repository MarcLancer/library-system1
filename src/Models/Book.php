<?php

namespace LibrarySystem\Models;

use LibrarySystem\Core\Database;

class Book {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAll($page = 1, $perPage = 20, $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $sql = 'SELECT b.*, c.name as category_name, p.name as publisher_name FROM books b 
                LEFT JOIN categories c ON b.category_id = c.id 
                LEFT JOIN publishers p ON b.publisher_id = p.id 
                WHERE 1=1';
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= ' AND b.category_id = ?';
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND (b.title LIKE ? OR b.isbn LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND b.status = ?';
            $params[] = $filters['status'];
        }

        $sql .= ' ORDER BY b.created_at DESC LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = $offset;

        $books = $this->db->query($sql, $params);
        $total = $this->getTotal($filters);

        return [
            'data' => $books,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'pages' => ceil($total / $perPage)
        ];
    }

    public function getById($id): ?array {
        $book = $this->db->queryOne(
            'SELECT b.*, c.name as category_name, p.name as publisher_name FROM books b 
             LEFT JOIN categories c ON b.category_id = c.id 
             LEFT JOIN publishers p ON b.publisher_id = p.id 
             WHERE b.id = ?',
            [$id]
        );

        if (!$book) return null;

        $authors = $this->db->query(
            'SELECT a.* FROM authors a 
             INNER JOIN book_authors ba ON a.id = ba.author_id 
             WHERE ba.book_id = ?',
            [$id]
        );
        $book['authors'] = $authors;

        return $book;
    }

    public function create($data): int {
        return $this->db->insert('books', $data);
    }

    public function update($id, $data): int {
        return $this->db->update('books', $data, 'id = ?', [$id]);
    }

    public function delete($id): int {
        return $this->db->delete('books', 'id = ?', [$id]);
    }

    public function assignAuthor($bookId, $authorId): void {
        $this->db->insert('book_authors', [
            'book_id' => $bookId,
            'author_id' => $authorId
        ]);
    }

    public function removeAuthor($bookId, $authorId): int {
        return $this->db->delete(
            'book_authors',
            'book_id = ? AND author_id = ?',
            [$bookId, $authorId]
        );
    }

    public function getAvailableBooks($limit = 10): array {
        return $this->db->query(
            'SELECT * FROM books WHERE available_copies > 0 AND status = "available" 
             ORDER BY created_at DESC LIMIT ?',
            [$limit]
        );
    }

    public function search($query): array {
        $searchTerm = '%' . $query . '%';
        return $this->db->query(
            'SELECT b.*, c.name as category_name FROM books b 
             LEFT JOIN categories c ON b.category_id = c.id 
             WHERE b.title LIKE ? OR b.isbn LIKE ? OR b.description LIKE ? 
             ORDER BY b.title ASC',
            [$searchTerm, $searchTerm, $searchTerm]
        );
    }

    private function getTotal($filters = []): int {
        $sql = 'SELECT COUNT(*) as total FROM books WHERE 1=1';
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= ' AND category_id = ?';
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND (title LIKE ? OR isbn LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND status = ?';
            $params[] = $filters['status'];
        }

        $result = $this->db->queryOne($sql, $params);
        return $result['total'] ?? 0;
    }
}
