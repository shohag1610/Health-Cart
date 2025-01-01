# Shopping List Application

A Laravel-based application for managing shopping lists, budgets, and sharing them via email.

## Features

-   Manage shopping lists: Add, update, and delete items.
-   Track your budget: Set a limit and calculate total expenses.
-   Mark items as purchased or unpurchased.
-   Share your shopping list via email.
-   Built with Laravel, MySQL,JavaScript and AJAX for seamless user interactions.

## Requirements

-   PHP 8.1 or higher
-   Composer
-   MySQL

## Installation Instructions

### Step 1: Clone the Repository

Clone the repository and navigate to the project directory:

```bash
git clone <https://github.com/shohag1610/Health-Cart.git>
cd <health-cart>
```

### Step 2: Install Dependencies

Run the following command to install PHP dependencies:

```bash
composer install
```

### Step 3: Configure Environment

Copy the .env.example file and update the configurations for your database and email services:

```bash
cp .env.example .env
```

Update the following lines in the .env file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

Replace your_database_name, your_username, and your_password with your MySQL credentials.
Replace your_mailtrap_username and your_mailtrap_password with your Mailtrap credentials.

### Step 4: Run Database Migrations

Set up the database structure by running:

```bash
php artisan migrate
```

### Step 5: Seed the Database

Seed the database with default user and items:

```bash
php artisan db:seed
```

Default Credentials:

-   Email: r@gmail.com
-   Password: 12345678

### Step 6: Start the Application

Open your browser and navigate to:

http://localhost:8000

### Note: Test cases are written using PHPUnit

If you want to check the test cases:

```bash
php artisan test
```

## Usage Instructions

### Managing Shopping Lists

-   Add new items by entering the name and price in the input fields and clicking **Add**.
-   Mark items as purchased using the checkbox.
-   Delete items using the delete button next to the item.

### Budget Management

-   Set your budget using the **Set Limit** button.

### Sharing Shopping List

-   Enter an email address and click **Send** to share your shopping list via email.

## Technologies Used

-   **Backend:** Laravel
-   **Database:** MySQL
-   **Frontend:** AJAX, Bootstrap, JavaScript
-   **Mail Service:** Laravel Mail with Mailtrap
