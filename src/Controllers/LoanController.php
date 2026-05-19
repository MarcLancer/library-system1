<?php

namespace LibrarySystem\Controllers;

use LibrarySystem\Models\Loan;

class LoanController {
    private Loan $loanModel;

    public function __construct(Loan $loanModel) {
        $this->loanModel = $loanModel;
    }

    public function create() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['book_id']) || empty($input['user_id'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Missing required fields']);
        }

        $result = $this->loanModel->create($input['book_id'], $input['user_id']);
        return json_encode($result);
    }

    public function returnBook($id) {
        $result = $this->loanModel->returnBook($id);
        return json_encode($result);
    }

    public function getActive($userId) {
        $loans = $this->loanModel->getActive($userId);
        return json_encode(['success' => true, 'data' => $loans]);
    }

    public function getHistory($userId) {
        $page = $_GET['page'] ?? 1;
        $result = $this->loanModel->getHistory($userId, $page);
        return json_encode(['success' => true, 'data' => $result]);
    }

    public function renew($id) {
        $result = $this->loanModel->renew($id);
        return json_encode($result);
    }
}
