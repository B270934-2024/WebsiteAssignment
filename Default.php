<?php
$servername = "127.0.0.1"; //localhost didn't work
$username = "s2761220";
$password = "!AEZZ)C1aezz0c";
$email="s2761220@ed.ac.uk";
include 'functions.php';

echo <<<_HEAD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protein Database - ProteinExplorer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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

        $file_pathpep = "Aves_Glucose-6-phosphatase_ae16a9801cdd61a0pepresults.txt";
        $file_pathali = "Aves_Glucose-6-phosphatase_ae16a9801cdd61a0alignment.fasta";
        $file_pathpro = "Aves_Glucose-6-phosphatase_ae16a9801cdd61a0resultsprosite.tsv";
        $file_pathtsv = "Aves_Glucose-6-phosphatase_ae16a9801cdd61a0results.tsv";
        uploadtsv($file_pathtsv,$conn,"ts_table",["SeqName","Organism","Length"],"ae16a9801cdd61a0");
        uploadtsv($file_pathpro,$conn,"pro_table",["SeqName",   "Start",        "End","Score",  "Strand",       "Motif"],"ae16a9801cdd61a0");
        uploadtsv($file_pathpep,$conn,"pep_table",["SeqName",   "MolecularWeight",      "ResidueCount", "ResidueWeight",        "Charge",       "IsoelectricPoint",     "ExtinctionReduced",    "ExtinctionBridges",    "ReducedMgMl",  "BridgeMgMl",   "Probability_pos_neg"],"ae16a9801cdd61a0");
        uploadfasta($file_pathali,$conn,"ae16a9801cdd61a0");

##if (isset($_COOKIE['user_id'])) {
##    $user_id = $_COOKIE['user_id'];
##    echo "User ID from cookie: " . htmlspecialchars($user_id);
##} else {
##    echo "No user ID found in cookies.";
##}
echo "<content><body><h1>Our default database of Glucose-6-Phosphatase in Aves</h1>";
echo "<form method='GET' action=''>
   	<label for='search'>Search by Sequence Name: Enter comma separated list of SeqNames or 'all'.
Alternatively, type 'MOTIF, or ALIGNMENT,' and your chosen sequence, to investigate that further.</label>
	<p></p>
	<div class='d-flex justify-content-between gap-2 mt-2'>
	<input type='text' class = 'form-control me-2' id='search' name='search' placeholder='Enter SeqName or all...' required>
   	<button type='submit' class='btn btn-primary'>Search</button>
	</div></form>";
	echo "
                            <div class='d-grid gap-2'>
                                <a href='backendphp.php' class='btn btn-outline-primary'>
                        <i class='bi bi-search me-2'></i>New Search
                    </a>
                                <a href='ae16a9801cdd61a0results.zip' class='btn btn-outline-success'>
                                    <i class='bi bi-download me-2'></i>Download Results
                                </a>
                            </div>";

maketables($conn);
$input = isset($_GET['search']) ? $_GET['search'] : 'all';
if (isset($_GET['search'])) {
    echo '<div class="visualization-container mt-5" style="overflow-x: auto;">';
    echo '<div class="table-container">';
    $input = $_GET['search'];
    
displayTable($conn,"ae16a9801cdd61a0",$input);
echo '</div></div></body></content>';
};

?>
