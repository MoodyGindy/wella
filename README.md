# WellaResin E-commerce Application

This project implements an e-commerce storefront and lightweight admin dashboard for **WellaResin**, built with PHP, HTML/CSS/JavaScript, and MySQL.

## Features
- Public catalog with category filters, product detail pages, favorites, cart, and guest checkout (cash on delivery).
- Session-based cart and favorites (no customer accounts required).
- Order workflow collects customer name, address, and WhatsApp number.
- Instagram feed widget surfaces the most recent posts from [`@wellaresin_33`](https://www.instagram.com/wellaresin_33/).
- Admin dashboard with authentication, product & category management, media uploads, and order review.

## Project Structure
```
assets/
  css/
  js/
config/
  db.php
includes/
  header.php
  footer.php
  functions.php
  instagram.php
public/
  index.php
  products.php
  product.php
  favorites.php
  cart.php
  checkout.php
  order_success.php
api/
  *.php (AJAX handlers)
admin/
  login.php
  logout.php
  index.php
  products.php
  product_form.php
  categories.php
  category_form.php
  orders.php
  order_detail.php
  uploads/
database/
  wellaresin_schema.sql
```

## Database
- Database name: `wellaresin`
- Username: `wellaresin`
- Password: `wellaresin`

Tables:
- `admin_users`
- `categories`
- `products`
- `product_images`
- `orders`
- `order_items`

## Environment
- PHP 8.x (MAMP default)
- MySQL 8.x
- Sessions enabled (`session_start()`).

## Next Steps
- Implement database connection helper and utility functions.
- Build public frontend pages and shared layout partials.
- Create AJAX endpoints for cart, favorites, and Instagram feed.
- Implement admin dashboard CRUD flows and order management.
- Populate SQL schema and seed data.


