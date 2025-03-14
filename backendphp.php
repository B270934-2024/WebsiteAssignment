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
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<title>Simple Protein Search</title>  
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style/style.css">
	</head>
	<body>
	_HEAD;
	if(isset($_POST['organism']) && isset($_POST['protein'])){
	$command = escapeshellcmd("EMAIL=" . escapeshellarg($email) . " python3 Backend.py " . escapeshellarg($user_id) . " " . escapeshellarg($_POST['organism']) . " " . escapeshellarg($_POST['protein']));
	$output = shell_exec($command);
	###echo $output;
	echo "<div class='mainheader'>The search for {$_POST['protein']} in {$_POST['organism']} has concluded successfully!</div>";
	###echo "<img src='/public_html/plotcon.1.png' alt=\"A plotcon graph generated from your search\">";
	echo "<div class='plotimage'><img src=plotcon.1.png alt='A plotcon graph generated from your search'></div>";
	echo "	<a href='{$user_id}results.zip' class='btn btn-secondary d-flex'>Download Results</a>";
	$file_pathpep = $_POST['organism'] . "_" . $_POST['protein'] . "_" . "{$user_id}pepresults.txt";
	$file_pathali = $_POST['organism'] . "_" . $_POST['protein'] . "_" . "{$user_id}alignment.fasta";
	$file_pathpro = $_POST['organism'] . "_" . $_POST['protein'] . "_" . "{$user_id}resultsprosite.tsv";
	uploadtsv($file_pathpro,$conn,"pro_table",["SeqName",	"Start",	"End","Score",	"Strand",	"Motif"],$user_id);
	uploadtsv($file_pathpep,$conn,"pep_table",["SeqName",	"MolecularWeight",	"ResidueCount",	"ResidueWeight",	"Charge",	"IsoelectricPoint",	"ExtinctionReduced",	"ExtinctionBridges",	"ReducedMgMl",	"BridgeMgMl",	"Probability_pos_neg"],$user_id);
	uploadfasta($file_pathali,$conn,$user_id);
	echo "<a href='Results.php?search=all' class='btn btn-secondary d-flex'>Browse Results</a>";
	#$input = isset($_GET['search']) ? $_GET['search'] : null;
	#displayTable($conn,"$input"); 
} else {
	echo <<<_FORM
	<form action="backendphp.php" method="post">
	<pre><font face ="arial">
	<h1>Welcome to the simple protein searcher.</h1>
	<p class='d-flex'>Please enter the organism and protein you are trying to learn about below!</p>
	<div class="h20">
	<input type="text" class="form-control" value="Aves" name="organism"/>
	<input type="text" class="form-control" value="Glucose-6-phosphatase" name="protein"/>
	<input type="submit" class = 'btn btn-primary' value="Search"/>
	</div>
	</pre>
	<a href='Default.php?search=all' class = 'btn btn-secondary d-flex'>Browse Default Results</a>
	</form>
	_FORM;

$stmt = $conn->prepare("SELECT * FROM pep_table  WHERE user_id = ?");
$stmt->execute([$user_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if ($result){
        echo "<a href='Results.php?search=all' class='btn btn-secondary d-flex' >Browse Stored Results</a>";
}
	echo <<<_TAIL
	</body>
	
	</html>
	_TAIL;
}} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

?>
