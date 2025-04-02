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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://d3js.org/d3.v7.min.js"></script>
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
	echo "<div class='d-grid gap-2'>
                                <a href='backendphp.php' class='btn btn-outline-primary'>
                        <i class='bi bi-search me-2'></i>New Search
                    </a>
                                <a href='{$user_id}results.zip' class='btn btn-outline-success'>
                                    <i class='bi bi-download me-2'></i>Download Results
                                </a>
                            </div>";

echo <<<CLEAR_FORM
<style>
    .btn-clear-results {
        background: white;
        color: #dc3545;
        border: 2px solid #dc3545;
        transition: all 0.3s ease;
    }
    .btn-clear-results:hover {
        background: #dc3545;
        color: white;
        border-color: #dc3545;
    }
    .btn-clear-results:focus {
        box-shadow: 0 0 0 0.25rem rgba(220,53,69,.3);
    }
</style>

<div class="mt-4 text-center">
    <form method='POST' action='' onsubmit="return confirm('Warning: This will permanently delete all your results. Continue?')">
        <input type='hidden' name='clear_results' value='1'>
        <button type='submit' class='btn btn-clear-results btn-lg px-4 py-2'>
            <i class='bi bi-trash3 me-2'></i>Clear All Results
        </button>
        <p class='text-danger small mt-2 fw-semibold'>
            <i class='bi bi-exclamation-triangle-fill'></i> This action is permanent
        </p>
    </form>
</div>
CLEAR_FORM;

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
}
$isSingleSeq = false;
$searchParts = explode(',', $input);
if (count($searchParts) === 1 && $input !== 'all') {
    // Simple single sequence search
    $isSingleSeq = true;
} elseif (count($searchParts) === 2 && in_array(strtoupper(trim($searchParts[0])), ['MOTIF', 'ALIGNMENT'])) {
    // MOTIF/ALIGNMENT search for single sequence
    $isSingleSeq = true;
}
if ($isSingleSeq) {
    // Extract actual sequence name from input
    $seqName = $input;
    if (count($searchParts) === 2) {
        $seqName = trim($searchParts[1]);
    }
}
   // In your Results.php after checking $isSingleSeq
try {
    // Get ACTUAL sequence name from input
    $seqName = $input;
    if (count($searchParts) === 2) {
        $seqName = trim($searchParts[1]); // For MOTIF,SEQNAME format
    }

    // Get motifs for THIS SPECIFIC sequence
    $stmt = $conn->prepare("SELECT Start, End, Motif, Score, Strand 
                          FROM pro_table 
                          WHERE user_id = ? AND SeqName = ?");
    $stmt->execute([$user_id, $seqName]);
    $motifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get PROTEIN LENGTH from ts_table
    $stmt = $conn->prepare("SELECT Length FROM ts_table 
                          WHERE user_id = ? AND SeqName = ?");
    $stmt->execute([$user_id, $seqName]);
    $proteinLength = $stmt->fetchColumn() ?: 100; // Default to 100 if missing

    echo "<script>
            const domainData = {
                length: $proteinLength,
                motifs: " . json_encode($motifs) . "
            };
          </script>";

} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Error loading data: " 
        . $e->getMessage() . "</div>";
}
// Domain Visualization Section
echo <<<DOMAIN_VIS
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-columns"></i> Domain Architecture Visualization
    </div>
    <div id="domain-visualization"></div>
</div>
DOMAIN_VIS;

// Prepare motif data for D3.js
try {
    $stmt = $conn->prepare("SELECT Start, End, Motif, Score, Strand FROM pro_table WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $motifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $proteinLength = 0;
    foreach ($motifs as $motif) {
        if ($motif['End'] > $proteinLength) {
            $proteinLength = $motif['End'];
        }
    }
    
    echo "<script>
            const domainData = {
                length: $proteinLength,
                motifs: " . json_encode($motifs) . "
            };
          </script>";
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Error loading motif data: " . $e->getMessage() . "</div>";
}
echo <<<'D3_SCRIPT'
<script>
function renderDomains(data) {
    const width = 800;
    const height = 120;
    const margin = { top: 20, right: 20, bottom: 30, left: 40 };

    // Clear container
    const container = d3.select("#domain-visualization");
    container.html("");

    if (!data.motifs || data.motifs.length === 0) {
        container.append("div")
            .classed("alert alert-info", true)
            .text("Search for a single protein to display this information");
        return;
    }

    // Create SVG
    const svg = container.append("svg")
        .attr("width", width)
        .attr("height", height);

    // Set up correct scaling
    const xScale = d3.scaleLinear()
        .domain([0, data.length]) // Full protein length
        .range([margin.left, width - margin.right]);

    // Protein backbone
    svg.append("line")
        .attr("x1", margin.left)
        .attr("x2", width - margin.right)
        .attr("y1", height/2)
        .attr("y2", height/2)
        .attr("stroke", "#666")
        .attr("stroke-width", 2);

    // Add domains
    svg.selectAll(".domain-box")
        .data(data.motifs)
        .enter()
        .append("rect")
        .attr("class", "domain-box")
        .attr("x", d => xScale(d.Start))
        .attr("width", d => xScale(d.End - d.Start))
        .attr("y", height/2 - 15)
        .attr("height", 30)
        .attr("fill", "#4CAF50") // Solid green for visibility
        .attr("rx", 4)
        .on("mouseover", function(event, d) {
            d3.select(this).attr("stroke", "#000").attr("stroke-width", 2);
            showTooltip(d);
        })
        .on("mouseout", function() {
            d3.select(this).attr("stroke", null);
            hideTooltip();
        });

    // Add axis
    const xAxis = d3.axisBottom(xScale)
        .ticks(5)
        .tickFormat(d => `${d} aa`);

    svg.append("g")
        .attr("transform", `translate(0,${height - margin.bottom})`)
        .call(xAxis);

    // Tooltip functions
    function showTooltip(d) {
        d3.select("#tooltip")
            .style("opacity", 1)
            .html(`<strong>${d.Motif}</strong><br>
                   Position: ${d.Start}-${d.End}<br>
                   Score: ${d.Score}<br>
                   Strand: ${d.Strand}`)
            .style("left", (event.pageX + 10) + "px")
            .style("top", (event.pageY - 28) + "px");
    }

    function hideTooltip() {
        d3.select("#tooltip").style("opacity", 0);
    }
}

// Initialize visualization
document.addEventListener('DOMContentLoaded', () => {
    if(typeof domainData !== 'undefined') {
        renderDomains(domainData);
    }
});
</script>
D3_SCRIPT;

echo "</div></body></content>";
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
?>