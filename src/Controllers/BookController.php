<?php

namespace LibrarySystem\Controllers;

use LibrarySystem\Models\Book;

class BookController {
    private Book $bookModel;

    public function __construct(Book $bookModel) {
        $this->bookModel = $bookModel;
    }

    public function getAll() {
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? 20;
        $filters = [
            'search' => $_GET['search'] ?? '',
            'category_id' => $_GET['category_id'] ?? '',
            'status' => $_GET['status'] ?? 'available'
        ];

        $result = $this->bookModel->getAll($page, $perPage, $filters);
        return json_encode($result);
    }

    public function getById($id) {
        $book = $this->bookModel->getById($id);
        
        if (!$book) {
            http_response_code(404);
            return json_encode(['success' => false, 'message' => 'Book not found']);
        }

        return json_encode(['success' => true, 'data' => $book]);
    }

    public function create() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            $id = $this->bookModel->create($input);
            return json_encode(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update($id) {
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            $this->bookModel->update($id, $input);
            return json_encode(['success' => true, 'message' => 'Book updated']);
        } catch (\Exception $e) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function delete($id) {
        try {
            $this->bookModel->delete($id);
            return json_encode(['success' => true, 'message' => 'Book deleted']);
        } catch (\Exception $e) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function search() {
        $query = $_GET['q'] ?? '';
        
        if (empty($query)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Search query required']);
        }

        $results = $this->bookModel->search($query);
        return json_encode(['success' => true, 'data' => $results]);
    }
}
