# Crypto Bridge Exchange

Crypto Bridge Exchange is a Laravel-based project that implements APIs for currency conversion orders and order information retrieval, along with a process for updating currency prices.

## Features

1. **Order Placement API**: Implements a RESTful API for registering currency conversion orders. Users can submit orders to convert one currency to another, specifying the source currency, destination currency, and amount.
    - **Required Inputs**:
        - User Email
        - Source Currency
        - Destination Currency
        - Amount of Source Currency
    - **Output**:
        - Order Tracking Code

2. **Order Information Retrieval API**: Provides an API for users to retrieve information about their submitted orders using the order tracking code received during order placement.

3. **Currency Price Update Process**: A background process runs every minute to update the prices of all currencies available in the system. You can use a mock API for updating currency prices.

## Installation

Follow these steps to set up the project locally:

1. Clone the repository:
   ```bash
   git clone https://github.com/donyatorabi/CryptoBridgeExchange.git
   
2. Install dependencies using Composer in project directory:
   ```bash
   composer install
   
3. Copy the .env.example file and update the configuration:
   ```bash
   cp .env.example .env
   
4. Generate an application key:
   ```bash
   php artisan key:generate
   
5. Configure the database connection in the .env file:
   ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=crypto-bridge-exchange-db
    DB_USERNAME=root
    DB_PASSWORD=
   
6. Run migrations:
    ```bash
   php artisan migrate

## Usage

**1. Order Placement API:**

- Endpoint: /order
- Method: POST
- Required Parameters:
````
email: User Email
src_coin_id: Source Currency
dest_coin_id: Destination Currency
price: Amount of Source Currency
quantity: Number of Source Currency
````
- Example Request:
```bash
    curl -X POST http://localhost:8000/order \
    -d 'email=user@example.com' \
    -d 'src_coin_id=1' \
    -d 'dest_coin_id=2' \
    -d 'price=50000000'
    -d 'quantity=2'
```  
- Example Response:
```bash
{
  "status": "success",
  "code": 200,
  "tracker_id": "2a0abc64-7338-45b5-a561-39f273cb8916"
}
```

**2. Order Information Retrieval API:**
- Endpoint: /order/{trackerId}
- Method: GET
- Example Request:
```bash
curl http://localhost:8000/order/2a0abc64-7338-45b5-a561-39f273cb8916
```
- Example Response
```bash
{
  "data": 
    [
        {
            "srcCoin": "USDT",
            "srcCoinPrice": 570000,
            "destCoin": "IRR",
            "destCoinPrice": 1,
            "quantity": 60,
            "email": "user@example.com"
        }
    ]
}
```
## Contact
For any inquiries or support, please contact **donya.torabi01@gmail.com**.
