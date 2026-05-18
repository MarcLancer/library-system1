# Library Management System

A comprehensive fullstack library management system built with PHP, featuring user authentication, book management, loans, reservations, reviews, and fines tracking.

## 🎯 Features

### User Management
- User registration and authentication with JWT tokens
- Role-based access control (Admin, Librarian, Member)
- User profiles with contact information
- Account status management (active, inactive, suspended)

### Book Management
- Browse and search books by title, ISBN, or category
- Comprehensive book information (author, publisher, pages, language)
- Book cover images and detailed descriptions
- Category-based organization
- Author and publisher management
- Stock management with available copies tracking

### Loan Management
- Borrow books with configurable due dates
- Return books with automatic fine calculation
- Loan history tracking
- Automatic overdue detection
- Loan renewal functionality
- Overdue notification system

### Reservation System
- Reserve unavailable books
- Automatic expiration of reservations
- Ready-for-pickup notifications
- Per-user reservation limits

### Review & Rating System
- Leave reviews and ratings for borrowed books
- Moderation system for reviews
- Average rating calculation
- Verified borrower reviews

### Fine Management
- Automatic fine calculation for overdue returns
- Configurable fine amounts
- Fine payment tracking
- Fine waiver functionality
- Outstanding balance tracking

### Additional Features
- Activity logging for audit trails
- Notification system
- Advanced search and filtering
- Responsive design
- RESTful API endpoints

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Setup Steps

1. Clone the repository:
```bash
git clone https://github.com/MarcLancer/library-system1.git
cd library-system1
```

2. Configure environment variables:
```bash
cp .env.example .env
# Edit .env with your database credentials
```

3. Create the database and run migrations:
```bash
mysql -u root -p < database/schema.sql
```

4. Set up web server to point to `public/` directory

5. Start PHP server:
```bash
php -S localhost:8000 -t public/
```

6. Access the application at `http://localhost:8000`

## API Documentation

### Authentication

**Register User**
```
POST /api/auth/register
Content-Type: application/json

{
  "username": "john_doe",
  "email": "john@example.com",
  "password": "password123",
  "first_name": "John",
  "last_name": "Doe"
}
```

**Login**
```
POST /api/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

### Books

**Get All Books**
```
GET /api/books?page=1&per_page=20&category=1&search=query
```

**Get Book Details**
```
GET /api/books/{id}
```

**Search Books**
```
GET /api/books/search?q=search_term
```

### Loans

**Create Loan**
```
POST /api/loans
Content-Type: application/json

{
  "book_id": 1,
  "user_id": 1,
  "due_days": 14
}
```

**Return Book**
```
POST /api/loans/return
Content-Type: application/json

{
  "loan_id": 1
}
```

## Database Schema

### Core Tables
- **users** - User accounts and profiles
- **books** - Book catalog
- **categories** - Book categories
- **authors** - Author information
- **publishers** - Publisher information
- **book_authors** - Many-to-many relationship
- **loans** - Loan records
- **reservations** - Book reservations
- **reviews** - Book reviews and ratings
- **fines** - Fine records
- **notifications** - User notifications
- **activity_logs** - System activity logs

## Project Structure

```
library-system1/
├── bootstrap/
│   └── app.php
├── config/
│   ├── app.php
│   └── database.php
├── database/
│   └── schema.sql
├── src/
│   ├── Core/
│   │   ├── Auth.php
│   │   └── Database.php
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── BookController.php
│   │   └── LoanController.php
│   └── Models/
│       ├── Book.php
│       ├── Loan.php
│       ├── User.php
│       ├── Reservation.php
│       ├── Review.php
│       └── Fine.php
├── public/
│   ├── index.php
│   ├── index.html
│   └── assets/
│       ├── css/
│       │   └── style.css
│       └── js/
│           └── app.js
└── README.md
```

## Security Features

- Password hashing with bcrypt
- JWT token-based authentication
- SQL injection prevention with prepared statements
- CORS protection
- Input validation and sanitization
- Activity logging for audit trails
- Role-based access control

## Future Enhancements

- Advanced search filters
- PDF report generation
- Email notifications
- Mobile app
- Book recommendations
- Wishlist functionality
- Library analytics dashboard

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

MIT License

## Support

For issues and questions, please create an issue on GitHub.

## Author

**MarcLancer** - [GitHub Profile](https://github.com/MarcLancer)
