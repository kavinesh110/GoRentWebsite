# Hasta Travel Car Rental Management System

## Project Overview
Hasta Travel Car Rental Management System is a web-based application developed using **Laravel** to manage car rental operations for **UTM customers** and **Hasta Travel staff/admin**.

The system supports structured booking workflows, manual payment verification, vehicle inspections, and administrative management. This project is developed as part of a **Software Engineering / Database Systems academic project**, following **SRS, ERD, and Agile sprint-based development practices**.

---

## Project Objectives
- Provide a centralized car rental platform for Hasta Travel
- Allow staff and admin to manage vehicles, bookings, and inspections
- Maintain a clear separation between customer-facing and admin-facing features
- Implement a scalable and normalized database design
- Follow software engineering best practices in documentation and development

---

## User Roles

### Customer (UTM Student / Staff)
- Register and manage account
- Make car rental bookings
- Upload payment receipts
- View booking history
- Submit feedback after rental

### Hasta Staff / Admin
- Verify customer documents
- Manage vehicles and availability
- Create and manage bookings
- Conduct vehicle inspections
- Manage payments, deposits, and penalties
- View reports and system activities

---

## System Modules

### Account Module
- Authentication and authorization
- Profile and password management
- Role-based access control

### Rental Module (Customer)
- Browse available cars
- Make bookings (date, time, pickup and drop-off)
- View booking status and history

### Rental Module (Staff)
- Create bookings on behalf of customers
- Perform pickup and return inspections
- Update car status (Available, In Use, Maintenance)

### Payment and Penalty Module
- Deposit handling
- Rental payment verification
- Late return and damage penalties
- Deposit refund or carry-forward management

### Feedback and Activity Module
- Customer feedback submission
- Display system activities and promotions

---

## Database Design
- Database: **MySQL**
- Managed using **Laragon + HeidiSQL**
- Designed using **Crow’s Foot ERD**
- Images and documents are stored in the **filesystem**
- Database stores **file paths (URLs)** only

### Key Tables
- customer
- staff
- car
- booking
- payment
- inspection
- penalty
- voucher
- rental_photo
- feedback
- maintenance_record
- activity

---

## Design Decisions
- Images are not stored as BLOBs in the database
- One booking may have multiple payments (deposit, rental, penalty)
- One booking may have multiple inspections (pickup and return)
- Each booking has only one feedback record
- Customer and staff authentication are separated for security and usability

---

## Technology Stack

| Layer | Technology |
|------|-----------|
| Backend | Laravel |
| Frontend | Blade, HTML, CSS |
| Database | MySQL |
| Server | Laragon |
| Tools | HeidiSQL, phpMyAdmin |
| Version Control | Git |

---

## Setup Instructions

### Prerequisites
- Laragon (PHP & MySQL)
- Composer
- Node.js (optional)

### Project Setup
```bash
git clone <repository-url>
cd GoRentWebsite
composer install
copy .env.example .env
php artisan key:generate

after line yg atas tu, pls create db named 'hasta' (kalau taktau how, figure out sendiri)

php artisan migrate
php artisan storage:link
php artisan serve
