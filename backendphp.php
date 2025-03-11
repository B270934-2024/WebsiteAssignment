<?php
$servername = "127.0.0.1"; //localhost didn't work
$username = "s2761220";
$password = "!AEZZ)C1aezz0c";
$email="s2761220@ed.ac.uk";
#setcookie("user_id", "", time() - 3600, "/"); // Expire the cookie
#unset($_COOKIE['user_id']); // Remove it from PHP
function generateUUID() {
    return bin2hex(random_bytes(8)); 
  }
  //if the user does not have a current id...
if(!isset($_COOKIE['user_id'])) {
  //create a unique 32character ID  
  $unique_id = generateUUID();
  //make the this a cookie saved in the browser, and last 1 week
  setcookie("user_id", $unique_id, time() + (86400 * 7), "/");
  $user_id=$_COOKIE['user_id'];
  #echo $unique_id;
  } else {
	//user's id becomes what it was   
	  $user_id=$_COOKIE['user_id'];
  }


try {
  $conn = new PDO("mysql:host=$servername;dbname=s2761220_website", $username, $password);
  // if it doesn't work we show what follows
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //echo "Connected successfully";  
  echo <<<_HEAD
	<html>
	<head>
	<title>Simple Protein Search</title>  
	</head>
	<body>
	_HEAD;
	if(isset($_POST['organism']) && isset($_POST['protein'])){
	$command = escapeshellcmd("EMAIL=" . escapeshellarg($email) . " python3 Backend.py " . escapeshellarg($user_id) . " " . escapeshellarg($_POST['organism']) . " " . escapeshellarg($_POST['protein']));
		$output = shell_exec($command);
		echo "<pre>$output</pre>";
		echo "<p>done</p>";
		echo "<p>Your user id is $user_id</p>";
		###echo "<img src='/public_html/plotcon.1.png' alt=\"A plotcon graph generated from your search\">";
		echo "<img src=plotcon.1.png>";

	echo "<a href='{$user_id}results.zip'>
            <button>Download Results</button>
          </a>";
       

	} else {
	echo <<<_FORM
	<form action="backendphp.php" method="post">
	<pre><font face ="arial">
	Welcome to the simple protein searcher. Please enter the organism and protein you are trying to learn about below!
	<input type="text" value="aves" name="organism"/>
	<input type="text" value="glucose-6-phosphatase" name="protein"/>
	<input type="submit" value="Search"/>
	</pre>
	</form>
	_FORM;


	
}
	echo <<<_TAIL
	</body>
	</html>
	_TAIL;
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

?>
