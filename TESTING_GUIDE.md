# Testing Guide - Travel App Backend GraphQL API

This guide will show you multiple ways to test the GraphQL API endpoints.

## Prerequisites

Before testing, ensure:
1. ✅ MySQL server is running
2. ✅ Database `travel_app` exists
3. ✅ Backend server is running

## Starting the Backend Server

### Option 1: Run in Terminal
```bash
cd travel_app_backend
php artisan serve
```

The server will start at: `http://localhost:8000`

You should see:
```
INFO  Server running on [http://127.0.0.1:8000]
```

Keep this terminal open while testing.

---

## Testing Methods

### Method 1: Using Postman (Recommended for Beginners)

#### Step 1: Download & Install Postman
Download from: https://www.postman.com/downloads/

#### Step 2: Create a New Request
1. Open Postman
2. Click "New" → "HTTP Request"
3. Set method to **POST**
4. Enter URL: `http://localhost:8000/graphql`

#### Step 3: Configure Headers
Click on "Headers" tab and add:
- Key: `Content-Type`, Value: `application/json`
- Key: `Accept`, Value: `application/json`

#### Step 4: Test Register Mutation

Click on "Body" tab → Select "raw" → Choose "JSON"

Paste this:
```json
{
  "query": "mutation { register(name: \"John Doe\", email: \"john@example.com\", password: \"password123\", password_confirmation: \"password123\") { user { id name email created_at } token message } }"
}
```

Click **Send**

**Expected Response:**
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
      "token": "1|xxxxxxxxxxxxxxxxxxxxxx",
      "message": "User registered successfully"
    }
  }
}
```

**Copy the token** from the response - you'll need it for logout!

#### Step 5: Test Login Mutation

Change the body to:
```json
{
  "query": "mutation { login(email: \"john@example.com\", password: \"password123\") { user { id name email } token message } }"
}
```

Click **Send**

**Expected Response:**
```json
{
  "data": {
    "login": {
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "token": "2|xxxxxxxxxxxxxxxxxxxxxx",
      "message": "Login successful"
    }
  }
}
```

#### Step 6: Test Logout Mutation

1. Go to "Headers" tab
2. Add a new header:
   - Key: `Authorization`
   - Value: `Bearer YOUR_TOKEN_HERE` (replace with token from login)

3. In "Body", change to:
```json
{
  "query": "mutation { logout { user { id name email } message } }"
}
```

Click **Send**

**Expected Response:**
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

---

### Method 2: Using cURL (Command Line)

Open a new terminal (keep the server running in the first one).

#### Test Register
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"query\": \"mutation { register(name: \\\"Jane Doe\\\", email: \\\"jane@example.com\\\", password: \\\"password123\\\", password_confirmation: \\\"password123\\\") { user { id name email } token message } }\"}"
```

#### Test Login
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"query\": \"mutation { login(email: \\\"jane@example.com\\\", password: \\\"password123\\\") { user { id name email } token message } }\"}"
```

**Copy the token from the response**

#### Test Logout
Replace `YOUR_TOKEN_HERE` with the actual token:
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d "{\"query\": \"mutation { logout { user { id name email } message } }\"}"
```

---

### Method 3: Using GraphQL Playground (Web-based)

#### Option A: Altair GraphQL Client (Browser Extension)

1. **Install Altair**
   - Chrome: https://chrome.google.com/webstore (search "Altair GraphQL")
   - Firefox: https://addons.mozilla.org/firefox/ (search "Altair GraphQL")

2. **Open Altair**
   - Click the extension icon
   - Enter URL: `http://localhost:8000/graphql`

