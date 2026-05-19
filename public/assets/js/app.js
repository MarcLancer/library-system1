// API Configuration
const API_BASE_URL = '/api';
const TOKEN_KEY = 'auth_token';

// Utility Functions
const api = {
    async get(endpoint) {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem(TOKEN_KEY)}`
            }
        });
        return response.json();
    },

    async post(endpoint, data) {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem(TOKEN_KEY)}`
            },
            body: JSON.stringify(data)
        });
        return response.json();
    }
};

class LibraryApp {
    constructor() {
        this.currentPage = 1;
        this.perPage = 12;
        this.init();
    }

    init() {
        this.loadBooks();
        this.attachEventListeners();
    }

    async loadBooks(page = 1, filters = {}) {
        try {
            const params = new URLSearchParams({
                page,
                per_page: this.perPage,
                ...filters
            });
            const data = await api.get(`/books?${params}`);
            this.renderBooks(data.data);
            this.renderPagination(data.pages, page);
        } catch (error) {
            console.error('Error loading books:', error);
        }
    }

    renderBooks(books) {
        const container = document.getElementById('booksContainer');
        if (!books || books.length === 0) {
            container.innerHTML = '<p>No books found</p>';
            return;
        }

        container.innerHTML = books.map(book => `
            <div class="card book-card">
                <img src="${book.cover_image || 'https://via.placeholder.com/120x180'}" alt="${book.title}" class="book-cover">
                <div class="book-info">
                    <h3 class="book-title">${book.title}</h3>
                    <p class="book-meta"><strong>Author:</strong> ${book.authors?.map(a => a.name).join(', ') || 'Unknown'}</p>
                    <p class="book-meta"><strong>ISBN:</strong> ${book.isbn}</p>
                    <p class="book-meta"><strong>Category:</strong> ${book.category_name}</p>
                    <div>
                        <span class="badge badge-${book.available_copies > 0 ? 'success' : 'danger'}">
                            ${book.available_copies} available
                        </span>
                    </div>
                    <div style="margin-top: 1rem;">
                        ${book.available_copies > 0 ? 
                            `<button class="btn btn-primary" onclick="app.borrowBook(${book.id})">Borrow</button>` :
                            `<button class="btn btn-warning" onclick="app.reserveBook(${book.id})">Reserve</button>`
                        }
                    </div>
                </div>
            </div>
        `).join('');
    }

    renderPagination(pages, currentPage) {
        const pagination = document.getElementById('pagination');
        if (pages <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `<a href="#" class="page-link ${i === currentPage ? 'active' : ''}" onclick="app.loadBooks(${i}); return false;">${i}</a>`;
        }
        pagination.innerHTML = html;
    }

    async borrowBook(bookId) {
        const userId = this.getCurrentUserId();
        if (!userId) {
            alert('Please login first');
            return;
        }

        try {
            const result = await api.post('/loans', {
                book_id: bookId,
                user_id: userId
            });
            if (result.success) {
                alert('Book borrowed successfully!');
                this.loadBooks(this.currentPage);
            } else {
                alert(result.message || 'Error borrowing book');
            }
        } catch (error) {
            console.error('Error borrowing book:', error);
        }
    }

    async reserveBook(bookId) {
        const userId = this.getCurrentUserId();
        if (!userId) {
            alert('Please login first');
            return;
        }
        alert('Book reservation feature coming soon!');
    }

    attachEventListeners() {
        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const search = document.getElementById('searchInput').value;
                const category = document.getElementById('categoryFilter').value;
                this.loadBooks(1, { search, category });
            });
        }
    }

    getCurrentUserId() {
        // This would typically be decoded from the JWT token
        return localStorage.getItem('user_id');
    }
}

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.app = new LibraryApp();
});
