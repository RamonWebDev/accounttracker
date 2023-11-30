<?php
require("cfd.php");
require("header.php");
require("footer.php");

define('OWURL', 'https://overfast-api.tekrop.fr/players/'); //API URL

// Fetch usernames from the database
$sql = "SELECT username FROM username"; // SQL query to select usernames from the 'username' table
$result = $conn->query($sql); // Execute the SQL query and store the result

if ($result->num_rows > 0) { // Check if there are rows (usernames) in the result
    $usernames = []; // Initialize an array to store usernames

    while ($row = $result->fetch_assoc()) {
        $usernames[] = $row["username"]; // Store each username in the array
    }

	function fetchStats($account, $conn) {
		// Extract the numbers after "-" in the account name
		$accountName = $account;
		$accountParts = explode('-', $account);
		$accountNumber = end($accountParts);

		$ch = curl_init(OWURL . $account);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$statsResponse = curl_exec($ch);

		// Check for cURL errors
		if ($statsResponse === false) {
			echo "cURL Error: " . curl_error($ch) . "\n";
			return []; // Return an empty array as there are no stats to display
		}

		// Attempt to decode the JSON response containing user stats
		$statsData = json_decode($statsResponse, true);

		// Check for JSON decoding errors
		if ($statsData === null && json_last_error() !== JSON_ERROR_NONE) {
			echo "Error decoding JSON: " . json_last_error_msg() . "\n";
			// Output the raw JSON response for investigation
			echo "Raw JSON Response: " . $statsResponse . "\n";
			return []; // Return an empty array in case of an error
		}
		
		curl_close($ch);
		displayStats($statsData, $accountNumber, $conn);
	}
	

    function displayStats($data, $accountNumber, $conn) {
        // Extract basic user information
		$username = $data['summary']['username'];
		$avatar = $data['summary']['avatar'];
		$level = $data['summary']['endorsement']['frame'];
        // Initialize variables for competitive division rankings
		$tankTier = $damageTier = $supportTier = "";
		$dpsRank = $tankRank = $supportRank = "Not Ranked";

		// Assign values if the data is available
		if (isset($data['summary']['competitive']['pc']['tank'])) {
			$tankDivision = $data['summary']['competitive']['pc']['tank']['division'];
			$tankTier = $data['summary']['competitive']['pc']['tank']['tier'];
			$tankRank = $tankDivision. ' '.  $tankTier;
		}
			// Assign values if the data is available
		if (isset($data['summary']['competitive']['pc']['damage'])) {
			$damageDivision = $data['summary']['competitive']['pc']['damage']['division'];
			$damageTier = $data['summary']['competitive']['pc']['damage']['tier'];
			$dpsRank = $damageDivision. ' '.  $damageTier;
		}
			// Assign values if the data is available
		if (isset($data['summary']['competitive']['pc']['support'])) {
			$supportDivision = $data['summary']['competitive']['pc']['support']['division'];
			$supportTier = $data['summary']['competitive']['pc']['support']['tier'];
			$supportRank = $supportDivision. ' '.  $supportTier;
		}

        // Extract the top 3 played heroes from the stats data
        $topHeroes = [];

	// Check if the path to top played heroes data exists in the API response
    if (isset($data['stats']['pc']['competitive']['heroes_comparisons']['time_played']['values'])) {
        $playedHeroes = $data['stats']['pc']['competitive']['heroes_comparisons']['time_played']['values'];

        // Iterate through the first 3 elements of the values array
        for ($i = 0; $i < min(3, count($playedHeroes)); $i++) {
            // Check if the 'hero' key exists in each value
            if (isset($playedHeroes[$i]['hero'])) {
                // Access and print the 'hero' value
                $topHeroes[] = $conn->real_escape_string($playedHeroes[$i]['hero']);
            }
        }
    } else {
        // Handle the case when the data doesn't exist
        $topHeroes = []; // Set an empty array or default values
    }
	    // Initialize hero variables
		$hero1 = $hero2 = $hero3 = '';
		// If you want to assign them to separate variables, you can do something like this:
		if (!empty($topHeroes)) {
			list($hero1, $hero2, $hero3) = $topHeroes;
		}


        // Update the database with the values
        $account = $username . '-' . $accountNumber;
        $avatar = $conn->real_escape_string($avatar);
        $tankRank = $conn->real_escape_string($tankRank);
        $dpsRank = $conn->real_escape_string($dpsRank);
        $supportRank = $conn->real_escape_string($supportRank);
		$hero1 = $conn->real_escape_string($hero1);
		$hero2 = $conn->real_escape_string($hero2);
		$hero3 = $conn->real_escape_string($hero3);

		$sql = "UPDATE username SET 
        battlenet = '{$account}',
        avatar = '{$avatar}',
        tankRank = CASE WHEN '{$tankRank}' != 'Not Ranked' THEN '{$tankRank}' ELSE tankRank END,
        dpsRank = CASE WHEN '{$dpsRank}' != 'Not Ranked' THEN '{$dpsRank}' ELSE dpsRank END,
        supportRank = CASE WHEN '{$supportRank}' != 'Not Ranked' THEN '{$supportRank}' ELSE supportRank END,
        hero1 = COALESCE(NULLIF('{$hero1}', ''), hero1), 
        hero2 = COALESCE(NULLIF('{$hero2}', ''), hero2),
        hero3 = COALESCE(NULLIF('{$hero3}', ''), hero3)
        WHERE username = '{$account}'";

        if ($conn->query($sql) === TRUE) {
            echo "Record updated successfully"; // Success message
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }

    // Fetch profile and stats for each username
    foreach ($usernames as $account) {
        fetchStats($account, $conn); // Pass the database connection to the fetchProfile function
    }
} else {
    echo "No usernames found in the database.";
}



// Close the database connection
$conn->close();


?>
