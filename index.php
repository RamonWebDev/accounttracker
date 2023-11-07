<?php
require("cfd.php");
require("header.php");
require("footer.php");

// Fetch usernames from the database
$sql = "SELECT username FROM username"; // SQL query to select usernames from the 'username' table
$result = $conn->query($sql); // Execute the SQL query and store the result

if ($result->num_rows > 0) { // Check if there are rows (usernames) in the result
    $usernames = []; // Initialize an array to store usernames
    echo "<div class='holder'>"; // Open a holder div to group and display user cards

    while ($row = $result->fetch_assoc()) {
        $usernames[] = $row["username"]; // Store each username in the array
    }
	
	// Define named functions
	function fetchProfile($account) {
		    // Extract the numbers after "-" in the account name
		$accountParts = explode('-', $account);
		$accountNumber = end($accountParts);
		$profileUrl = "https://owapi.io/profile/pc/us/{$account}";
		$profileResponse = file_get_contents($profileUrl); // Fetch the profile data from the specified URL

		if ($profileResponse === false) {
			echo "No Account Found For {$account}\n"; // Display an error message if the profile data is not found
			return; // Exit the function
		}
		
		$profileData = json_decode($profileResponse, true); // Parse the profile data as JSON
		if ($profileData === null) {
			echo "Error decoding JSON\n"; // Display an error message if JSON decoding fails
			return; // Exit the function
		}
		
		$statsData = fetchStats($accountNumber); // Use the extracted account number
		$statsData = fetchStats($account); // Call a separate function to fetch additional stats data
		// Merge profile and stats data into a single data array
		$mergedData = array_merge($profileData, $statsData);

		displayStats($mergedData, $accountNumber); // Display the merged data using a separate function

	}

	function fetchStats($account) {
		$statsUrl = "https://owapi.io/stats/pc/us/{$account}";
		$statsResponse = file_get_contents($statsUrl);
		
		 // Check if the response is false, indicating no stats were found for the user
		if ($statsResponse === false) {
			echo "No Stats Found For {$account}\n";
			return [];// Return an empty array as there are no stats to display
		}

		// Attempt to decode the JSON response containing user stats
		$statsData = json_decode($statsResponse, true); 
		if ($statsData === null) {
			echo "Error decoding JSON\n";// Display an error message if JSON decoding fails
			return [];// Return an empty array in case of an error
		}

		return $statsData; //returns data
	}

	function displayStats($data, $accountNumber) {
		 // Extract basic user information
		$username = $data['username'] ?? ''; //if NULL changes to ''
		$avatar = $data['portrait'] ?? '';
		$level = $data['endorsement'] ?? '';
		// Initialize variables for competitive division rankings
		$tankDivision = $damageDivision = $supportDivision = "Not Ranked";
		$tophero = $data['competitive'] ?? '';

		// Check and set competitive division rankings if available
		if (isset($data['competitive']['tank'])) {
			$tankDivision = $data['competitive']['tank']['rank'];
		}

		if (isset($data['competitive']['offense'])) {
			$damageDivision = $data['competitive']['offense']['rank'];
		}

		if (isset($data['competitive']['support'])) {
			$supportDivision = $data['competitive']['support']['rank'];
		}
		
		// Extract the top 3 played heroes from the stats data
		$topHeroes = [];
		$ranHero = null; // Initialize $ranHero

		if (isset($data['stats']['top_heroes']['competitive']['played'])) {
			$playedHeroes = $data['stats']['top_heroes']['competitive']['played'];

			// Get the first 3 played heroes
			$topHeroes = array_slice($playedHeroes, 0, 3);

			// Check if there is at least one hero in the topHeroes array
			if (!empty($topHeroes)) {
				// Use array_rand to get a random index from the topHeroes array
				$randomIndex = array_rand($topHeroes);
				// Extract the hero name from the randomly selected index
				$ranHero = $topHeroes[$randomIndex]['hero'];

				// Remove spaces from the hero name if it's "Soldier 76" or "Wrecking Ball"
				$ranHero = str_replace([' ', ':'], '', $ranHero);
			}
		}

		// Now, $ranHero contains the name of a random hero from the top 3 played heroes.


		//Display card with info
		echo "<div class='card' style=\"background-image: url('img/{$ranHero}.jpg'); background-size: cover; background-position: center;\">";
		echo "<ul class='user-info'>";
		echo "<li><img src='$avatar' alt='User Avatar' class='avatar'></li>";
		echo "<br>";
		echo "<div class='border'>";
		echo "<li class='username battlenet'>$username#$accountNumber<i class='clipboardIcon bx bx-clipboard'></i><img src='$level' alt='User Level' class='level'></li>";
		echo "<li>Tank Rank: {$tankDivision}</li>";
		echo "<li>DPS Rank: {$damageDivision}</li>";
		echo "<li>Support Rank: {$supportDivision}</li>";
		    // Display the top 3 played heroes
		echo "<li>Top 3 Played Heroes:</li>";
		echo "<ul>";
		foreach ($topHeroes as $hero) {
			$heroName = $hero['hero'] ?? 'Unknown Hero';
			$heroImg = $hero['img'] ?? ''; // You can use this URL to display hero images
			echo "<li>$heroName</li>";
		}
		echo "</ul>";//end topHeroes
		echo "</div>";//end border
		echo "</ul>";//end user-info
		echo "</div>"; //end card
		
	}

	// Fetch profile and stats for each username
	foreach ($usernames as $name) {
		fetchProfile($name);
	}
	echo "</div>"; //end holder 

	} else {
		echo "No usernames found in the database.";
	}

// Close the database connection
$conn->close();

?>