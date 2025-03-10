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
  function generateUUID() {
    return bin2hex(random_bytes(16)); // 
  }
  //if the user does not have a current id...
  if(!isset($_COOKIE['user_id'])) {
  //create a unique 64character ID
  $unique_id = generateUUID();
  //make the this a cookie saved in the browser, and last 30 days
  setcookie("user_id", $unique_id, time() + (86400 * 30), "/");
  echo $unique_id
  }
  else {
	//user's id becomes what it was    
	  $user_id=$_COOKIE['user_id'];
  }
	$command = escapeshellcmd("python3 Backend.py " . escapeshellarg($user_id));
	$output = shell_exec($command);
	echo $output;
  


} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

?>
