# FoodShare - XAMPP Setup Instructions

Follow these steps to run the FoodShare application on your local machine using XAMPP.

## 1. Prerequisites
- Download and install [XAMPP](https://www.apachefriends.org/index.html).
- Ensure Apache and MySQL modules are running in the XAMPP Control Panel.

## 2. Project Placement
- Copy the entire `foodshare-php` folder into your XAMPP's `htdocs` directory.
  - Typical path: `C:\xampp\htdocs\foodshare-php`

## 3. Database Setup
- Open your browser and go to `http://localhost/phpmyadmin/`.
- Create a new database named `foodshare`.
- Click on the `foodshare` database, then go to the **Import** tab.
- Choose the `setup.sql` file located in the project root and click **Import**.

## 4. Run the Application
- Open your browser and visit: `http://localhost/foodshare-php/`

## 5. Test Accounts (Example)
1. **Register as a Donor**: Post some food using the "List New Food" button.
2. **Register as a Receiver**: Log in with a different browser (or logout) and browse the available food.
3. **Request Food**: As a receiver, click "Request This Food".
4. **Approve**: As the donor, go to your dashboard and accept the request.

## Folder Structure
- `/config`: Database connection details.
- `/assets`: CSS styles.
- `/views/layout`: Reusable header and footer.
- `index.php`: Landing page.
- `dashboard.php`: Main hub for logged-in users.
