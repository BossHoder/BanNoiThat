# Nội Thất Theanhdola E-commerce Platform

Welcome to the Nội Thất Theanhdola E-commerce Platform. This project is a web-based application designed to facilitate the online sale of furniture products. The platform includes features such as product browsing, detailed product views, and a checkout process.

## Table of Contents

- [Project Structure](#project-structure)
- [Installation](#installation)
- [Usage](#usage)
- [Features](#features)
- [Contributing](#contributing)
- [License](#license)

## Project Structure

The project is organized into several key files and directories:

- `product-details.php`: Handles the display of individual product details.
- `index.php`: The main landing page that lists available products.
- `checkout.php`: Manages the checkout process for users.
- `asset/css`: Contains CSS files for styling the application.
- `asset/img`: Stores images used in the application.

## Installation

To set up the project locally, follow these steps:

1. **Clone the Repository:**

   ```bash
   git clone https://github.com/yourusername/yourrepository.git
   cd yourrepository
Copy
Insert

Set Up the Database:
Ensure you have a MySQL server running.
Create a database named your_database_name.
Import the SQL file provided in the database directory to set up the necessary tables.
Configure the Database Connection:
Open conn.php and update the database connection parameters (host, username, password, database) to match your local setup.
Start the PHP Server:
Run the following command in the project directory:
php -S localhost:8000
Copy
Insert

Access the Application:
Open your web browser and go to http://localhost:8000.
Usage
Product Browsing: Navigate to the homepage to view a list of available products.
Product Details: Click on a product to view detailed information, including price and description.
Checkout: Add products to your cart and proceed to the checkout page to complete your purchase.
Features
Responsive Design: The application is designed to be responsive and user-friendly across various devices.
Secure Transactions: Utilizes prepared statements to prevent SQL injection attacks.
User-Friendly Interface: Easy navigation with a clean and modern design using Tailwind CSS and Font Awesome.
Contributing
We welcome contributions to improve the platform. To contribute:

Fork the repository.
Create a new branch for your feature or bug fix.
Commit your changes and push to your fork.
Submit a pull request with a detailed description of your changes.
License
This project is licensed under the MIT License. See the LICENSE file for more details.

Thank you for using the Nội Thất Theanhdola E-commerce Platform. We hope you enjoy the experience!


### Notes:
- Replace `yourusername` and `yourrepository` with your actual GitHub username and repository name.
- Ensure the database name and connection details in the `Installation` section match your actual setup.
- Add any additional secti