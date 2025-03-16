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
        <h1  class='d-flex'>Credits and Contacts.</h1>
	<p class='d-flex'>Thank you for using the Simple Protein Searcher. The code used to create this site is available <a href='https://github.com/B270934-2024/WebsiteAssignment'>here</a>.
<p class='d-flex'>Thank you all to the good people on StackOverflow for providing help with the various coding issues I faced, and ELM for aiding the debugging process.</p> 
<p class='d-flex'>Also thank you to Al Iverns and all of my coursemates for the help and motivation!</p>
<p class='d-flex'>Finally, thanks to everyone who created the EMBOSS tools that the site makes very liberal use of, and to NCBI for providing an excellent database to query!</p>
<p class='d-flex'>If you have any questions or suggestsions, I would love to hear them. You can contact me <a href='mailto:s2761220@ed.ac.uk'>here</a>!</p>
</pre>";
	echo "<a href='Default.php?search=all' class = 'btn btn-secondary d-flex'>Browse Default Results</a>";
$stmt = $conn->prepare("SELECT * FROM pep_table  WHERE user_id = ?");
$stmt->execute([$user_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result){
        echo "<a href='Results.php?search=all' class='btn btn-secondary d-flex' >Browse Stored Results</a>";}

echo "<a href='HelpAndContext.php' class = 'btn btn-secondary d-flex'>Help and Context</a>";
echo "<a href='backendphp.php' class='btn btn-secondary d-flex'>Back to Search</a>";

        echo <<<_TAIL
        </body>

        </html>
        _TAIL;
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

?>
