<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require __DIR__."/vendor/autoload.php";

$mail= new PHPMailer(true);
$mail->isSMTP();
$mail->SMTPAuth = true;

$mail -> Host = "smtp.gmail.com";
$mail->SMTPSecure =PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->Username = 'sumeetsingh300103@gmail.com';
$mail->Password= 'rrzatozodrskiwno';
$mail->isHtml(true);

return $mail;





?>