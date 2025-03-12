<?php
$servername = "127.0.0.1"; //localhost didn't work
$username = "s2761220";
$password = "!AEZZ)C1aezz0c";
$email="s2761220@ed.ac.uk";
function maketables($conn){
  $tables = [
        "CREATE TABLE IF NOT EXISTS pep_table (
            SeqName VARCHAR(255),
            MolecularWeight FLOAT NOT NULL,
            ResidueCount INT NOT NULL,
            ResidueWeight FLOAT,
            Charge FLOAT,
            IsoelectricPoint FLOAT,
            ExtinctionReduced INT,
            ExtinctionBridges INT,
            ReducedMgMl INT,
            BridgeMgMl INT,
            Probability_pos_neg FLOAT,
            user_id VARCHAR(255)
            )",

        "CREATE TABLE IF NOT EXISTS fasta_table (
            SeqName VARCHAR(255),
            Sequence TEXT NOT NULL,
            user_id VARCHAR(255)
        )",

        "CREATE TABLE IF NOT EXISTS pro_table (
            SeqName VARCHAR(255),
            Start INT NOT NULL,
            End INT NOT NULL,
	    Score INT NOT NULL, 
	    Strand VARCHAR(50),
            Motif TEXT,
            user_id VARCHAR(255)
        )"];
  foreach ($tables as $sql) {
          $conn->exec($sql);
      }
  }

function displayTable($conn,$selected_id=null) {
	maketables($conn);
	if($selected_id=='all'){
        $stmt = $conn->prepare("
    SELECT *
    FROM pep_table p
    LEFT JOIN pro_table pr ON p.SeqName = pr.SeqName
    ");
        $stmt->execute();
     }else{
	     if (!is_array($selected_id)) {
		     $selected_id = explode(',', $selected_id);
		     #$selected_id = [$selected_id];  // Convert single value to an array
		     $selected_id = array_map('trim', $selected_id);
	     }
	     if (empty($selected_id)) {
    echo "<p>No IDs selected!</p>";
    return;
	     }
    $IDlist = implode(',', array_fill(0, count($selected_id), '?'));
	 $stmt = $conn->prepare("
	    SELECT *
	    FROM pep_table p
	    LEFT JOIN pro_table pr ON p.SeqName = pr.SeqName
	    WHERE p.SeqName IN ($IDlist)");
	$stmt->execute($selected_id);
     }
     $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
     if(!$results)
     {echo "<p>No results!</p>";return;}
     echo "<table class='table'>";
    echo "<tr class='tr'>";
    // Table Headers (Generate dynamically)
    foreach (array_keys($results[0]) as $column) {
        echo "<th class='th'>" . htmlspecialchars($column) . "</th>";
    }
    echo "</tr>";
    foreach ($results as $row) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>" . $cell . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}
echo <<<_HEAD
        <html>
        <head>
        <title>Simple Protein Results</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style/style.css">

        </head>
        <body>
        _HEAD;

$conn = new PDO("mysql:host=$servername;dbname=s2761220_website", $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    PDO::MYSQL_ATTR_LOCAL_INFILE => true
  ]);

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
    echo "User ID from cookie: " . htmlspecialchars($user_id);
} else {
    echo "No user ID found in cookies.";
}
echo "<form method='GET' action=''>
    	<label for='search'>Search by Sequence Name: Enter comma separated list of SeqNames or 'all'.</label>
    	<input type='text' id='search' name='search' placeholder='Enter SeqName or all...' required>
    	<button type='submit'>Search</button>
	</form>";
maketables($conn);
$input = isset($_GET['search']) ? $_GET['search'] : 'all';
if (isset($_GET['search'])) {
    $input = $_GET['search'];

    if ($input === "DELETE") {
        $stmt = $conn->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $conn->exec("DROP TABLE IF EXISTS `$table`");
        }
        exit; // Stop execution after deleting tables
    } elseif ($input === "") {
        exit; // Stop execution if search is empty
    }

    displayTable($conn, $input);
}
echo "<a href='backendphp.php'>
<button> back </button>
</a>";
echo "<div class = 'downloader'>
        <a href='{$user_id}results.zip'>
        <button>Download Results</button>
        </a>
	</div>";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["clear_results"])) {
    clearUserResults($conn, $user_id);
    echo "<p>Your results have been cleared.</p>";
}
echo"<form method='POST' action=''>
    <input type='hidden' name='clear_results' value='1'>
    <button type='submit' class='btn btn-danger'>Clear My Results</button>
</form>";
function clearUserResults($conn, $user_id) {
    $tables = ["pep_table", "pro_table", "fasta_table"]; // Add all tables that store user results
    foreach ($tables as $table) {
        $stmt = $conn->prepare("DELETE FROM $table WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }
}
echo "</body>";
?>
