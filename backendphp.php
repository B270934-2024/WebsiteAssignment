<?php
$servername = "127.0.0.1"; //localhost didn't work
$username = "s2761220";
$password = "!AEZZ)C1aezz0c";
$email="s2761220@ed.ac.uk";
#setcookie("user_id", "", time() - 3600, "/"); // Expire the cookie
#unset($_COOKIE['user_id']); // Remove it from PHP
include 'functions.php';

//if the user does not have a current id (saved session)...
//$user_id=0 if not =0 etc etc etc 

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
 //$user_id="087d47dde52b98f6";
  }
try {
  #$conn = new PDO("mysql:host=$servername;dbname=s2761220_website", $username, $password);
  $conn = new PDO("mysql:host=$servername;dbname=s2761220_website", $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    PDO::MYSQL_ATTR_LOCAL_INFILE => true
  ]);
  // if it doesn't work we show what follows
  //echo "Connected successfully"; 
maketables($conn); 
echo <<<_HEAD
	<html>
	<head>
	<title>Simple Protein Search</title>  
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style/style.css">
	
	</head>
	<body>
	<div style="padding-left: 75px; padding-right: 75px;">
	_HEAD;
	if(isset($_POST['organism']) && isset($_POST['protein'])){
	$command = escapeshellcmd("EMAIL=" . escapeshellarg($email) . " python3 Backend.py " . escapeshellarg($user_id) . " " . escapeshellarg($_POST['organism']) . " " . escapeshellarg($_POST['protein']));
	$output = shell_exec($command);
	###echo $output;
	echo "<div class='mainheader'>The search for {$_POST['protein']} in {$_POST['organism']} has concluded successfully!</div>";
	###echo "<img src='/public_html/plotcon.1.png' alt=\"A plotcon graph generated from your search\">";
	echo "<div class='plotimage'><img src=plotcon.1.png alt='A plotcon graph generated from your search'></div>";
	echo "<div class = 'downloader'>
	<a href='{$user_id}results.zip'>
	<button>Download Results</button>
	</a>
	</div>";
	echo "<p>Your id is: $user_id. This is displayed in front of your results.</p>";
	
	$file_pathpep = $_POST['organism'] . "_" . $_POST['protein'] . "_" . "{$user_id}pepresults.txt";
	$file_pathali = $_POST['organism'] . "_" . $_POST['protein'] . "_" . "{$user_id}alignment.fasta";
	$file_pathpro = $_POST['organism'] . "_" . $_POST['protein'] . "_" . "{$user_id}resultsprosite.tsv";
	uploadtsv($file_pathpro,$conn,"pro_table",["SeqName",	"Start",	"End","Score",	"Strand",	"Motif"],$user_id);
	uploadtsv($file_pathpep,$conn,"pep_table",["SeqName",	"MolecularWeight",	"ResidueCount",	"ResidueWeight",	"Charge",	"IsoelectricPoint",	"ExtinctionReduced",	"ExtinctionBridges",	"ReducedMgMl",	"BridgeMgMl",	"Probability_pos_neg"],$user_id);
	uploadfasta($file_pathali,$conn,$user_id);
	echo"<a href='Results.php'>
    	<button type='button'>Browse Results</button>
	</a>";
	#$input = isset($_GET['search']) ? $_GET['search'] : null;
	#displayTable($conn,"$input"); 
} else {
	echo <<<_FORM
	<form action="backendphp.php" method="post">
	<pre><font face ="arial">
	<div class = "mainheader" >Welcome to the simple protein searcher.</div>
	Please enter the organism and protein you are trying to learn about below!
	<div class="central-entry">
	<input type="text" value="aves" name="organism"/>
	<input type="text" value="glucose-6-phosphatase" name="protein"/>
	<input type="submit" value="Search"/>
	</div>
	</pre>
	<a href='Default.php'>
	<button type='button'>Browse Default Results</button>
	</a>
	</form>
	_FORM;
	if (file_exists("{$user_id}pepresults.txt")){
        echo "<a href='Results.php'>
        <button type='button'>Browse Stored Results</button>
	</a>";
}
	echo <<<_TAIL
	</body>
	
	</html>
	_TAIL;
}} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

?>
