<?php
require("cfd.php");
require("header.php");
require("footer.php");

// Fetch usernames from the database
$sql = "SELECT * FROM username"; // SQL query to select usernames from the 'username' table
$result = $conn->query($sql); // Execute the SQL query and store the result

if ($result->num_rows > 0) {
    echo "<div class='holder'>"; // Open a holder div to group and display user cards

    while ($row = $result->fetch_assoc()) {
        $battlenet = $row["battlenet"];
        $avatar = $row["avatar"];
        $tankRank = $row["tankRank"];
        $dpsRank = $row["dpsRank"];
        $supportRank = $row["supportRank"];

        //Display card with info
        echo "<div class='card' style=\"background-image: url('img/{$ranHero}.jpg'); background-size: cover; background-position: center;\">";
        echo "<ul class='user-info'>";
        echo "<li><img src='$avatar' alt='User Avatar' class='avatar'></li>";
        echo "<br>";
        echo "<div class='border'>";
        echo "<li class='username battlenet'>$battlenet<i class='clipboardIcon bx bx-clipboard'></i></li>";
        echo "<li>Tank Rank: $tankRank</li>";
        echo "<li>DPS Rank: $dpsRank</li>";
        echo "<li>Support Rank: $supportRank</li>";
        // Display the top 3 played heroes
        //echo "<li>Top 3 Played Heroes:</li>";
        //echo "<ul>";
        //foreach ($topHeroes as $hero) {
        //    $heroName = $hero['hero'] ?? 'Unknown Hero';
        //    $heroImg = $hero['img'] ?? ''; // You can use this URL to display hero images
        //    echo "<li>$heroName</li>";
        //}
        //echo "</ul>";//end topHeroes
        echo "</div>";//end border
        echo "</ul>";//end user-info
        echo "</div>"; //end card
    }

    echo "</div>"; //end holder
} else {
    echo "No usernames found in the database.";
}
// Close the database connection
$conn->close();

?>

<button onclick='refresh()' id="executeButton">Execute PHP Script</button>

<script>
	document.getElementById("executeButton").addEventListener("click", function() {
		var xhr = new XMLHttpRequest();
		xhr.open("GET", "stats.php", true);
		xhr.onload = function() {
			if (xhr.status == 200) {
				 // Handle the response from the PHP script
			}
		};
			xhr.send();
	});
	
	function refresh(){
		window.location.href = 'index.php';
	}
</script>