3. **Test Register**

   In the query editor, paste:
   ```graphql
   mutation {
     register(
       name: "Alice Smith"
       email: "alice@example.com"
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

   Click "Send Request"

4. **Test Login**

   ```graphql
   mutation {
     login(
       email: "alice@example.com"
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

5. **Test Logout**

   First, add the Authorization header:
   - Click "Set Headers"
   - Add: `Authorization: Bearer YOUR_TOKEN`

   Then run:
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

#### Option B: GraphiQL (Install Package)

1. Install GraphiQL package (in your backend):
   ```bash
   composer require mll-lab/laravel-graphiql
   ```

2. Visit: `http://localhost:8000/graphiql`

3. Use the same GraphQL queries as above

---

### Method 4: Using a Simple HTML Test Page

Create a file `test-api.html` anywhere on your computer:

```html
<!DOCTYPE html>
<html>
<head>
    <title>GraphQL API Tester</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        button { padding: 10px 20px; margin: 10px 0; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 5px; }
        button:hover { background: #0056b3; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
        input { padding: 8px; margin: 5px 0; width: 100%; box-sizing: border-box; }
        .section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Travel App GraphQL API Tester</h1>

    <div class="section">
        <h2>1. Register User</h2>
        <input type="text" id="reg-name" placeholder="Name" value="Test User">
        <input type="email" id="reg-email" placeholder="Email" value="test@example.com">
        <input type="password" id="reg-password" placeholder="Password" value="password123">
        <input type="password" id="reg-confirm" placeholder="Confirm Password" value="password123">
        <button onclick="register()">Register</button>
        <pre id="register-result"></pre>
    </div>

    <div class="section">
        <h2>2. Login</h2>
        <input type="email" id="login-email" placeholder="Email" value="test@example.com">
        <input type="password" id="login-password" placeholder="Password" value="password123">
        <button onclick="login()">Login</button>
        <pre id="login-result"></pre>
    </div>

    <div class="section">
        <h2>3. Logout</h2>
        <input type="text" id="logout-token" placeholder="Paste token here">
        <button onclick="logout()">Logout</button>
        <pre id="logout-result"></pre>
    </div>

    <script>
        const API_URL = 'http://localhost:8000/graphql';

        async function register() {
            const name = document.getElementById('reg-name').value;
            const email = document.getElementById('reg-email').value;
            const password = document.getElementById('reg-password').value;
            const password_confirmation = document.getElementById('reg-confirm').value;

            const query = `
                mutation {
                    register(
                        name: "${name}"
                        email: "${email}"
                        password: "${password}"
                        password_confirmation: "${password_confirmation}"
                    ) {
                        user { id name email created_at }
                        token
                        message
                    }
                }
            `;

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ query })
                });

                const result = await response.json();
                document.getElementById('register-result').textContent = JSON.stringify(result, null, 2);

                if (result.data?.register?.token) {
                    document.getElementById('logout-token').value = result.data.register.token;
                }
            } catch (error) {
                document.getElementById('register-result').textContent = 'Error: ' + error.message;
            }
        }

        async function login() {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;

            const query = `
                mutation {
                    login(email: "${email}", password: "${password}") {
                        user { id name email }
                        token
                        message
                    }
                }
            `;

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ query })
                });

                const result = await response.json();
                document.getElementById('login-result').textContent = JSON.stringify(result, null, 2);

                if (result.data?.login?.token) {
                    document.getElementById('logout-token').value = result.data.login.token;
                }
            } catch (error) {
                document.getElementById('login-result').textContent = 'Error: ' + error.message;
            }
        }

        async function logout() {
            const token = document.getElementById('logout-token').value;

            const query = `
                mutation {
                    logout {
                        user { id name email }
                        message
                    }
                }
            `;

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ query })
                });

                const result = await response.json();
                document.getElementById('logout-result').textContent = JSON.stringify(result, null, 2);
            } catch (error) {
                document.getElementById('logout-result').textContent = 'Error: ' + error.message;
            }
        }
    </script>
</body>
</html>
```

**How to use:**
1. Save the file as `test-api.html`
2. Open it in your web browser (double-click the file)
3. Fill in the forms and click the buttons to test each mutation

---

## Expected Responses

### ✅ Successful Register
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
      "token": "1|pDRfflYb8MknPKoNQRVb0kgzKtQg3PHATaY1bQxh53c4ae12",
      "message": "User registered successfully"
    }
  }
}
```

### ✅ Successful Login
```json
{
  "data": {
    "login": {
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "token": "2|EvJZ3BsmnUJcCOJgYfKuBG4uiGfmmobVDjtdyOws0398fb59",
      "message": "Login successful"
    }
  }
}
```

### ✅ Successful Logout
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

### ❌ Error: Email Already Exists
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

### ❌ Error: Invalid Credentials
```json
{
  "errors": [
    {
      "message": "Invalid credentials",
      "extensions": {
        "category": "graphql"
      }
    }
  ]
}
```

### ❌ Error: Unauthenticated (Logout without token)
```json
{
  "errors": [
    {
      "message": "Unauthenticated",
      "extensions": {
        "category": "graphql"
      }
    }
  ]
}
```

---

## Testing Checklist

Use this checklist to verify everything works:

- [ ] Backend server is running on http://localhost:8000
- [ ] MySQL server is running
- [ ] Database `travel_app` exists
- [ ] Can register a new user successfully
- [ ] Receive a valid token from register
- [ ] Cannot register same email twice (validation works)
- [ ] Can login with correct credentials
- [ ] Receive a new token from login
- [ ] Cannot login with wrong password
- [ ] Can logout with valid token
- [ ] Cannot logout without token

---

## Troubleshooting

### Problem: "Connection refused" or "Cannot connect"
**Solution:** Make sure the backend server is running:
```bash
cd travel_app_backend
php artisan serve
```

### Problem: "SQLSTATE[HY000] [2002] Connection refused"
**Solution:** MySQL server is not running. Start MySQL:
- Windows: Start MySQL service from Services
- Mac: `brew services start mysql`
- Linux: `sudo service mysql start`

### Problem: "Database 'travel_app' doesn't exist"
**Solution:** The migration should create it automatically. If not:
```bash
php artisan migrate
```

### Problem: "CORS policy" error in browser
**Solution:** This is normal - CORS is configured for development. Make sure:
1. You're using the correct URL: `http://localhost:8000/graphql`
2. Headers are set correctly

### Problem: Token doesn't work for logout
**Solution:** Make sure:
1. You're using the token from the most recent login
2. Token format is: `Bearer YOUR_TOKEN` (note the space)
3. Token hasn't been revoked by another logout

---

## Quick Test Script

Want to test everything at once? Run this in a new terminal:

```bash
# Register
echo "Testing Register..."
curl -s -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"mutation{register(name:\"Quick Test\",email:\"quick@test.com\",password:\"password123\",password_confirmation:\"password123\"){token message}}"}' \
  | python -m json.tool

# Login
echo "\nTesting Login..."
curl -s -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"mutation{login(email:\"quick@test.com\",password:\"password123\"){token message}}"}' \
  | python -m json.tool
```

---

## Next Steps

Once you've verified everything works:

1. ✅ Test all three mutations (register, login, logout)
2. ✅ Verify tokens are generated correctly
3. ✅ Test error cases (wrong password, duplicate email, etc.)
4. 🚀 Start integrating with your frontend application!

See [INTEGRATION_GUIDE.md](../INTEGRATION_GUIDE.md) for frontend integration.

---

## Need Help?

If you encounter any issues:
1. Check the Laravel logs: `travel_app_backend/storage/logs/laravel.log`
2. Check the server console output
3. Verify MySQL is running: `mysql -u root -p` then `SHOW DATABASES;`
4. Make sure all migrations ran: `php artisan migrate:status`

Happy Testing! 🚀
