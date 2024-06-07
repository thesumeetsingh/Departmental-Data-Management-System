# Departmental Data Management System (DDMS)
## Authors

- [@thesumeetsingh](https://www.github.com/thesumeetsingh)


## Overview
The Departmental Data Management System (DDMS) is a full-stack web application designed for Jindal Steel and Power Limited. This platform allows employees to sign up and log in to manage departmental data through Excel file uploads. The system ensures secure data handling and role-based access, enabling employees to update and view data specific to their department while allowing administrators to manage data across all departments.

## Features
- **User Registration and Authentication:**
  - Employees can sign up using their personal details and department information.
  - Login functionality with session management for secure access.

- **Role-Based Access:**
  - **Department Employees:**
    - Upload and view Excel data related to their specific department.
    - Restriction to ensure employees can only manage their department's data.
  - **Admin:**
    - View and update data for all departments.
    - Full access to manage the data across the organization.

- **Excel File Handling:**
  - User-friendly interface to upload and view data.
- **Reset Password**
  - Sends reset password link with special token for limited time to ensure security in resetting passwords.

## Web Pages
- `signup.php`: Allows employees to register with their personal and departmental details.
- `login.php`: Enables employees to log in using their credentials.
- `dept.php`: Interface for employees to upload and manage departmental data.
- `depttable.php`: View uploaded data specific to the employee's department.
- `admin.php`: Admin interface to view data across all departments.
- `updateadmin.php`: Allows the admin to update data for any department.
- `forgot-password`: Allows user to enter their email and sends a secure token link to that user's email address for limited time period.
- `create-password`: User creates new password with secure hashing.


## Database Schema
- `userdetails`: Stores sign-up data of all users.
- `power_table`: Centralized table storing data for all departments.
- `SMS`: Stores data for the SMS department.
- `nspl`: Stores data for the NSPL department.
- `spm`: Stores data for the SPM department.
- `railmill`: Stores data for the Railmill department.
- `platemill`: Stores data for the Platemill department.
- `jldc`: Stores data for the JLDC department.

## Technologies Used
- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Session Management:** PHP sessions for secure login.
- **Hashing:** Password hashing is used to ensure security.
- **Gmail SMTP:** Gmail's smtp server is used to send email to user for resetting passwords.

## Setup Instructions
1. Clone the repository:
   ```bash
   https://github.com/thesumeetsingh/Departmental-Data-Management-System.git
   ```
2. Navigate to project folder:
   ```bash
   cd Departmental-Data-Management-System.git
   ```
3. Setup the Database:
  - Import the provided SQL script (database.sql) to create the required tables in MySQL.

4. Configure the database connection:
  - Update the database connection details in the config.php file.

5. Run the application
  - Deploy the application on a local or remote server with PHP support.

## Usage Instructions
1. **Sign Up:**
   - Navigate to the `signup.php` page and fill in the required details.
2. **Log In:**
   - Go to the `login.php` page and enter your credentials.
3. **Upload Data:**
   - Department employees can upload their department-specific Excel data via the `dept.php` page.
   - Admins can upload data for any department via the `updateadmin.php` page.
4. **View Data:**
   - Employees can view their department's data using the `depttable.php` page.
   - Admins can view data from all departments using the `admin.php` page.

## Contributing
We welcome contributions to enhance the functionality of DDMS. Please fork the repository and submit pull requests for any improvements or bug fixes.


## Contact
For any inquiries or support, please contact:
- **Name:** Sumeet Singh
- **Email:** sumeetsingh273152@gmail.com
- **LinkedIn:** [Sumeet Singh](https://www.linkedin.com/in/iam-sumeet-singh/)
