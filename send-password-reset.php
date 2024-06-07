<?php
date_default_timezone_set('Asia/Kolkata');
$email=$_POST["email"];
$token= bin2hex(random_bytes(16));
$token_hash= hash("sha256", $token);
$expiry= date("Y-m-d H:i:s",time()+(60*30));

include "connection.php";

$sql= "UPDATE user_details 
    SET reset_token_hash=?,
        reset_token_expires_at=?
        WHERE EMAILADD=?";


$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();
// $result = $stmt->get_result();

if ($conn->affected_rows){
    $mail=require __DIR__."/mailer.php";
    $mail->setFrom("noreplay@jindalsteel.com");
    $mail->addAddress($email);
    $mail->Subject="Password Reset";
    $mail-> Body=<<<END
    Click <a href="http://localhost/webportal/createpassword.php?token=$token">Here</a>
    END;

    try{
        $mail->send();
    }catch(Exception $e){
        echo "Message could not be sent. Mailer error: {$mail-> ErrorInfo}";
    }
}
echo "Message sent, Please check your inbox";
?>