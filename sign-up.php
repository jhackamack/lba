<?php
error_reporting(-1);
ini_set('display_errors', 'On');

if(!isset($_POST["EMAIL"])) {
  header( 'Location: index.html#sign-up' ) ; 
  die();
}

$to      = $_POST["EMAIL"];
$subject = 'Welcome to Laundry by Alex';
$message = "put HTML CONTENT HERE";
$headers = 'From: sign-up@laundry.press' . "\r\n" .
    'Reply-To: alex@laundry.press' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

//mail($to, $subject, $message, $headers);
header( 'Location: index.html#sign-up' ) ; 
