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

echo "<pre><font face ='arial'>
        <h1  class='d-flex'>Help and Context.</h1>
	<p class='d-flex'>This website is designed for bioinformatics analysis specifically for retrieving and analyzing protein sequence from a user-defined taxonomic group or species. It allows users to:
- Fetch protein sequences for an organism of interest. Subsequently, these are then analysed via several EMBOSS tools.
- These data are then stored, and once can revisit previous results associated with your unique ID, generated on your first page load. Yours is {$user_id}!
- The primary use of this site is to investigate proteins of a specific class within or between species. It can be used to generate your own database to BLAST against.</p>
	<h3 class='d-flex'>How to use this website.</h3>
<p class='d-flex' >- Enter the organism name and protein of interest in the search form.
- Click the Search button to fetch and analyze sequences.
- If no sequences are found, you will be returned to the search page, with a short error message
- Stored results can be accessed at any time via the Browse Stored Results button. These are automatically removed after 7 days.
- You can download the results as a .zip file using the Download Results button.
- You can search for specific sequences by entering their sequence names.
- To explore motifs, type MOTIF, sequence in the search box.</p>
<h3 class='d-flex'>What is this telling me?</h3>
<p class='d-flex'>-Are the proteins all of a similar size? Are their statistics similar? They are likely highly conserved!
-Do they share common motifs? They are also likely conserved!
-It might be worth using a BLAST on this too, to determine exactly how similar they are.
-Otherwise, inspect the 'PrettyPlot' and the 'Plotcon' plot to determine this graphically!</p>
</pre>
        <a href='Default.php?search=all' class = 'btn btn-secondary d-flex'>Browse Default Results</a>
        ";

$stmt = $conn->prepare("SELECT * FROM pep_table  WHERE user_id = ?");
$stmt->execute([$user_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result){
        echo "<a href='Results.php?search=all' class='btn btn-secondary d-flex' >Browse Stored Results</a>";
echo "<a href='backendphp.php' class='btn btn-secondary d-flex'>Back to Search</a>";
}
        echo <<<_TAIL
        </body>

        </html>
        _TAIL;
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

?>
