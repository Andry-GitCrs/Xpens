# Xpens

A simple expense tracking application.

## Features

*   User authentication (register, login, logout)
*   Manage shopping lists
*   Manage products
*   Track purchases and expenses

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

*   PHP 
*   MySQL
*   A web server (e.g., Apache, Nginx); i made it with PHP 7.3.12 using the Development Server

### Installation

1.  Clone the repo
    ```sh
    git clone https://github.com/Andry-GitCrs/Xpens.git
    ```
2.  Create a `.env` file in the root directory and add the following environment variables:
    ```
    DB_HOST=your_db_host
    DB_NAME=your_db_name
    DB_USER=your_db_user
    DB_PASS=your_db_password
    ```
3.  Run 
    ```sh
    php -S localhost:8000
    ``` 
4. Navigate to `/` in your browser to initialize the database tables.

## API Endpoints

The following are the available API endpoints:

*   `api/auth/`
*   `api/lists/`
*   `api/products/`
*   `api/purchases/`
*   `api/users/`

For more details, please refer to the code.

## Database Schema

The database schema is defined in `config/init.php`. It consists of the following tables:

*   `users`
*   `lists`
*   `products`
*   `purchases`
