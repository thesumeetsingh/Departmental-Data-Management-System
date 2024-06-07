<?php
include 'suppressError.php';
$token=$_GET["token"];
$token_hash = hash("sha256", $token);

include "connection.php";

$sql="SELECT * FROM user_details 
    WHERE reset_token_hash=?";
    
$stmt=$conn->prepare($sql);
$stmt->bind_param('s', $token_hash);
$stmt->execute();
$result=$stmt->get_result();
$user=$result->fetch_assoc();

if($user===null){
    echo "<script>
    alert('Token not found. Create new request');
  </script>";
  echo '<script>window.location.href = "forgot-password.php";</script>';
}


if(strtotime($user["reset_token_expires_at"])<=time()){
    echo "<script>
    alert('Token expired. Create new request');
  </script>";
    echo '<script>window.location.href = "forgot-password.php";</script>';
};


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Reset Password</title>
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
                                    <h3 class="mb-4 pt-2">Reset Password</h3>
                                </div>
                                <div class="w-100">
                                    <p class="social-media d-flex justify-content-end">
                                        <a href="#" class="d-flex align-items-center justify-content-center"><img
                                                src="images/Jindal logo Revised.png" width="110"></a>
                                    </p>
                                </div>
                            </div>
                            <form  method="POST" class="create-password-form" action="process-password.php">
                                        <input type="hidden" name="token" value="<?= htmlspecialchars($token)?>">
                                    
                                        <div class="form-group mb-3">
                                            <label class="label" for="password">New Password</label>
                                            <input id="password" name="password" type="password" class="form-control" placeholder="Password" required>
                                        </div>
                                    
                                    
                                        <div class="form-group mb-3">
                                            <label class="label" for="confirmPassword">Confirm Password</label>
                                            <input id="confirmPassword" name="confirmPassword" type="password" class="form-control" placeholder="Confirm Password" required>
                                        </div>
                                    
                                

                                <div class="form-group">
                                    <button type="submit" class="form-control btn btn-primary rounded submit px-3">RESET PASSWORD</button>
                                </div>
                            </form>
                            <p class="text-center">Back to Login? <a href="login.php">Log In</a></p>
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
     <script>
                    var password = document.getElementById("password").value;
                    var confirmPassword = document.getElementById("confirmPassword").value;
                                // Validate password and confirm password match
                        if (password !== confirmPassword) {
                            alert("Passwords do not match.");
                            return false;
                        }
     </script>
</body>

</html>
