# Report on Chosen Data Interchange Format and HTTP Response Code Structures

## Introduction:
In modern web application design, choosing the right data format for communication and ensuring that the server provides meaningful HTTP response codes are paramount. Our application has been analyzed to review the choice of data format and the implementation of response codes. Below are the findings.

## Data Interchange Format:
The code provided makes use of JSON as its data interchange format. JSON is lightweight, human-readable, and easy to parse, making it a popular choice for web APIs and services.

## HTTP Response Code Structures:

### 1. Purchase Item (Endpoint: /purchase-item)

**Request Requirements:**
- Method: POST
- Data: token, itemId, userId, quantity, and seller_ip.

**Response Structure:**
- Authentication Failure: 401 Unauthorized.
```json
{
  "success": false,
  "message": "Invalid or expired token."
}
```
***Purchase Completion: 201 Created.***
```json
{
  "success": true,
  "message": "Purchase completed successfully!"
}
```
***User Not Found: 400 Bad Request.***
```json
{
  "success": false,
  "message": "User not found."
}
```
***Insufficient Balance: 400 Bad Request***
```json
{
  "success": false,
  "message": "Insufficient balance."
}
```
***Error while creating purchase: 500 Internal Server Error.***
```json
{
  "success": false,
  "message": "Sorry!, error while creating purchasing."
}
```

### 2. Search Purchase (Endpoint: /search-purchase)

**Searching by Purchase ID (purId):**  
****Success: 200 OK.****
```json
{
    "success": true,
    "data": [
        {
            "pur_id": "1",
            "user_id": "1",
            "item_id": "1",
            "quantity": "1",
            "price": "53.00",
            "seller_ip": "127.0.0.1:8080",
            "date": "2023-10-01 10:29:36"
        },
        {
            "pur_id": "2",
            "user_id": "1",
            "item_id": "1",
            "quantity": "1",
            "price": "53.00",
            "seller_ip": "127.0.0.1:8080",
            "date": "2023-10-01 15:30:54"
        }
    ]
}
```
****No purchase found for the given Purchase ID: 404 Not Found.****
```json
{
  "success": false,
  "message": "No purchase found for the provided purId."
}
```
***Searching by User ID (userId):***
****Success: 200 OK.****
```json
{
    "success": true,
    "data": [
        {
            "pur_id": "1",
            "user_id": "1",
            "item_id": "1",
            "quantity": "1",
            "price": "53.00",
            "seller_ip": "127.0.0.1:8080",
            "date": "2023-10-01 10:29:36"
        },
        {
            "pur_id": "2",
            "user_id": "1",
            "item_id": "1",
            "quantity": "1",
            "price": "53.00",
            "seller_ip": "127.0.0.1:8080",
            "date": "2023-10-01 15:30:54"
        }
    ]
}
```
****No purchases found for the given User ID: 404 Not Found****
```json
{
  "success": false,
  "message": "No purchases found for the provided userId."
}
```

***Neither purId nor userId provided:***
****Error: 400 Bad Request.****
```json
{
  "success": false,
  "message": "Neither purId nor userId provided."
}
```
### 3. Cancel Purchase (Endpoint: /cancel-purchase)
**Request Requirements:**
-Method: DELETE
-Data: token and purchaseId.

**Response Structure:**
***Authentication Failure: 401 Unauthorized.***
```json
{
  "success": false,
  "message": "Invalid or expired token."
}
```
***Purchase Cancellation Success: 201 Created.***
```json
{
  "success": true,
  "message": "Purchase cancelled successfully."
}
```
***Error while Canceling purchase: 500 Internal Server Error.***
```json
{
  "success": false,
  "message": "Failed to cancel the purchase."
}
```

### 4. Add Balance (Endpoint: /add-balance)

**Request Requirements:**  
- Method: POST
- Data: token, amount, and userId.

***Success: Expected to return a 200 OK when balance is successfully added.***  
***Success: 201 Created.***
```json
{
  "success": true,
  "message": "Balance updated successfully."
}
```


***Failure: A 401 Unauthorized when the token is invalid or expired.***
****Authentication Failure: 401 Unauthorized.****
```json
{
  "success": false,
  "message": "Invalid or expired token."
}
```
****User Not Found: 400 Bad Request.****
```json
{
  "success": false,
  "message": "User not found."
}
```
****Failed Update: 500 Internal Server Error.****
```json
{
  "success": false,
  "message": "Failed to update balance."
}
```

### 5. Card Check (Endpoint: /card-check)

**Request Requirements:**  
- Method: POST
- Data: cardNum and pin.

***Success: Returns a 200 OK when the card credentials are verified successfully, returning a JWT token.***  
***Success: 200 OK.***
```json
{
  "status": "success",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJNYXJrZXRwbGFjZSIsImlhdCI6MTY5NjE1OTQ3NSwiZXhwIjoxNjk2MTU5Nzc1LCJjYXJkX251bSI6IjEyMzQ1Njc4OSJ9.dnS5SRSTyOi9tEOcEsWjDba9x-WlcN2Vk6jc1ysfSq0"
}
```

***Failure: A generic 400 Bad Request or a more specific 401 Unauthorized when the card credentials are incorrect.***
```json
{
  "status": "error",
  "message": "Invalid card credentials"
}
```

### 6. Get Item List

***Success: A 200 OK response when items are successfully retrieved.***  
***Success: 200 OK.***
```json
{
  "success": true,
  "data": [
    {
      "item_id": "1",
      "item_name": "first item from seller1",
      "stock_qty": "23",
      "price_of_unit": "53.00"
    }
  ],
  "seller_ip": "127.0.0.1:8080"
}
```
***Failure: A 404 Not Found when no items are available.***
```json
{
  "success": false, 
  "message": "No items found.."
}
```

### 7. Search for Items

***1. Request without a Search Term***  
***Error: 400 Bad Request.***
```json
{
  "success": false,
  "message": "Search term not provided."
}
```

***Success: Returns a 200 OK when items related to the search term are found.***
```json
{
    "success": true,
    "items": [
        {
            "item_id": "1",
            "item_name": "first item from seller1",
            "stock_qty": "23",
            "price_of_unit": "53.00",
            "seller_ip": "127.0.0.1:8080"
        }
    ]
}
```

***Search with No Matching Items Found***
```json
{
  "success": false,
  "message": "No items found for the given search term."
}
```
***Failure: A 400 Bad Request when the search term is not provided.***
```json
{
  "success": false,
  "message": "No items found for the given search term."
}
```

## Conclusion:
Our application predominantly uses JSON as its data format, capitalizing on its versatility, and readability. Additionally, we've ensured that our endpoints provide clear HTTP response codes to convey the outcome of each request, enabling better client-side handling and debugging. This choice of JSON and meaningful response codes will enhance the robustness and usability of our application.