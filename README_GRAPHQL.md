# Travel App Backend - GraphQL API

Laravel 12 backend with GraphQL API for authentication (register, login, logout) using Laravel Sanctum.

## Tech Stack

- **Laravel**: 12.x
- **PHP**: 8.2+
- **Database**: MySQL
- **GraphQL**: rebing/graphql-laravel
- **Authentication**: Laravel Sanctum

## Installation

1. **Install Dependencies**
```bash
composer install
```

2. **Configure Environment**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configure Database**

Update your `.env` file with MySQL credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=travel_app
DB_USERNAME=root
DB_PASSWORD=
```

4. **Run Migrations**
```bash
php artisan migrate
```

5. **Start Server**
```bash
php artisan serve
```

The API will be available at: `http://localhost:8000`

## GraphQL Endpoint

**URL**: `http://localhost:8000/graphql`

## Available Mutations

### 1. Register

Create a new user account.

**Mutation:**
```graphql
mutation {
  register(
    name: "John Doe"
    email: "john@example.com"
    password: "password123"
    password_confirmation: "password123"
  ) {
    user {
      id
      name
      email
      created_at
    }
    token
    message
  }
}
```

**Response:**
```json
{
  "data": {
    "register": {
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-10-19T03:20:00.000000Z"
      },
      "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
      "message": "User registered successfully"
    }
  }
}
```

### 2. Login

Authenticate an existing user.

**Mutation:**
```graphql
mutation {
  login(
    email: "john@example.com"
    password: "password123"
  ) {
    user {
      id
      name
      email
    }
    token
    message
  }
}
```

**Response:**
```json
{
  "data": {
    "login": {
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "token": "2|xxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
      "message": "Login successful"
    }
  }
}
```

### 3. Logout

Log out the authenticated user (requires authentication).

**Headers Required:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

**Mutation:**
```graphql
mutation {
  logout {
    user {
      id
      name
      email
    }
    message
  }
}
```

**Response:**
```json
{
  "data": {
    "logout": {
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "message": "Logout successful"
    }
  }
}
```

## Testing with cURL

### Register:
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "query": "mutation { register(name: \"John Doe\", email: \"john@example.com\", password: \"password123\", password_confirmation: \"password123\") { user { id name email } token message } }"
  }'
```

### Login:
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "query": "mutation { login(email: \"john@example.com\", password: \"password123\") { user { id name email } token message } }"
  }'
```

### Logout:
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "query": "mutation { logout { user { id name email } message } }"
  }'
```

## Project Structure

```
app/
├── GraphQL/
│   ├── Mutations/
│   │   ├── RegisterMutation.php    # User registration
│   │   ├── LoginMutation.php       # User login
│   │   └── LogoutMutation.php      # User logout
│   └── Types/
│       ├── UserType.php            # User GraphQL type
│       └── AuthPayloadType.php     # Auth response type
├── Models/
│   └── User.php                    # User model with HasApiTokens trait
config/
├── graphql.php                      # GraphQL configuration
├── sanctum.php                      # Sanctum configuration
└── cors.php                         # CORS configuration
```

## Security Features

- **Password Hashing**: Passwords are hashed using bcrypt
- **Token-based Authentication**: Laravel Sanctum for API tokens
- **CORS Configuration**: Configured for cross-origin requests
- **Validation**: Input validation on all mutations
- **Token Revocation**: Previous tokens are revoked on new login

## CORS Configuration

The API is configured to accept requests from any origin. In production, update `config/cors.php` to restrict allowed origins:

```php
'allowed_origins' => ['https://your-frontend-domain.com'],
```

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User's name
- `email` - Unique email address
- `password` - Hashed password
- `email_verified_at` - Email verification timestamp
- `created_at` - Account creation timestamp
- `updated_at` - Last update timestamp

### Personal Access Tokens Table (Sanctum)
- `id` - Primary key
- `tokenable_type` - Polymorphic type
- `tokenable_id` - User ID
- `name` - Token name
- `token` - Hashed token
- `abilities` - Token abilities/scopes
- `last_used_at` - Last usage timestamp
- `expires_at` - Expiration timestamp
- `created_at` - Token creation timestamp
- `updated_at` - Last update timestamp

## Integration with Frontend

When integrating with your travel app frontend:

1. **API Endpoint**: `http://localhost:8000/graphql`
2. **Authentication Flow**:
   - Call `register` or `login` mutation
   - Store the returned `token` in localStorage/sessionStorage
   - Include token in subsequent requests:
     ```
     Authorization: Bearer YOUR_TOKEN
     ```
3. **GraphQL Client**: Use Apollo Client, urql, or any GraphQL client
4. **Example with Apollo Client**:
   ```javascript
   import { ApolloClient, InMemoryCache, createHttpLink } from '@apollo/client';
   import { setContext } from '@apollo/client/link/context';

   const httpLink = createHttpLink({
     uri: 'http://localhost:8000/graphql',
   });

   const authLink = setContext((_, { headers }) => {
     const token = localStorage.getItem('token');
     return {
       headers: {
         ...headers,
         authorization: token ? `Bearer ${token}` : "",
       }
     }
   });

   const client = new ApolloClient({
     link: authLink.concat(httpLink),
     cache: new InMemoryCache()
   });
   ```

## Error Handling

The API returns errors in GraphQL format:

```json
{
  "errors": [
    {
      "message": "Validation error: The email has already been taken.",
      "extensions": {
        "category": "graphql"
      }
    }
  ]
}
```

Common error messages:
- `Validation error: ...` - Input validation failed
- `Invalid credentials` - Login failed
- `Unauthenticated` - No valid token provided for logout

## Development

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Run Tests
```bash
php artisan test
```

### Database Reset
```bash
php artisan migrate:fresh
```

## Production Considerations

1. **Environment Variables**: Update `.env` for production
2. **CORS**: Restrict allowed origins in `config/cors.php`
3. **Database**: Use production MySQL credentials
4. **HTTPS**: Use HTTPS in production
5. **Token Expiration**: Configure in `config/sanctum.php`
6. **Rate Limiting**: Add rate limiting middleware
7. **Error Reporting**: Configure proper error logging

## License

MIT License
