#!/usr/local/bin/php
<?php
$servername = "127.0.0.1"; //localhost didn't work
$username = "s2761220";
$password = "!AEZZ)C1aezz0c";
try {
  $conn = new PDO("mysql:host=$servername;dbname=s2761220_website", $username, $password);
  // if it doesn't work we show what follows
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //echo "Connected successfully";
  



} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

?>
