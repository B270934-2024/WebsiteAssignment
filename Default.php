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
	<link rel="stylesheet" href="style/style.css">
	</head>
	<body>
	<div style="padding-left: 75px; padding-right: 75px;">
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
Alternatively, type MOTIF, and your chosen sequence, to investigate that further.</label>
	<p></p>
	<input type='text' id='search' name='search' placeholder='Enter SeqName or all...' required>
    	<button type='submit'>Search</button>
	</form>";
maketables($conn);
$input = isset($_GET['search']) ? $_GET['search'] : 'all';
###if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["clear_results"])) {
###    clearUserResults($conn, $user_id);
###	echo "<p>Your results have been cleared. Please return to the previous page.</p>";
###}
###echo"<form method='POST' action=''>
###    <input type='hidden' name='clear_results' value='1'>
###    <button type='submit' class='btn btn-danger'>Clear My Results</button>
###</form>";
if (isset($_GET['search'])) {
    $input = $_GET['search'];
displayTable($conn,"087d47dde52b98f6",$input);  
} elseif ($input === "") {
        exit; // Stop execution if search is empty
 }
echo "<a href='backendphp.php'>
<button> back </button>
</a>";
echo "<div class = 'downloader'>
        <a href='087d47dde52b98f6results.zip'>
        <button>Download Results</button>
        </a>
	</div>";


echo "</div></body>";
?>

