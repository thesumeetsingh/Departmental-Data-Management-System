<?php
include 'suppressError.php';

session_start();
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "powerdb";

// Create connection
include 'connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username exists
    $stmt = $conn->prepare("SELECT * FROM user_details WHERE USERNAME = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username exists, check password
        $user = $result->fetch_assoc();
        $hashedPassword = $user['PASSWORD'];
        if (password_verify($password, $hashedPassword)) {
            // Password is correct, retrieve email and department
            $email = $user['EMAILADD'];
            $dept = $user['DEPT'];
            $userLocation=$user['USERLOCATION'];
            $_SESSION['username'] = $username;
            $_SESSION['useremail'] = $email;
            $_SESSION['dept'] = $dept;
            $_SESSION['userLocation']=$userLocation;
            
            // Redirect based on user's department and role
            if ($dept == 'ADMIN') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            // Wrong password, redirect with alert
            header("Location: login.php?msg=wrong_password");
            exit();
        }
    } else {
        // Username not found, redirect with alert
        header("Location: login.php?msg=username_not_found");
        exit();
    }

    $stmt->close();
} else {
    // Handle non-POST requests
    //echo "Invalid request.";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>
<link rel="icon" type="image/x-icon" href="/images/favicon.png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

.ftco-section {
    min-height: 100%;
}

/* .wrap {
    display: flex;
    align-items: stretch;
    height: 100%;
} */
.ftco-section {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh; 
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

    </style>
</head>

<body>
    <section class="ftco-section" style="background: rgb(177,176,160);
    background: linear-gradient(90deg, rgba(177,176,160,1) 0%, rgba(204,162,114,1) 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-10">
                    <div class="wrap d-md-flex">
                        <div class="img" style="background-image: url(images/bg-1.jpg);">
                        </div>
                        <div class="login-wrap p-4 p-md-5">
                            <div class="d-flex">
                                <div class="w-100">
                                    <h3 class="mb-4 pt-2">Sign In</h3>
                                </div>
                                <div class="w-100">
                                    <p class="social-media d-flex justify-content-end">
                                        <a href="#" class="d-flex align-items-center justify-content-center"><img
                                                src="images/Jindal logo Revised.png" width="110"></a>
                                    </p>
                                </div>
                            </div>
                            <form action="login.php" method="POST" class="signin-form">
                                <div class="form-group mb-3">
                                    <label class="label" for="name">Username</label>
                                    <input id="txtuser" name="username" type="text" class="form-control" placeholder="Username" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="label" for="password">Password</label>
                                    <input name="password" type="password" class="form-control" placeholder="Password" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="form-control btn btn-primary rounded submit px-3">LOGIN</button>
                                </div>
                                <div class="form-group d-md-flex">
                                    <div class="w-50 text-left">
                                        <label class="checkbox-wrap checkbox-primary mb-0">Remember Me
                                            <input type="checkbox" checked>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="w-50 text-md-right">
										<a href="forgot-password.php">Forgot Password</a>
									</div>
                                    
                                </div>
                            </form>
                            <p class="text-center">Not a member? <a href="signup.php">Sign Up</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap core JS and other libraries -->
    
	 <!-- Bootstrap core JS-->
	 <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
	 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
 
	 <!-- JavaScript Libraries -->
	 <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	 <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
	 <script src="js/main.js"></script>
	 
    <!-- Include necessary JS libraries and scripts -->
</body>

</html>

<script>
    // Function to get URL parameter by name
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Check if there is a message in the URL
    var msg = getUrlParameter('msg');
    if (msg === 'wrong_password') {
        alert("Wrong password. Please try again.");
    } else if (msg === 'username_not_found') {
        alert("Username not found. You can register here.");
    }
</script>
