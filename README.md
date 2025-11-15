# 🍳 CookLens Backend API

**Enterprise-grade Recipe Management & AI-Powered Cooking Assistant API**

Built with Laravel 11 | MySQL | Laravel Sanctum

---

## 📋 About CookLens

CookLens is a modern recipe management platform with AI-powered features including:

- 🔍 **Smart Recipe Search** - Advanced filtering and full-text search
- 🤖 **AI Ingredient Detection** - Upload images to detect ingredients
- 💡 **AI Recipe Suggestions** - Get recipe recommendations based on available ingredients
- 📱 **Social Features** - Likes, comments, favorites, and sharing
- 📅 **Meal Planning** - Weekly meal plans with nutrition tracking
- 👨‍🍳 **User-Generated Content** - Create and share your own recipes
- 💳 **Subscription System** - Premium features with Stripe integration

---

## 🏗️ Architecture

**Clean Architecture with:**
- **Service Layer** - All business logic
- **Repository Pattern** - Database access isolation
- **Feature-based Modules** - Organized by domain
- **API Resources** - Consistent JSON responses
- **Events & Jobs** - Async processing with queues

```
app/
├── Features/       # Feature modules (Auth, Recipe, AI, etc.)
├── Services/       # Business logic layer
├── Repositories/   # Data access layer
├── Models/         # Eloquent models
├── Traits/         # Reusable traits
└── Enums/          # PHP enums
```

---

## 🚀 Tech Stack

- **Framework:** Laravel 11 (PHP 8.2+)
- **Database:** MySQL 8.0+
- **Authentication:** Laravel Sanctum (Token-based)
- **Image Processing:** Intervention Image
- **Caching:** Redis (production)
- **Queue:** Database/Redis
- **Testing:** PHPUnit

---

## 📦 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8.0+
- Node.js & NPM (for asset compilation)

### Setup Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd cooklens_back
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database in `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cooklens_back
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Seed database (optional)**
```bash
php artisan db:seed
```

7. **Start development server**
```bash
php artisan serve
```

API will be available at: `http://localhost:8000/api`

---

## 📚 API Documentation

API documentation is available at `/docs` when running the application.

### Base URL
```
http://localhost:8000/api
```

### Authentication
All protected endpoints require a Bearer token:
```
Authorization: Bearer {your-token}
```

---

## 🗄️ Database Schema

**29 Tables** including:
- Users & Authentication
- Recipes & Ingredients
- AI Features (Suggestions, Image Detection)
- Social Features (Likes, Comments, Favorites)
- Meal Planning
- Subscriptions & Payments
- Analytics & Notifications

See `databseshema.txt` for complete schema documentation.

---

## 🧪 Testing

Run tests with:
```bash
php artisan test
```

---

## 📖 Development Guide

See `STEPS.txt` for the complete development roadmap and checklist.

See `docs/` folder for detailed documentation:
- `DEVELOPMENT_SUMMARY.md` - Project overview
- `DATABASE_VALIDATION_10_10.md` - Database verification

---

## 🔐 Security

- Token-based authentication with Laravel Sanctum
- CORS configuration
- Rate limiting on API endpoints
- Input validation and sanitization
- SQL injection prevention

---

## 📝 License

This project is proprietary software. All rights reserved.

---

## 👥 Team

CookLens Development Team

---

**Built with ❤️ using Laravel**
