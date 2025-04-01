<?php
$servername = "127.0.0.1"; //localhost didn't work
$username = "s2761220";
$password = "!AEZZ)C1aezz0c";
$email="s2761220@ed.ac.uk";
include'functions.php';
$user_id=$_COOKIE['user_id'];
echo <<<_HEAD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protein Database - ProteinExplorer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            padding-top: 80px; 
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #003366 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            color: #fff !important;
            font-weight: 600;
            letter-spacing: 0.05em;
        }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #fff !important;
            transform: translateY(-1px);
        }
    </style>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="backendphp.php">ProteinExplorer</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="Default.php?search=all">Default Results</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="HelpAndContext.php">Help</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="CreditAndContacts.php">Credits</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
_HEAD;

$conn = new PDO("mysql:host=$servername;dbname=s2761220_website", $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    PDO::MYSQL_ATTR_LOCAL_INFILE => true
  ]);
echo	"<content><h1>Your Database</h1>";
echo "<form method='GET' action=''>
        <label for='search'>Search by Sequence Name: Enter comma separated list of SeqNames or 'all'.
Alternatively, type 'MOTIF, or ALIGNMENT,' and your chosen sequence, to investigate that further.</label>
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
echo "</div></body></content>";
?>
