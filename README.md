# 🏛️ Hall Booking Management System

This is a **Hall Booking Web Application** developed using **PHP** and **MySQL** for the backend. The system allows **users** to sign up, log in, and book available halls. **Admins** can manage bookings and users efficiently.

---

## 🔧 Tech Stack

- **Backend Language**: PHP
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (Basic)
- **Tools**: XAMPP/WAMP, phpMyAdmin

---

## 📁 Project Features

### 👤 User Features
- ✅ User Registration (Sign Up)
- ✅ Secure User Login
- ✅ Book a Hall (based on date and time availability)
- ✅ Check Hall Availability Before Booking
- ✅ View Booking History
- ✅ Edit User Profile

### 🛠️ Admin Features
- ✅ Admin Login
- ✅ View All Hall Bookings
- ✅ Manage User Accounts
- ✅ Approve or Reject Bookings (if implemented)
- ✅ Add/Edit/Delete Halls (optional if used)

---

## 🗄️ Database

The system uses **two main MySQL tables**:

1. **Users Table**
   - Stores user credentials and information
   - Fields: `id`, `name`, `email`, `password`, `role`, `created_at`

2. **Bookings Table**
   - Stores booking details
   - Fields: `booking_id`, `user_id`, `hall_name`, `date`, `start_time`, `end_time`, `status`

> Database connection is handled via PHP `mysqli` or `PDO`.

---

## 🚀 How to Run the Project Locally

1. **Clone this Repository**:
   ```bash
   git clone https://github.com/Pasindu115530/hall_booking.git
