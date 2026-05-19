<?php

namespace LibrarySystem\Models;

use LibrarySystem\Core\Database;
use DateTime;

class Loan {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function create($bookId, $userId, $dueDays = 14): array {
        // Check if book is available
        $book = $this->db->queryOne('SELECT * FROM books WHERE id = ?', [$bookId]);
        if (!$book || $book['available_copies'] <= 0) {
            return ['success' => false, 'message' => 'Book not available'];
        }

        // Calculate due date
        $dueDate = (new DateTime())->modify("+{$dueDays} days")->format('Y-m-d');

        try {
            $loanId = $this->db->insert('loans', [
                'book_id' => $bookId,
                'user_id' => $userId,
                'due_date' => $dueDate,
                'status' => 'active'
            ]);

            // Update available copies
            $this->db->update('books', ['available_copies' => $book['available_copies'] - 1], 'id = ?', [$bookId]);

            return ['success' => true, 'loan_id' => $loanId];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function returnBook($loanId): array {
        $loan = $this->db->queryOne('SELECT * FROM loans WHERE id = ?', [$loanId]);
        if (!$loan) {
            return ['success' => false, 'message' => 'Loan not found'];
        }

        try {
            $this->db->update('loans', 
                ['return_date' => date('Y-m-d'), 'status' => 'returned'], 
                'id = ?', 
                [$loanId]
            );

            // Update available copies
            $book = $this->db->queryOne('SELECT * FROM books WHERE id = ?', [$loan['book_id']]);
            $this->db->update('books', 
                ['available_copies' => $book['available_copies'] + 1], 
                'id = ?', 
                [$loan['book_id']]
            );

            // Check for overdue fines
            if ($loan['due_date'] < date('Y-m-d')) {
                $days = (new DateTime($loan['due_date']))->diff(new DateTime())->days;
                $fineAmount = $days * 0.50; // $0.50 per day
                $this->db->insert('fines', [
                    'loan_id' => $loanId,
                    'user_id' => $loan['user_id'],
                    'amount' => $fineAmount,
                    'reason' => 'Overdue book return',
                    'status' => 'unpaid'
                ]);
            }

            return ['success' => true, 'message' => 'Book returned successfully'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getActive($userId): array {
        return $this->db->query(
            'SELECT l.*, b.title, b.isbn FROM loans l 
             INNER JOIN books b ON l.book_id = b.id 
             WHERE l.user_id = ? AND l.status = "active" 
             ORDER BY l.due_date ASC',
            [$userId]
        );
    }

    public function getOverdue(): array {
        return $this->db->query(
            'SELECT l.*, b.title, u.email, u.first_name, u.last_name FROM loans l 
             INNER JOIN books b ON l.book_id = b.id 
             INNER JOIN users u ON l.user_id = u.id 
             WHERE l.status = "active" AND l.due_date < NOW() 
             ORDER BY l.due_date ASC'
        );
    }

    public function getHistory($userId, $page = 1, $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        $loans = $this->db->query(
            'SELECT l.*, b.title, b.isbn FROM loans l 
             INNER JOIN books b ON l.book_id = b.id 
             WHERE l.user_id = ? 
             ORDER BY l.created_at DESC 
             LIMIT ? OFFSET ?',
            [$userId, $perPage, $offset]
        );

        $total = $this->db->queryOne(
            'SELECT COUNT(*) as count FROM loans WHERE user_id = ?',
            [$userId]
        )['count'];

        return [
            'data' => $loans,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    public function renew($loanId, $dueDays = 14): array {
        $loan = $this->db->queryOne('SELECT * FROM loans WHERE id = ?', [$loanId]);
        if (!$loan || $loan['status'] !== 'active') {
            return ['success' => false, 'message' => 'Loan cannot be renewed'];
        }

        $newDueDate = (new DateTime($loan['due_date']))->modify("+{$dueDays} days")->format('Y-m-d');
        $this->db->update('loans', ['due_date' => $newDueDate], 'id = ?', [$loanId]);

        return ['success' => true, 'message' => 'Loan renewed successfully'];
    }
}
