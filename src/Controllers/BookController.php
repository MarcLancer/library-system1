<?php

namespace LibrarySystem\Controllers;

use LibrarySystem\Models\Book;

class BookController {
    private Book $book;

    public function __construct(Book $book) {
        $this->book = $book;
    }

    public function index(): void {
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? 20;
        $filters = [
            'category_id' => $_GET['category'] ?? null,
            'search' => $_GET['search'] ?? null,
            'status' => $_GET['status'] ?? null
        ];

        $books = $this->book->getAll($page, $perPage, $filters);
        $this->jsonResponse($books);
    }

    public function show($id): void {
        $book = $this->book->getById($id);
        if (!$book) {
            $this->jsonResponse(['error' => 'Book not found'], 404);
            return;
        }
        $this->jsonResponse($book);
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $bookId = $this->book->create($data);
            $this->jsonResponse(['message' => 'Book created', 'id' => $bookId], 201);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function search(): void {
        $query = $_GET['q'] ?? '';
        if (strlen($query) < 2) {
            $this->jsonResponse(['error' => 'Query too short'], 400);
            return;
        }
        $results = $this->book->search($query);
        $this->jsonResponse($results);
    }

    private function jsonResponse($data, $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
