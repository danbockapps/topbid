<?php
$to = "danbock@gmail.com";
$subject = "Test mail";
$message = "Hello! This is a simple email message.";
$from = "danbock@gmail.com";
$headers = "From:" . $from;
mail($to,$subject,$message,$headers);
echo "Mail Sent.";
?>

