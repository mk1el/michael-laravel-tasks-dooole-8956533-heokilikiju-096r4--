Task Management System 

1. How to Run Locally
Prerequisites
PHP (8.1 or higher)
Composer
MySQL (or MariaDB)
Node.js & NPM
Installation Steps
Clone the repository:

Bash
git clone <https://github.com/Mk1el/michael-laravel-tasks-dooole-8956533-heokilikiju-096r4--.git>
cd task-manager
Install dependencies:

Bash
composer install
npm install && npm run build
Configure Environment:

Env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3308
DB_DATABASE=task_api_db
DB_USERNAME=root
DB_PASSWORD=MOSES21
Generate Application Key:

php artisan key:generate
Run Migrations and Seeders:
code
Bash
php artisan migrate --seed
Start the Local Server:
code
Bash
php artisan serve
The application will now be available at http://127.0.0.1:8000.
2. How to Deploy (e.g., Laravel Cloud, Railway, Render)
General Cloud Setup
To deploy this project to any modern cloud provider:
Connect GitHub: Link your repository to the platform (e.g., Railway or Render).
Environment Variables: Add the following "Config Vars" in your provider's dashboard:
APP_KEY: (Generated via php artisan key:generate --show)
APP_ENV: production
DB_CONNECTION: mysql
DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD: (Provided by your cloud database service)
Deployment Command: Set your "Deploy Command" to ensure the database stays up to date:
code
Bash
php artisan migrate --force --seed && php artisan optimize
Specific to Laravel Cloud
Navigate to Settings > Deployments.
Paste php artisan migrate --force --seed && php artisan optimize into the Deploy Commands box.
Ensure a MySQL resource is added via the Environment tab.
3. Example API Requests
The application provides a JSON API at the /api prefix.
Fetch All Tasks
Endpoint: GET /api/tasks
Description: Returns a list of all tasks ordered by priority and due date.
Create a New Task
Endpoint: POST /api/tasks
Body (JSON):
code
JSON
{
  "title": "Complete Project Documentation",
  "description": "Write the README and API docs",
  "priority": "high",
  "due_date": "2024-12-31"
}
Fetch Daily Task Report
Endpoint: GET /api/tasks/report
Query Parameter: date=YYYY-MM-DD
Example: GET /api/tasks/report?date=2024-04-01
Description: Returns tasks specifically scheduled for the provided date.
Important Notes for Reviewers:
Database: This application is configured to use MySQL for production-grade data persistence.
MySQL Compatibility: Queries have been optimized for MySQL/MariaDB (e.g., using whereDate instead of SQLite-specific strftime).