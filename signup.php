<?php
    //suppress error
    include 'suppressError.php';

    // Create connection
    include 'connection.php';
    // Check if form is submitted

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Fetch form data
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $department = $_POST['department']; // Added department variable
        $userLocation=$_POST['userLocation'];

        // Check if username already exists
        $stmt = $conn->prepare("SELECT * FROM user_details WHERE USERNAME = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<script>alert("Username already taken. Please choose a different username.");</script>';
            echo '<script>window.location.href = "signup.php";</script>';
            exit; // Stop further execution after redirection
        } else {
            $hashedPassword=password_hash($password, PASSWORD_DEFAULT);
            // Insert user details into the database
            $stmt = $conn->prepare("INSERT INTO user_details (FIRSTNAME, LASTNAME, USERNAME, PASSWORD, EMAILADD, PHONENUMBER, AGE, GENDER, DEPT, USERLOCATION) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
            $stmt->bind_param("ssssssisss", $firstName, $lastName, $username, $hashedPassword, $email, $phone, $age, $gender, $department, $userLocation); // Added department binding
            
            if ($stmt->execute()) {
                echo '<script>alert("User registered successfully.");</script>';
                // Redirect to login.php after successful registration
                echo '<script>window.location.href = "login.php";</script>';
                exit; // Stop further execution after redirection
            } else {
                echo "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    } else {
        //echo "Invalid request."; // Handle non-POST requests
    }
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
        <title>Signup</title>
        <link rel="icon" type="image/x-icon" href="/images/favicon.png">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <section class="ftco-section" style="background: rgb(177,176,160);
        background: linear-gradient(90deg, rgba(177,176,160,1) 0%, rgba(204,162,114,1) 100%);">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12 col-lg-10">
                        <div class="wrap d-md-flex">
                            <div class="img" style="background-image: url(images/bg-1.jpg);"></div>
                            <div class="login-wrap p-4 p-md-5">
                                <h3 class="mb-4 pt-2">Sign Up</h3>
                                <form action="signup.php" method="POST" class="signup-form" onsubmit="return validateForm()">
                                <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label" for="firstName">First Name</label>
                                                <input id="firstName" name="firstName" type="text" class="form-control" placeholder="First Name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label" for="lastName">Last Name</label>
                                                <input id="lastName" name="lastName" type="text" class="form-control" placeholder="Last Name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="label" for="username">Username</label>
                                        <input id="username" name="username" type="text" class="form-control" placeholder="Username" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="label" for="email">Email Address</label>
                                        <input id="email" name="email" type="email" class="form-control" placeholder="Email Address" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label" for="password">Password</label>
                                                <input id="password" name="password" type="password" class="form-control" placeholder="Password" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label" for="confirmPassword">Confirm Password</label>
                                                <input id="confirmPassword" name="confirmPassword" type="password" class="form-control" placeholder="Confirm Password" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label" for="phone">Phone Number</label>
                                                <input id="phone" name="phone" type="tel" class="form-control" placeholder="Phone Number" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label" for="age">Age</label>
                                                <input id="age" name="age" type="number" class="form-control" placeholder="Age" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="label">Gender</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="male" value="male" checked>
                                            <label class="form-check-label" for="male">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                                            <label class="form-check-label" for="female">Female</label>
                                        </div>
                                    </div>
                                    <!-- Department dropdown -->
                                    <div class="form-group mb-3">
                                        <label class="label" for="department">Department</label>
                                        <select id="department" name="department" class="form-control" required>
                                            <option value="">Select Department</option>
                                            <option value="SMS">SMS</option>
                                            <option value="RAILMILL">RAILMILL</option>
                                            <option value="PLATEMILL">PLATEMILL</option>
                                            <option value="SPM">SPM</option>
                                            <option value="NSPL">NSPL</option>
                                            <option value="JLDC">JLDC</option>
                                        
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="label" for="userLocation">Location</label>
                                        <select id="userLocation" name="userLocation" class="form-control" required>
                                            <option value="">Select Location</option>
                                            <option value="RAIGARH">RAIGARH</option>
                                            <option value="TAMNAR">TAMNAR</option>
                                            <option value="ANGUL">ANGUL</option>
                                            <option value="PATRATU">PATRATU</option>
                                            <option value="NSPL">NSPL</option>          
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="form-control btn btn-primary rounded submit px-3">Sign Up</button>
                                    </div>
                                    <div class="form-group text-center">
                                        <p class="text-center">Already have an account? <a href="login.php">Login</a></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="js/main.js"></script>
    <script>
    function validateForm() {
                var firstName = document.getElementById("firstName").value.trim();
                var lastName = document.getElementById("lastName").value.trim();
                var username = document.getElementById("username").value.trim();
                var email = document.getElementById("email").value.trim();
                var phone = document.getElementById("phone").value.trim();
                var age = document.getElementById("age").value.trim();
                var password = document.getElementById("password").value;
                var confirmPassword = document.getElementById("confirmPassword").value;
                var department = document.getElementById("department").value;
                var userLocation = document.getElementById("userLocation").value;
                // Validate first name and last name (should not contain numbers)
                var nameRegex = /^[a-zA-Z\s]*$/;
                if (!nameRegex.test(firstName) || !nameRegex.test(lastName)) {
                    alert("First Name and Last Name should only contain alphabets and spaces.");
                    return false;
                }
                // Validate email format
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert("Please enter a valid email address.");
                    return false;
                }
                // Validate phone number (10 digits, positive)
                var phoneRegex = /^\d{10}$/;
                if (!phoneRegex.test(phone)) {
                    alert("Please enter a valid 10-digit phone number.");
                    return false;
                }
                // Validate password and confirm password match
                if (password !== confirmPassword) {
                    alert("Passwords do not match.");
                    return false;
                }
                // Validate department selection
                if (department === "") {
                    alert("Please select a department.");
                    return false;
                }
                if (userLocation === "") {
                    alert("Please select a Location.");
                    return false;
                }
                return true;
            }
        </script>
    </body>
</html>