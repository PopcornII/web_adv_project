`Ordering Foods`

# run the project with the following command

- npm install
- composer install

# MYSQL
- Creat a table in MYSQL server

# env migrate
cp .env.example .env

# migration
- php artisan migrate:fresh --seed
- php artisan optimize

# run project

 `client `
npm run dev 

`server side`

php artisan serve

URL: http://127.0.0.1:8000


# Features 

- Menu List (create, edit, delete, view)
- Category List (create, edit, delete, view)
- Floor List (create, edit, delete, view)
- Table List (create, edit, delete, view)
- Order List (create, edit, delete, view)
- Order Flow

----------------------------------------------------------------
Menu Payload
`{
    "name": "Chocolate Smoothie",
    "description": "Rich chocolate blended with ice cream and milk.",
    "price": 4.99,
    "category": "Smoothies"
}
`


-------------------------------------------------------------------
Order Flow

#note
menu grid list image side w-250 h-150 px


{
    "user_id": 1,
    "items": [
        {
            "name": "Burger",
            "description": "Juicy grilled beef burger",
            "quantity": 2,
            "price": 9.99,
            "category": "Fried",
            "subtotal": 19.98
        },
        {
            "name": "Soda",
            "description": "Refreshing cola drink",
            "quantity": 1,
            "price": 5.00,
            "category": "Beverage",
            "subtotal": 5.00
        }
    ]
}


#================================================================
Order Payload
{
    "order_id": 1,
    "user_id": 1,
    "status": "pending",
    "total_price": 25.98,
    "items": [
        {
            "name": "Burger",
            "description": "Juicy grilled beef burger",
            "quantity": 2,
            "price": 9.99,
            "category": "Fried",
            "subtotal": 19.98
        },
        {
            "name": "Soda",
            "description": "Refreshing cola drink",
            "quantity": 1,
            "price": 5.00,
            "category": "Beverage",
            "subtotal": 5.00
        }
    ]
}