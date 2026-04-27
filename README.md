# FoodShare - Community Food Sharing Platform

FoodShare is a full-stack web application designed to bridge the gap between event surplus food and local communities in need. It allows donors (event organizers, catering services, individuals) to list excess food and enables receivers (NGOs, community centers) to browse and request it directly via WhatsApp.

## 🚀 Tech Stack

- **Backend**: Core PHP (Version 7.4+)
- **Database**: MySQL (MariaDB)
- **Frontend**: HTML5, Vanilla CSS3 (Custom Design System), JavaScript
- **Server**: Apache (Optimized for XAMPP/WAMP environment)
- **Integration**: WhatsApp API (URL-based redirection)

## 🛠️ Core Features

### 1. Authentication & User Management
- **Dual Roles**: Users register as either a **Donor** or a **Receiver**.
- **Secure Login**: Session-based authentication with `password_hash` encryption.
- **Profile Data**: Collection of contact information (Phone/Email) essential for coordination.

### 2. Donor Functionalities
- **Food Listing**: Post surplus food with details like Name, Quantity, Location, and Expiry.
- **Image Uploads**: Attach photos of the food to build trust and clarity.
- **Manage Listings**: Full CRUD (Create, Read, Update, Delete) capabilities for your own listings.
- **Request Tracking**: View who is interested in your food and coordinate pickups.

### 3. Receiver Functionalities
- **Public Discovery**: Browse available food listings directly on the landing page without needing to log in.
- **Smart Requests**: Request food with a single click, which automatically logs the request and triggers a WhatsApp redirection.
- **WhatsApp Integration**: Automatically generates a pre-filled message to the donor with the receiver's name and email for instant communication.

### 4. Admin (Internal Logic)
- **Status Management**: Automated status updates (Available/Completed) based on donor approvals.

## 📂 Project Structure

```text
/foodshare-php
├── /assets/css/      # Custom CSS design system
├── /config/          # Database connection (PDO)
├── /uploads/         # Directory for food listing images
├── /views/layout/    # Reusable header and footer components
├── index.php         # Public landing page with listings
├── dashboard.php     # Role-based router for users
├── donor_dashboard.php   # Donor management interface
├── receiver_dashboard.php # Receiver discovery interface
├── add_food.php      # Create listing logic
├── edit_food.php     # Update listing logic
├── delete_food.php   # Delete listing logic
├── register.php      # User registration
├── login.php         # User authentication
└── setup.sql         # Database schema and initialization
```

## ⚙️ Installation Instructions (XAMPP)

1.  **Move Files**: Copy the `foodshare-php` folder to `C:\xampp\htdocs\`.
2.  **Start Services**: Open XAMPP Control Panel and start **Apache** and **MySQL**.
3.  **Database Setup**:
    - Go to `http://localhost/phpmyadmin/`.
    - Create a database named `foodshare`.
    - Import the `setup.sql` file provided in the project root.
4.  **Run**: Visit `http://localhost/foodshare-php/` in your browser.

## 📱 WhatsApp Integration Logic
The system uses the `https://api.whatsapp.com/send` endpoint. When a receiver clicks "Avail", the system constructs a message:
> "Hi [Donor Name], I'm interested in this food pickup: [Food Name] from FoodShare! My Details: Name: [Receiver Name], Email: [Receiver Email]"

This ensures a zero-friction communication loop between the two parties.
