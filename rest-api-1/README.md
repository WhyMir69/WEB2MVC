# REST API Project with JWT Authentication

This project is a RESTful API built using PHP, designed to manage user data with secure authentication via JSON Web Tokens (JWT). Below are the details for setting up and using the API.

## Project Structure

```
rest-api-1
├── index.php                # Main application entry point
├── init.php                 # Initialization and autoloading
├── routes.php               # API route definitions
├── setup_database.sql       # Database setup script
├── test.sql                 # Test queries
├── classes
│   ├── Database.php         # Database connection and query handling
│   ├── DataRepositoryInterface.php  # Interface for data repositories
│   ├── Request.php          # HTTP request handling
│   ├── RequestInterface.php # Interface for request handling
│   ├── Response.php         # HTTP response handling
│   ├── RouteMatcher.php     # Route matching logic
│   ├── Router.php           # Request routing
│   ├── UserController.php   # User management endpoints
│   ├── UserRepository.php   # User data access
│   ├── AuthController.php   # Authentication logic
│   ├── JwtHandler.php       # JWT generation and validation
│   └── Middleware.php       # Authentication middleware
└── README.md                # This file
```

## Setup Instructions

1. **Database Setup**:
   - Import the `setup_database.sql` file into your MySQL database:
     ```
     mysql -u root -p < setup_database.sql
     ```
   - This will create the necessary database, user account, and tables with sample data.

2. **Dependencies**:
   - Install the PHP JWT library using Composer:
     ```
     composer require firebase/php-jwt
     ```
   - If Composer is not installed, you can download it from [getcomposer.org](https://getcomposer.org/).

3. **Web Server Configuration**:
   - Ensure mod_rewrite is enabled if using Apache
   - For development, you can use PHP's built-in server:
     ```
     php -S localhost:8000
     ```

## API Endpoints

### Authentication

- **POST /login**: Authenticate a user and receive a JWT
  ```
  POST /login
  Content-Type: application/json
  
  {
    "email": "john.doe@example.com",
    "password": "password123"
  }
  ```
  
  **Response**:
  ```
  {
    "status": "success",
    "message": "Login successful",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john.doe@example.com"
    }
  }
  ```

### User Management (Requires Authentication)

- **GET /users**: Retrieve all users
  ```
  GET /users
  Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
  ```

- **GET /users/{id}**: Retrieve a user by ID
  ```
  GET /users/1
  Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
  ```

- **POST /users**: Create a new user
  ```
  POST /users
  Content-Type: application/json
  Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
  
  {
    "name": "New User",
    "email": "new.user@example.com",
    "password": "securepassword"
  }
  ```

- **PUT /users/{id}**: Update an existing user
  ```
  PUT /users/1
  Content-Type: application/json
  Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
  
  {
    "name": "Updated Name",
    "email": "updated.email@example.com"
  }
  ```

- **DELETE /users/{id}**: Delete a user
  ```
  DELETE /users/1
  Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
  ```

## JWT Authentication Details

### How JWT Works in This API

1. **Login Process**:
   - User provides email and password
   - Server validates credentials
   - Server generates a JWT containing user ID and expiration
   - JWT is signed using a secret key
   - Token is returned to the client

2. **Secured Endpoint Access**:
   - Client includes the JWT in the Authorization header
   - Server validates the token signature and expiration
   - If valid, the request proceeds; if invalid, 401 Unauthorized response

3. **Token Structure**:
   - Header: Algorithm and token type
   - Payload: User ID, issuance time, expiration time
   - Signature: HMAC-SHA256 signature using the secret key

4. **Security Considerations**:
   - Tokens expire after 1 hour by default
   - Use HTTPS in production
   - Keep the secret key secure
   - Never store sensitive data in the JWT payload

## Testing the API

You can test the API using tools like Postman, cURL, or any HTTP client:

```bash
# Login example
curl -X POST http://localhost/rest-api-1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john.doe@example.com","password":"password123"}'

# Using the token
curl -X GET http://localhost/rest-api-1/users \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

## Error Handling

The API returns appropriate HTTP status codes and JSON responses:

- **400 Bad Request**: Missing or invalid parameters
- **401 Unauthorized**: Invalid or expired JWT
- **404 Not Found**: Resource not found
- **500 Server Error**: Unexpected error