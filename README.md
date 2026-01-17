# Laravel Technical Assessment System

A comprehensive web application for conducting technical assessments with multiple programming languages, real-time scoring, and resume upload functionality.

## 🚀 Features

- **Multi-Language Assessment**: Support for JavaScript, Python, and more
- **Dynamic Question Selection**: Random questions based on selected languages
- **Real-Time Progress Tracking**: Save and restore partial progress
- **Anti-Cheat Timer System**: Server-side timer prevents manipulation
- **Score Analytics**: Detailed performance breakdown
- **Conditional Resume Upload**: File upload for passing candidates
- **Mobile Responsive**: Works seamlessly on all devices
- **Vue.js + Inertia.js**: Modern SPA experience with Laravel backend

## 🛠️ Tech Stack

- **Backend**: Laravel 12, PHP 8.5
- **Frontend**: Vue.js 3, Inertia.js v2
- **Styling**: Tailwind CSS v4
- **Database**: SQLite (configurable)
- **Testing**: Pest v4
- **Code Quality**: Laravel Pint (PSR-12)

## 📋 Quick Setup

### Prerequisites
- PHP 8.5+
- Composer
- Node.js & npm

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd candidate-assessment
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate --seed
   ```

5. **Build assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

6. **Start the server**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to access the application.

## 📊 Sample Questions Database

The application includes comprehensive sample questions for quick evaluation and testing.

### Option 1: Using Seeder (Recommended)
```bash
php artisan migrate:fresh --seed
```

### Option 2: Using JSON Import
```bash
php artisan questions:import
# or specify custom file
php artisan questions:import --file=path/to/questions.json
```

### Sample Questions Structure

The `database/sample-questions.json` file contains:
- **24 total questions** (12 JavaScript + 12 Python)
- **Multiple choice format** with 4 options each
- **Explanations** for learning purposes
- **Difficulty range**: Beginner to Intermediate

#### JavaScript Topics Covered:
- Type coercion and operators
- Functions and arrow functions
- Arrays and methods
- Strict mode and best practices
- Data types and primitives

#### Python Topics Covered:
- Function definitions and syntax
- Data types and mutability
- Operators and expressions
- Exception handling
- Built-in functions and methods

### JSON Structure Example:
```json
{
  "languages": [
    {
      "name": "JavaScript",
      "description": "A versatile programming language..."
    }
  ],
  "questions": {
    "JavaScript": [
      {
        "question": "What is the output of 1 + \"1\" in JavaScript?",
        "options": ["2", "11", "NaN", "undefined"],
        "correctAnswer": "11",
        "explanation": "JavaScript performs type coercion..."
      }
    ]
  }
}
```

## 🗄️ Database Schema

The application uses a normalized database design with three main tables:

### Languages Table
```sql
- id (Primary Key)
- name (Unique)
- description (Optional)
- timestamps
```

### Questions Table
```sql
- id (Primary Key)
- language_id (Foreign Key)
- question_text (Text)
- options (JSON Array)
- correct_answer (String)
- timestamps
```

### Assessments Table
```sql
- id (Primary Key)
- candidate_name (String)
- candidate_email (String)
- score (Integer 0-100)
- resume_path (String, Nullable)
- timestamps
```

## 🎯 Assessment Flow

1. **Language Selection**: Choose programming languages
2. **Assessment**: Answer randomized questions with timer
3. **Auto-Save**: Progress saved automatically
4. **Results**: View score and detailed analytics
5. **Resume Upload**: Available for passing scores (≥50%)

## 🔒 Security Features

- **Server-Side Timer**: Prevents client-side manipulation
- **File Validation**: Resume uploads limited to PDF, DOC, DOCX
- **Session Management**: Secure progress tracking
- **CSRF Protection**: Laravel's built-in security
- **Input Validation**: Comprehensive form validation

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

For specific test files:
```bash
php artisan test tests/Feature/AssessmentTest.php
```

## 📝 Code Quality

The codebase follows PSR-12 standards with comprehensive documentation:

```bash
# Format code
vendor/bin/pint

# Check specific files
vendor/bin/pint app/Http/Controllers/
```

## 🚀 Deployment

### Production Setup
1. Set `APP_ENV=production` in `.env`
2. Configure database connection
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `npm run build`

### Environment Variables
```env
APP_NAME="Laravel Assessment"
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite
```

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/AssessmentController.php
│   ├── Models/
│   │   ├── Assessment.php
│   │   ├── Language.php
│   │   └── Question.php
│   └── Console/Commands/ImportQuestionsFromJson.php
├── database/
│   ├── migrations/
│   ├── seeders/QuestionSeeder.php
│   └── sample-questions.json
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── Welcome.vue
│   │   │   ├── Assessment.vue
│   │   │   └── Result.vue
│   │   └── Components/
│   └── css/app.css
└── routes/web.php
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Follow PSR-12 coding standards
4. Add tests for new features
5. Submit a pull request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🔧 Development Commands

```bash
# Start development server
php artisan serve

# Watch for file changes
npm run dev

# Run tests
php artisan test

# Fresh database with sample data
php artisan migrate:fresh --seed

# Import questions from JSON
php artisan questions:import

# Format code
vendor/bin/pint
```

## 📞 Support

For questions or issues, please create an issue in the repository or contact the development team.

---

**Built with ❤️ using Laravel, Vue.js, and modern web technologies.**
