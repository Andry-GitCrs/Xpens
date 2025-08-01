
# Xpens API Documentation

This documentation provides a complete overview of the Xpens API, designed to facilitate the development of a user interface.

## Project Features

### Authentication
- **User Registration:** Create a new user account.
- **User Login:** Authenticate and start a session.
- **User Logout:** Terminate the current session.

### User Management
- **Get All Users:** Retrieve a list of all registered users.

### List Management
- **Create Lists:** Add new shopping or expense lists.
- **View Lists:** See all lists belonging to the authenticated user.
- **Update Lists:** Modify the name or description of existing lists.
- **Delete Lists:** Remove unwanted lists.

### Product Management
- **Create Products:** Add new products that can be purchased.
- **View Products:** Get a list of all available products.
- **Update Products:** Change the name of a product.
- **Delete Products:** Remove products from the system.

### Purchase Tracking
- **Log Purchases:** Record new purchases with details like number of items, unit, unit price, and description.
- **View Purchases:** Get a complete history of all purchases.
- **Update Purchases:** Modify details of a previously logged purchase.
- **Delete Purchases:** Remove purchase records.

### Expense Filtering and Reporting
- **Filter by List:** View all purchases associated with a specific list.
- **Filter by Date:** See all purchases made on a particular date.
- **Filter by Date Range:** Analyze expenses within a specific period.
- **Total Expense by Product:** Calculate the total amount spent on a single product.
- **Total Expense by Date:** Sum up all expenses for a specific day.
- **Total Expense by Date Range:** Get the total expenses over a range of dates.

## Base URL

The base URL for all API endpoints is: `https://domain.com/api`

## Authentication

Authentication is required for most endpoints. The API uses a session-based authentication system.

### Register

* **Endpoint:** `/auth/register`
* **Method:** `POST`
* **Description:** Creates a new user account.
* **Request Body:**
  ```json
  {
    "username": "testuser",
    "email": "test@example.com",
    "password": "password123"
  }
  ```
* **Responses:**
  * `201 Created`: User registered successfully.
  * `400 Bad Request`: Invalid input.
  * `409 Conflict`: User with this username or email already exists.
  * `500 Internal Server Error`: Failed to create user.

### Login

* **Endpoint:** `/auth/login`
* **Method:** `POST`
* **Description:** Authenticates a user and starts a session.
* **Request Body:**
  ```json
  {
    "username": "testuser",
    "password": "password123"
  }
  ```
* **Responses:**
  * `200 OK`: User connected successfully.
  * `401 Unauthorized`: Invalid credentials or user not registered.
  * `405 Method Not Allowed`: Method not allowed.

### Logout

* **Endpoint:** `/auth/logout`
* **Method:** `GET`
* **Description:** Logs out the currently authenticated user and destroys the session.
* **Responses:**
  * `200 OK`: User logged out successfully.
  * `401 Unauthorized`: User not authenticated.
  * `405 Method Not Allowed`: Method not allowed.

---

## Users

### Get All Users

* **Endpoint:** `/users`
* **Method:** `GET`
* **Description:** Retrieves a list of all active users.
* **Authentication:** Not required.
* **Responses:**
  * `200 OK`: Returns an array of user objects.
  * `405 Method Not Allowed`: Method not allowed.

---

## Lists

### Get All Lists

* **Endpoint:** `/lists`
* **Method:** `GET`
* **Description:** Retrieves all lists belonging to the authenticated user.
* **Authentication:** Required.
* **Responses:**
  * `200 OK`: Returns an array of list objects.
  * `401 Unauthorized`: User not authenticated.
  * `405 Method Not Allowed`: Method not allowed.

### Create a List

* **Endpoint:** `/lists`
* **Method:** `POST`
* **Description:** Creates a new list for the authenticated user.
* **Authentication:** Required.
* **Request Body:**
  ```json
  {
    "list_name": "My Shopping List",
    "description": "A list of things to buy."
  }
  ```
* **Responses:**
  * `201 Created`: List created successfully.
  * `400 Bad Request`: Invalid input.
  * `401 Unauthorized`: User not authenticated.
  * `404 Not Found`: User not found.
  * `409 Conflict`: List with the same name already exists.
  * `500 Internal Server Error`: Failed to create list.

### Update a List

* **Endpoint:** `/lists?id={list_id}`
* **Method:** `PUT`
* **Description:** Updates an existing list.
* **Authentication:** Required.
* **URL Parameters:**
  * `id` (required): The ID of the list to update.
