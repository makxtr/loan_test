### Setup Instructions

1. Clone the repository
   ```bash
   git clone [repository-url]
   cd [project-directory]
   ```

2. Build and start Docker containers, tests
   ```bash
   make init
   ```
3. Run test separately
      ```bash
   make tests
   ```

API

*  Create Client (POST /api/client)

   _Creates a new client with the provided details. The client data is stored in var/clients.json._


   `curl -X POST http://localhost:8000/api/client \
   -H "Content-Type: application/json" \
   -d '{
   "name": "Petr Pavel",
   "age": 35,
   "region": "PR",
   "income": 1500.0,
   "score": 600,
   "pin": "123-45-6789",
   "email": "petr.pavel@example.com",
   "phone": "+420123456789"
   }'`

    Response: 200 OK, 400 Error

* Check for credit (POST /api/credit/check)

`curl -X POST http://localhost:8000/api/credit/check \
-H "Content-Type: application/json" \
-d '{
"pin": "123-45-6789",
"amount": 2000
}'`

    Response: true/false

* Approve Credit (POST /api/credit)

Creates a new credit. The credit data is stored in var/credits.json.



`curl -X POST http://localhost:8000/api/credit \
-H "Content-Type: application/json" \
-d '{
"pin": "123-45-6789",
"amount": 2000.0,
"startDate": "2025-09-03T18:12:00+03:00",
"endDate": "2026-09-03T18:12:00+03:00"
}'`

    Response: 201 Created, 400 Error
