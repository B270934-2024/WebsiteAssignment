<?php
$servername = "127.0.0.1"; //localhost didn't work
$username = "s2761220";
$password = "!AEZZ)C1aezz0c";
$email="s2761220@ed.ac.uk";
include 'functions.php';

echo <<<_HEAD
	<html>
	<head>
	 <title>Simple Protein Results</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<link rel="stylesheet" href="style/style.css">
	</head>
	<body>
	
	_HEAD;

$conn = new PDO("mysql:host=$servername;dbname=s2761220_website", $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    PDO::MYSQL_ATTR_LOCAL_INFILE => true
  ]);

##if (isset($_COOKIE['user_id'])) {
##    $user_id = $_COOKIE['user_id'];
##    echo "User ID from cookie: " . htmlspecialchars($user_id);
##} else {
##    echo "No user ID found in cookies.";
##}
echo "<h1>Our default database of Glucose-6-Phosphatase in Aves</h1>";
echo "<form method='GET' action=''>
   	<label for='search'>Search by Sequence Name: Enter comma separated list of SeqNames or 'all'.
Alternatively, type 'MOTIF,' and your chosen sequence, to investigate that further.</label>
	<p></p>
	<div class='d-flex justify-content-between gap-2 mt-2'>
	<input type='text' class = 'form-control me-2' id='search' name='search' placeholder='Enter SeqName or all...' required>
   	<button type='submit' class='btn btn-primary'>Search</button>
	</div></form>";
	echo "<div class='d-flex gap-2 mt-2'>
	<a href='backendphp.php' class='btn btn-secondary flex-grow-1' style='height:50px';>Back to Search</a>
<a href='087d47dde52b98f6results.zip'class='btn btn-secondary flex-grow-1' style='height: 50px';>Download Results</a></div>";

maketables($conn);
$input = isset($_GET['search']) ? $_GET['search'] : 'all';
if (isset($_GET['search'])) {
    $input = $_GET['search'];
displayTable($conn,"087d47dde52b98f6",$input);  
};
?>