* **Request Body:**
  ```json
  {
    "list_name": "My Updated Shopping List",
    "description": "An updated list of things to buy."
  }
  ```
* **Responses:**
  * `200 OK`: List updated successfully.
  * `400 Bad Request`: Invalid input.
  * `401 Unauthorized`: User not authenticated.
  * `404 Not Found`: List not found.
  * `500 Internal Server Error`: Failed to update list.

### Delete a List

* **Endpoint:** `/lists?id={list_id}`
* **Method:** `DELETE`
* **Description:** Deletes a list.
* **Authentication:** Required.
* **URL Parameters:**
  * `id` (required): The ID of the list to delete.
* **Responses:**
  * `200 OK`: List deleted successfully.
  * `400 Bad Request`: Invalid input.
  * `401 Unauthorized`: User not authenticated.
  * `404 Not Found`: List not found.
  * `500 Internal Server Error`: Failed to delete list.

---

## Products

### Get All Products

* **Endpoint:** `/products`
* **Method:** `GET`
* **Description:** Retrieves a list of all available products.
* **Authentication:** Required.
* **Responses:**
  * `200 OK`: Returns an array of product objects.
  * `401 Unauthorized`: User not authenticated.
  * `405 Method Not Allowed`: Method not allowed.

### Create a Product

* **Endpoint:** `/products`
* **Method:** `POST`
* **Description:** Creates a new product.
* **Authentication:** Required.
* **Request Body:**
  ```json
  {
    "product_name": "Milk"
  }
  ```
* **Responses:**
  * `201 Created`: Product created successfully.
  * `400 Bad Request`: Invalid input.
  * `401 Unauthorized`: User not authenticated.
  * `409 Conflict`: Product with the same name already exists.
  * `500 Internal Server Error`: Failed to create product.

### Update a Product

* **Endpoint:** `/products?id={product_id}`
* **Method:** `PUT`
* **Description:** Updates an existing product.
* **Authentication:** Required.
* **URL Parameters:**
  * `id` (required): The ID of the product to update.
* **Request Body:**
  ```json
  {
    "product_name": "Almond Milk"
  }
  ```
* **Responses:**
  * `200 OK`: Product updated successfully.
  * `400 Bad Request`: Invalid input.
  * `401 Unauthorized`: User not authenticated.
  * `404 Not Found`: Product not found.
  * `409 Conflict`: Another product with the same name already exists.
  * `500 Internal Server Error`: Failed to update product.

### Delete a Product

* **Endpoint:** `/products?id={product_id}`
* **Method:** `DELETE`
* **Description:** Deletes a product.
* **Authentication:** Required.
* **URL Parameters:**
  * `id` (required): The ID of the product to delete.
* **Responses:**
  * `200 OK`: Product deleted successfully.
  * `400 Bad Request`: Invalid input.
  * `401 Unauthorized`: User not authenticated.
  * `404 Not Found`: Product not found.
  * `500 Internal Server Error`: Failed to delete product.

---

## Purchases

### Get All Purchases

* **Endpoint:** `/purchases`
* **Method:** `GET`
* **Description:** Retrieves all purchases for the authenticated user.
* **Authentication:** Required.
* **Responses:**
  * `200 OK`: Returns an array of purchase objects.
  * `401 Unauthorized`: User not authenticated.
  * `405 Method Not Allowed`: Method not allowed.

### Filter Purchases by List

* **Endpoint:** `/purchases?list_id={list_id}`
* **Method:** `GET`
* **Description:** Retrieves all purchases for a specific list.
* **Authentication:** Required.
* **URL Parameters:**
  * `list_id` (required): The ID of the list to filter by.
* **Responses:**
  * `200 OK`: Returns an array of purchase objects.
  * `401 Unauthorized`: User not authenticated.
  * `404 Not Found`: List not found.
  * `405 Method Not Allowed`: Method not allowed.

### Filter Purchases by Date

* **Endpoint:** `/purchases?purchase_date={dd-mm-yyyy}`
* **Method:** `GET`
* **Description:** Retrieves all purchases for a specific date.
* **Authentication:** Required.
* **URL Parameters:**
  * `purchase_date` (required): The date to filter by in `dd-mm-yyyy` format.
