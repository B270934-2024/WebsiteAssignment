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
  

function uploadtsv($filepath,$conn,$table_name,$columns,$user_id){
   $column_list = implode(", ", $columns);  // Convert array to a column string
   $temp_table = $table_name . "_temp";
   $conn->exec("CREATE TEMPORARY TABLE IF NOT EXISTS $temp_table LIKE $table_name;");   
   #$columns[] = "user_id"; 
   $column_list = implode(", ", $columns); 
   $query = "
      	LOAD DATA LOCAL INFILE :filepath
        INTO TABLE $temp_table
        FIELDS TERMINATED BY '\t'
        LINES TERMINATED BY '\n'
        IGNORE 1 LINES
        ($column_list)
    ";
    #$conn->exec("ALTER TABLE $temp_table ADD COLUMN user_id VARCHAR(255);");
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":filepath", $filepath, PDO::PARAM_STR);
    $stmt->execute();
    $conn->exec("UPDATE $temp_table SET user_id = '$user_id';");
    $column_list_with_user = implode(", ", array_merge($columns, ["user_id"]));
    $insert_query = "
            INSERT INTO $table_name ($column_list_with_user)
            SELECT $column_list_with_user FROM $temp_table
            WHERE NOT EXISTS (
            SELECT 1 FROM $table_name 
            WHERE $table_name.SeqName = $temp_table.SeqName 
            AND $table_name.user_id = '$user_id'
        );
        ";
	$conn->exec($insert_query);
        $conn->exec("DROP TEMPORARY TABLE IF EXISTS $temp_table;");
}

function uploadfasta($filepath,$conn,$user_id){
  $data = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  $seq_name = "";
  $sequence = "";
  #$conn->exec("ALTER TABLE fasta_table ADD COLUMN IF NOT EXISTS user_id VARCHAR(255);");
  $stmt = $conn->prepare("
        INSERT INTO fasta_table (SeqName, Sequence, user_id)
	Select ?, ?, ? from dual 
            WHERE NOT EXISTS (
            SELECT 1 FROM fasta_table WHERE SeqName = ? AND user_id = ?
        )
    ");
  foreach ($data as $line) {
        if (strpos($line, ">") === 0) { // Header line
            if ($seq_name) {
               $stmt->execute([$seq_name, $sequence,$user_id, $seq_name, $user_id]); // Save previous sequence
            }
            $seq_name = explode(" ",substr($line, 1))[0]; // Remove '>'
            $sequence = "";
        } else {
            $sequence .= trim($line);
        }
    }
     if ($seq_name) {
        $stmt->execute([$seq_name, $sequence,$user_id, $seq_name, $user_id]); // Save last sequence
    }
}

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
  //if the user does not have a current id (saved session)...
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
	_HEAD;
	if(isset($_POST['organism']) && isset($_POST['protein'])){
	$command = escapeshellcmd("EMAIL=" . escapeshellarg($email) . " python3 Backend.py " . escapeshellarg($user_id) . " " . escapeshellarg($_POST['organism']) . " " . escapeshellarg($_POST['protein']));
	$output = shell_exec($command);
	echo $output;
	echo "<div class='mainheader'>The search for {$_POST['protein']} in {$_POST['organism']} has concluded successfully!</div>";
	###echo "<img src='/public_html/plotcon.1.png' alt=\"A plotcon graph generated from your search\">";
	echo "<div class='plotimage'><img src=plotcon.1.png alt='A plotcon graph generated from your search'></div>";
	echo "<div class = 'downloader'>
	<a href='{$user_id}results.zip'>
	<button>Download Results</button>
	</a>
	</div>";
	echo "<p>Your id is: $user_id. This is displayed in front of your results.</p>";
	
	$file_pathpep = "{$user_id}pepresults.txt";
	$file_pathali = "{$user_id}alignment.fasta";
	$file_pathpro = "{$user_id}resultsprosite.tsv";
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
	</form>
	_FORM;
	if (file_exists("{$user_id}pepresults.txt")){
        echo "<a href='Results.php'>
        <button type='button'>Browse Stored Results</button>
        </a>";}


}
	echo <<<_TAIL
	</body>
	</html>
	_TAIL;
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

?>
