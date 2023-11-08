<?php
require("cfd.php");
require("header.php");
require("footer.php");

// Fetch usernames from the database
$sql = "SELECT username FROM username"; // SQL query to select usernames from the 'username' table
$result = $conn->query($sql); // Execute the SQL query and store the result

if ($result->num_rows > 0) { // Check if there are rows (usernames) in the result
    $usernames = []; // Initialize an array to store usernames

    while ($row = $result->fetch_assoc()) {
        $usernames[] = $row["username"]; // Store each username in the array
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
	


    function fetchProfile($account, $conn) {
        // Extract the numbers after "-" in the account name
		$accountName = $account;
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

        $statsData = fetchStats($accountName);
        // Merge profile and stats data into a single data array
        $mergedData = array_merge($profileData, $statsData);

        displayStats($mergedData, $accountNumber, $conn); // Pass the database connection to the displayStats function
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

			// Loop through the data and display it
		foreach ($topHeroes as $heroData) {
			echo "Hero: " . $heroData["hero"] . "<br>";
		}

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
				tankRank = '{$tankDivision}',
				dpsRank = '{$damageDivision}',
				supportRank = '{$supportDivision}',
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

// Define your data as an array
$data = [
    [
        "hero" => "Hanzo",
        "img" => "https://d15f34w2p8l1cc.cloudfront.net/overwatch/aecd8fa677f0093344fab7ccb7c37516c764df3f5ff339a5a845a030a27ba7e0.png",
        "played" => "01:46:25"
    ],
    [
        "hero" => "Pharah",
        "img" => "https://d15f34w2p8l1cc.cloudfront.net/overwatch/f8261595eca3e43e3b37cadb8161902cc416e38b7e0caa855f4555001156d814.png",
        "played" => "01:35:22"
    ],
    [
        "hero" => "Widowmaker",
        "img" => "https://d15f34w2p8l1cc.cloudfront.net/overwatch/a714f1cb33cc91c6b5b3e89ffe7e325b99e7c89cc8e8feced594f81305147efe.png",
        "played" => "01:22:47"
    ]
];



?>