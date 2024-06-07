<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include "connection.php";
    include 'suppressError.php';
    $token=$_POST["token"];
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
        alert('Token not found');
        </script>";
        exit;
    }


    if(strtotime($user["reset_token_expires_at"])<=time()){
        die("token has expired");
        exit;
    };
    $password = $_POST['password'];
    echo "you entered $password";
    $stmt = $conn->prepare("SELECT * FROM user_details WHERE reset_token_hash = ?");
    $stmt->bind_param("s", $token_hash);
    echo "token hash is $token_hash";
    $stmt->execute();
    echo "executed";
    $result = $stmt->get_result();
    echo "result collected";

    if ($result->num_rows > 0) {
        $hashedPassword=password_hash($password, PASSWORD_DEFAULT);
        // Insert user details into the database
        $stmt = $conn->prepare("UPDATE user_details SET PASSWORD=? WHERE reset_token_hash=?");
        $stmt->bind_param("ss", $hashedPassword, $token_hash); 
        if ($stmt->execute()) {
            echo '<script>alert("Password Changed successfully.");</script>';
            $stmtDeleteToken=$conn->prepare("UPDATE user_details SET reset_token_hash=NULL, reset_token_expires_at=NULL WHERE reset_token_hash=?");
            $stmtDeleteToken->bind_param('s', $token_hash);
            $stmtDeleteToken->execute();
            // Redirect to login.php after reset
            echo '<script>window.location.href = "login.php";</script>';
            exit; // Stop further execution after redirection
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

?>