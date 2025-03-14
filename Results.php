<?php
$servername = "127.0.0.1"; //localhost didn't work
$username = "s2761220";
$password = "!AEZZ)C1aezz0c";
$email="s2761220@ed.ac.uk";
include'functions.php';
$user_id=$_COOKIE['user_id'];
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
echo	"<h1>Your Database</h1>";
echo "<form method='GET' action=''>
        <label for='search'>Search by Sequence Name: Enter comma separated list of SeqNames or 'all'.
Alternatively, type MOTIF, and your chosen sequence, to investigate that further.</label>
        <p>Your User ID is: {$user_id}</p>
        <div class='d-flex justify-content-between gap-3 mt-3'>
        <input type='text' class = 'form-control me-2' id='search' name='search' placeholder='Enter SeqName or all...' required>
        <button type='submit' class='btn btn-primary'>Search</button>
        </div></form>";
echo "<div class='d-flex gap-3 mt-3 align-items-stretch'>
        <a href='backendphp.php' class='btn btn-secondary flex-grow-1' style='height: 50px;'>Back to Search</a>
	<a href='{$user_id}results.zip'class='btn btn-secondary flex-grow-1' style='height: 50px';>Download Results</a>
	</div>";

echo "<form method='POST' action='' class='d-flex justify-content-center'>
	<input type='hidden' name='clear_results' value='1'>
    <button type='submit' class='btn btn-secondary w-50' style='height: 50px;'>Clear My Results</button>
</div>
</form>";


$input = isset($_GET['search']) ? $_GET['search'] : 'all';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["clear_results"])) {
    clearUserResults($conn, $user_id);
	echo "<p>Your results have been cleared. Please return to the previous page.</p>";
}

if (isset($_GET['search'])) {
    $input = $_GET['search'];
    if ($input === "DELETE!AEZZ)C1aezz0c") {
        $stmt = $conn->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $conn->exec("DROP TABLE IF EXISTS `$table`");
        }
        exit; // Stop execution after deleting tables
    } elseif ($input === "") {
        exit; // Stop execution if search is empty
    }

    displayTable($conn,$user_id,$input);
}
echo "</div></body>";
?>