* **Responses:**
  * `200 OK`: Returns an array of purchase objects.
  * `400 Bad Request`: Invalid date format.
  * `401 Unauthorized`: User not authenticated.
  * `405 Method Not Allowed`: Method not allowed.

### Get Total Expense by Product

* **Endpoint:** `/purchases?product_id={product_id}&get_total_expense=1`
* **Method:** `GET`
* **Description:** Calculates the total expense for a specific product.
* **Authentication:** Required.
* **URL Parameters:**
  * `product_id` (required): The ID of the product.
  * `get_total_expense` (required): Must be set to `1`.
* **Responses:**
  * `200 OK`: Returns the total expense for the product.
  * `400 Bad Request`: Invalid input.
  * `401 Unauthorized`: User not authenticated.
  * `405 Method Not Allowed`: Method not allowed.

### Get Total Expense by Date

* **Endpoint:** `/purchases?purchase_date={dd-mm-yyyy}&get_total_expense=1`
* **Method:** `GET`
* **Description:** Calculates the total expense for a specific date.
* **Authentication:** Required.
* **URL Parameters:**
  * `purchase_date` (required): The date in `dd-mm-yyyy` format.
  * `get_total_expense` (required): Must be set to `1`.
* **Responses:**
  * `200 OK`: Returns the total expense for the date.
  * `400 Bad Request`: Invalid date format.
  * `401 Unauthorized`: User not authenticated.
  * `405 Method Not Allowed`: Method not allowed.

### Get Total Expense by Date Range

* **Endpoint:** `/purchases?start_date={dd-mm-yyyy}&end_date={dd-mm-yyyy}&get_total_expense=1`
* **Method:** `GET`
* **Description:** Calculates the total expense for a given date range.
* **Authentication:** Required.
* **URL Parameters:**
  * `start_date` (required): The start date in `dd-mm-yyyy` format.
  * `end_date` (required): The end date in `dd-mm-yyyy` format.
  * `get_total_expense` (required): Must be set to `1`.
* **Responses:**
  * `200 OK`: Returns the total expense for the date range.
  * `400 Bad Request`: Invalid date format or range.
  * `401 Unauthorized`: User not authenticated.
  * `405 Method Not Allowed`: Method not allowed.

### Create a Purchase

* **Endpoint:** `/purchases`
* **Method:** `POST`
* **Description:** Creates a new purchase.
* **Authentication:** Required.
* **Request Body:**
  ```json
  {
    "number": 1,
    "unit": "bottle",
    "unit_price": 2.5,
    "list_id": 1,
    "product_id": 1,
    "description": "1L bottle of milk",
    "purchase_date": "29-07-2025 10:00"
  }
  ```
* **Responses:**
  * `201 Created`: Purchase created successfully.
  * `400 Bad Request`: Invalid input.
  * `401 Unauthorized`: User not authenticated.
  * `404 Not Found`: List or product not found.
  * `409 Conflict`: Purchase already exists.
  * `500 Internal Server Error`: Failed to create purchase.

### Update a Purchase

* **Endpoint:** `/purchases?id={purchase_id}`
* **Method:** `PUT`
* **Description:** Updates an existing purchase.
* **Authentication:** Required.
* **URL Parameters:**
  * `id` (required): The ID of the purchase to update.
* **Request Body:**
  ```json
  {
    "number": 2,
    "unit": "bottles",
    "unit_price": 2.5,
    "list_id": 1,
    "product_id": 1,
    "description": "Two 1L bottles of milk",
    "purchase_date": "29-07-2025 10:30"
  }
  ```
* **Responses:**
  * `200 OK`: Purchase updated successfully.
  * `400 Bad Request`: Invalid input.
  * `401 Unauthorized`: User not authenticated.
  * `404 Not Found`: Purchase, list, or product not found.
  * `409 Conflict`: Another purchase with the same list and product already exists.
  * `500 Internal Server Error`: Failed to update purchase.

### Delete a Purchase

* **Endpoint:** `/purchases?id={purchase_id}`
* **Method:** `DELETE`
* **Description:** Deletes a purchase.
* **Authentication:** Required.
* **URL Parameters:**
  * `id` (required): The ID of the purchase to delete.
* **Responses:**
  * `200 OK`: Purchase deleted successfully.
  * `400 Bad Request`: Invalid input.
  * `401 Unauthorized`: User not authenticated.
  * `404 Not Found`: Purchase not found.
  * `500 Internal Server Error`: Failed to delete purchase.
