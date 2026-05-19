# Library Management System

A comprehensive fullstack library management system built with PHP and vanilla JavaScript.

## Features

### User Management
- User registration and authentication with JWT tokens
- Role-based access control (Admin, Librarian, Member)
- User profile management

### Book Management
- Complete book catalog with search and filtering
- Multiple authors per book
- Book availability tracking
- Category organization
- ISBN management

### Loan Management
- Borrow and return books
- Automatic due date calculation (14 days default)
- Overdue tracking with automatic fine calculation ($0.50/day)
- Loan history and renewal capability

### Advanced Features
- **Reservations**: Reserve unavailable books with auto-expiration
- **Reviews & Ratings**: Leave verified reviews (only for borrowed books)
- **Fine Management**: Automatic overdue fines with payment tracking
- **Activity Logging**: Comprehensive audit trails for all actions
- **Notifications**: Automated notifications for due dates and reservations

## Technology Stack

### Backend
- **PHP 7.4+** - Server-side language
- **MySQL 5.7+** - Database
- **PDO** - Database abstraction
- **JWT** - Token-based authentication

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Responsive design
- **JavaScript (ES6+)** - Dynamic functionality

## Database Schema

### Tables
1. **users** - User accounts and profiles
2. **categories** - Book categories
3. **authors** - Author information
4. **publishers** - Publisher details
5. **books** - Book inventory
6. **book_authors** - Many-to-many relationship
7. **loans** - Borrowing transactions
8. **reservations** - Book reservations
9. **reviews** - User reviews and ratings
10. **fines** - Overdue fines
11. **notifications** - System notifications
12. **activity_logs** - Audit trail

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer (optional)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/MarcLancer/library-system1.git
   cd library-system1
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   ```
   Edit `.env` with your database credentials:
   ```
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=library_system
   DB_USER=root
   DB_PASSWORD=your_password
   JWT_SECRET=your-secret-key
   ```

3. **Create database**
   ```bash
   mysql -u root -p < database/schema.sql
   ```

4. **Start the server**
   ```bash
   php -S localhost:8000 -t public/
   ```

5. **Access the application**
   Open your browser and navigate to: `http://localhost:8000`

## Project Structure

```
library-system1/
├── config/                 # Configuration files
│   ├── app.php            # Application config
│   └── database.php       # Database config
├── src/
│   ├── Core/              # Core classes
│   │   ├── Database.php   # Database wrapper
│   │   └── Auth.php       # Authentication
│   ├── Models/            # Data models
│   │   ├── Book.php
│   │   ├── Loan.php
│   │   ├── User.php
│   │   ├── Reservation.php
│   │   ├── Review.php
│   │   └── Fine.php
│   └── Controllers/       # API controllers
│       ├── AuthController.php
│       ├── BookController.php
│       └── LoanController.php
├── bootstrap/             # Application bootstrap
│   └── app.php
├── database/              # Database migrations
│   └── schema.sql
├── public/                # Public assets
│   ├── index.html         # Homepage
│   └── assets/
│       ├── css/
│       │   └── style.css
│       └── js/
│           └── app.js
├── .env.example           # Environment template
├── .gitignore             # Git ignore rules
└── README.md              # This file
```

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user
- `POST /api/auth/verify` - Verify JWT token

### Books
- `GET /api/books` - List all books
- `GET /api/books/{id}` - Get book details
- `POST /api/books` - Create book (Admin)
- `PUT /api/books/{id}` - Update book (Admin)
- `DELETE /api/books/{id}` - Delete book (Admin)

### Loans
- `GET /api/loans` - Get user's loans
- `POST /api/loans` - Borrow book
- `POST /api/loans/{id}/return` - Return book
- `POST /api/loans/{id}/renew` - Renew loan

### Users
- `GET /api/users` - List users (Admin)
- `GET /api/users/{id}` - Get user profile
- `PUT /api/users/{id}` - Update profile
- `POST /api/users/{id}/password` - Change password

## Security Features

- **Password Hashing**: bcrypt algorithm
- **JWT Tokens**: Secure token-based authentication
- **Prepared Statements**: SQL injection prevention
- **Input Validation**: Server-side validation
- **CORS Protection**: Configurable cross-origin requests
- **Rate Limiting**: API rate limiting (optional)

## Usage Examples

### Register a new user
```javascript
const response = await fetch('/api/auth/register', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        username: 'john_doe',
        email: 'john@example.com',
        password: 'secure_password'
    })
});
```

### Borrow a book
```javascript
const response = await fetch('/api/loans', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({
        book_id: 1,
        user_id: 1
    })
});
```

## Fine Calculation

Overdue fines are automatically calculated:
- **Rate**: $0.50 per day
- **Calculation**: Applied when book is returned after due date
- **Example**: 5 days overdue = $2.50 fine

## Future Enhancements

- [ ] Email notifications
- [ ] Advanced analytics dashboard
- [ ] Mobile app
- [ ] QR code book scanning
- [ ] Wishlist functionality
- [ ] Social sharing features
- [ ] Payment gateway integration
- [ ] Barcode system

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, email support@library-system.com or open an issue on GitHub.

## Authors

- **MarcLancer** - Initial work

## Acknowledgments

- PHP Community
- MySQL Documentation
- Open source contributors
