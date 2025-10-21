# Inventory Management System

This is a simple inventory management system built with PHP, MySQL, and Bootstrap.

## Features

*   Add, view, edit, and delete products
*   Track stock levels
*   Generate basic inventory reports

## Requirements

*   PHP
*   MySQL
*   Web server (e.g., Apache, Nginx)

## Installation

1.  **Clone the repository:**

    ```bash
    git clone https://github.com/your-username/inventory-management.git
    ```

2.  **Create the database:**

    *   Create a new database in your MySQL server (e.g., `inventory_db`).
    *   Import the `database.sql` file to create the `products` table.

3.  **Configure the database connection:**

    *   Open the `config/database.php` file.
    *   Update the `DB_NAME`, `DB_USER`, and `DB_PASS` constants with your database credentials.

4.  **Run the application:**

    *   Place the project files in your web server's document root.
    *   Open the project in your web browser.

## Usage

*   The main page displays a list of all products.
*   You can add a new product by clicking the "Add Product" button.
*   You can edit or delete a product by clicking the corresponding buttons in the product list.
*   You can view an inventory report by clicking the "View Reports" button.
