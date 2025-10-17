# Canteen Backend API

Backend API untuk sistem manajemen kantin menggunakan Laravel 12 dengan autentikasi Laravel Sanctum.

## 📋 Deskripsi Project

Aplikasi backend untuk sistem kantin yang menyediakan fitur:
- Autentikasi user (Register, Login, Logout)
- Manajemen menu makanan dan minuman
- Keranjang belanja (Cart)
- Pemesanan (Order)
- Role-based access control (Admin & User)

## 🛠️ Tech Stack

- **Laravel**: 12.x
- **PHP**: ^8.2
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **API**: RESTful API

## 📦 Requirements

Pastikan sistem Anda sudah terinstall:

- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM (untuk Vite)
- Git

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/mamahda/canteen_backend.git
cd canteen_backend
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Setup Environment

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=canteen
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Buat Database

Buat database baru di MySQL:

```sql
CREATE DATABASE canteen;
```

### 6. Jalankan Migration & Seeder

```bash
# Jalankan migration untuk membuat tabel
php artisan migrate

# Jalankan seeder untuk data awal
php artisan db:seed
```

Seeder akan membuat:
- 1 akun admin (email: `admin@admin`, password: `admin#123`)
- Data menu makanan (Main Course, Snack, Beverage)

### 7. Setup Storage

```bash
# Buat symbolic link untuk storage
php artisan storage:link
```

### 8. Jalankan Server

```bash
# Development server
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

## 📁 Struktur Database

### Tabel Users
- `id`: Primary Key
- `name`: Nama user
- `email`: Email (unique)
- `password`: Password (hashed)
- `role`: Role user (admin/customer)
- `remember_token`: Token remember me
- `timestamps`: created_at, updated_at

### Tabel Menus
- `id`: Primary Key
- `name`: Nama menu
- `price`: Harga
- `stock`: Stok tersedia
- `image`: Path gambar
- `type`: Tipe menu (Main Course, Snack, Beverage)
- `timestamps`: created_at, updated_at

### Tabel Carts
- `id`: Primary Key
- `user_id`: Foreign Key ke users
- `total_price`: Total harga
- `timestamps`: created_at, updated_at

### Tabel Orders
- `id`: Primary Key
- `user_id`: Foreign Key ke users
- `status`: Status order (pending, completed, cancelled)
- `total_price`: Total harga
- `timestamps`: created_at, updated_at

### Tabel menu_cart (Pivot)
- `menu_id`: Foreign Key ke menus
- `cart_id`: Foreign Key ke carts
- `quantity`: Jumlah item
- `subtotal_price`: Subtotal harga

### Tabel menu_order (Pivot)
- `menu_id`: Foreign Key ke menus
- `order_id`: Foreign Key ke orders
- `quantity`: Jumlah item
- `subtotal_price`: Subtotal harga

## 🔌 API Endpoints

### Authentication

#### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

### Menu Management

#### Get All Menus (Public)
```http
GET /api/menus
GET /api/menus?type=Main Course
```

#### Add Menu (Admin Only)
```http
POST /api/menus
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Nasi Goreng",
  "price": 15000,
  "stock": 50,
  "type": "Main Course"
}
```

#### Upload Menu Image (Admin Only)
```http
POST /api/menus/{id}/image
Authorization: Bearer {token}
Content-Type: multipart/form-data

image: [file]
```

#### Update Menu (Admin Only)
```http
PUT /api/menus/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Nasi Goreng Special",
  "price": 18000,
  "stock": 45,
  "type": "Main Course"
}
```

#### Delete Menu (Admin Only)
```http
DELETE /api/menus/{id}
Authorization: Bearer {token}
```

### Cart Management

#### Get Cart
```http
GET /api/cart
Authorization: Bearer {token}
```

#### Update Cart (Add/Update/Remove items)
```http
PATCH /api/cart
Authorization: Bearer {token}
Content-Type: application/json

{
  "items": [
    {
      "menu_id": 1,
      "quantity": 2
    },
    {
      "menu_id": 3,
      "quantity": 0  // 0 untuk menghapus item
    }
  ]
}
```

## 🔐 Authentication

API menggunakan Laravel Sanctum untuk autentikasi. Setelah login, Anda akan menerima token yang harus disertakan di header setiap request:

```
Authorization: Bearer {your_token}
```

## 👥 User Roles

### Admin
- Mengelola menu (Create, Read, Update, Delete)
- Upload gambar menu
- Akses penuh ke semua fitur

### Customer
- Melihat menu
- Mengelola cart pribadi
- Membuat order
