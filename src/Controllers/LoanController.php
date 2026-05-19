<?php

namespace LibrarySystem\Controllers;

use LibrarySystem\Core\Database;
use LibrarySystem\Models\Loan;

class LoanController {
    private Loan $loanModel;

    public function __construct(Database $db) {
        $this->loanModel = new Loan($db);
    }

    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $this->loanModel->create($data['book_id'], $data['user_id'], $data['due_days'] ?? 14);
        return json_encode($result);
    }

    public function returnBook() {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $this->loanModel->returnBook($data['loan_id']);
        return json_encode($result);
    }

    public function getActive($userId) {
        $loans = $this->loanModel->getActive($userId);
        return json_encode(['data' => $loans]);
    }

    public function getHistory($userId, $page = 1) {
        $history = $this->loanModel->getHistory($userId, $page);
        return json_encode($history);
    }

    public function renew() {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $this->loanModel->renew($data['loan_id'], $data['due_days'] ?? 14);
        return json_encode($result);
    }
}
