<?php
require("cfd.php");
require("header.php");
require("footer.php");

define('OWURL', 'https://owapi.io/stats/pc/us/'); //API URL

// Fetch usernames from the database
$sql = "SELECT username FROM username"; // SQL query to select usernames from the 'username' table
$result = $conn->query($sql); // Execute the SQL query and store the result

if ($result->num_rows > 0) { // Check if there are rows (usernames) in the result
    $usernames = []; // Initialize an array to store usernames

    while ($row = $result->fetch_assoc()) {
        $usernames[] = $row["username"]; // Store each username in the array
    }

	function fetchStats($account) {
		$ch = curl_init(OWURL.$account);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$statsResponse = curl_exec($ch);
		curl_close($ch);
		echo "Raw Response: " . $statsResponse . "\n";
		
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
	


	 function fetchProfile($account, $conn) {
        // Extract the numbers after "-" in the account name
		$accountName = $account;
        $accountParts = explode('-', $account);
        $accountNumber = end($accountParts);
		$ch = curl_init(OWURL . $account);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$profileResponse = curl_exec($ch);

		if ($profileResponse === false) {
			echo "cURL Error: " . curl_error($ch); // Display a detailed cURL error message
			curl_close($ch);
			return; // Exit the function
		}

		curl_close($ch);

		$profileData = json_decode($profileResponse, true);

		if ($profileData === null) {
			echo "Error decoding JSON\n"; // Display an error message if JSON decoding fails
			return; // Exit the function
		}

		$statsData = fetchStats($accountName);
		// Merge profile and stats data into a single data array
		$mergedData = array_merge($profileData, $statsData);

		displayStats($mergedData, $accountNumber, $conn);
    }

    function displayStats($data, $accountNumber, $conn) {
        // Extract basic user information
        $username = $data['username'] ?? '';
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

		// Check if the path to top played heroes data exists in the API response
		if (isset($data['stats']['top_heroes']['competitive']['played'])) {
			$playedHeroes = $data['stats']['top_heroes']['competitive']['played'];

			// Get the first 3 played heroes
			$topHeroes = array_slice($playedHeroes, 0, 3);
			    // Extract the heroes into separate variables
			$hero1 = isset($topHeroes[0]['hero']) ? $conn->real_escape_string($topHeroes[0]['hero']) : null;
			$hero2 = isset($topHeroes[1]['hero']) ? $conn->real_escape_string($topHeroes[1]['hero']) : null;
			$hero3 = isset($topHeroes[2]['hero']) ? $conn->real_escape_string($topHeroes[2]['hero']) : null;

		} else {
			// Handle the case when the data doesn't exist
			$topHeroes = []; // Set an empty array or default values
		}


        // Update the database with the values
        $account = $username . '-' . $accountNumber;
        $avatar = $conn->real_escape_string($avatar);
        $tankDivision = $conn->real_escape_string($tankDivision);
        $damageDivision = $conn->real_escape_string($damageDivision);
        $supportDivision = $conn->real_escape_string($supportDivision);

        $sql = "UPDATE username SET 
        battlenet = '{$account}',
        avatar = '{$avatar}',
        tankRank = CASE WHEN '{$tankDivision}' != 'Not Ranked' THEN '{$tankDivision}' ELSE tankRank END,
        dpsRank = CASE WHEN '{$damageDivision}' != 'Not Ranked' THEN '{$damageDivision}' ELSE dpsRank END,
        supportRank = CASE WHEN '{$supportDivision}' != 'Not Ranked' THEN '{$supportDivision}' ELSE supportRank END,
        hero1 = '{$hero1}', 
        hero2 = '{$hero2}',
        hero3 = '{$hero3}'
        WHERE username = '{$account}'";

        if ($conn->query($sql) === TRUE) {
            echo "Record updated successfully"; // Success message
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }

    // Fetch profile and stats for each username
    foreach ($usernames as $name) {
        fetchProfile($name, $conn); // Pass the database connection to the fetchProfile function
    }
} else {
    echo "No usernames found in the database.";
}



// Close the database connection
$conn->close();

echo '<h2>hello</h2>';

?>
