<?php
<<<<<<< HEAD
require("cfd.php");
require("header.php");
require("footer.php");
=======
$dbhost = "localhost";
$dbuser = "user";
$dbpassword = "password";
$dbdatabase = "databse";
$config_basedir = "http://ramonmorales831.com/account/";//this line
$db = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbdatabase) or die("Error " . mysqli_error($db));
date_default_timezone_set('America/Chicago');
>>>>>>> f29e545e2efef8ef1b315a7fdc9d75ac08508f25

// Fetch usernames from the database
$sql = "SELECT username FROM username"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $usernames = [];
	echo "<div class='holder'>";
    while ($row = $result->fetch_assoc()) {
        $usernames[] = $row["username"];
    }

    // Define named functions
    function fetchStats($account) {
        $url = "https://owapi.io/profile/pc/us/{$account}";
        $response = file_get_contents($url);

        if ($response === false) {
            echo "No Account Found For {$account}\n";
            return;
        }

        $data = json_decode($response, true);
        if ($data === null) {
            echo "Error decoding JSON\n";
            return;
        }

        displayStats($data);
    }

    function displayStats($data) {
        $username = $data['username'] ?? '';
		$avatar = $data['portrait'] ?? '';
		$level = $data['endorsement']??'';
        $tankDivision = $damageDivision = $supportDivision = '';

        if (isset($data['competitive']['tank'])) {
            $tankDivision = $data['competitive']['tank']['rank'];
        }

        if (isset($data['competitive']['offense'])) {
            $damageDivision = $data['competitive']['offense']['rank'];
        }

        if (isset($data['competitive']['support'])) {
            $supportDivision = $data['competitive']['support']['rank'];
        }
		
		echo "<div class='card'>";
		echo "<ul class='user-info'>";
		echo "<li><img src='$avatar' alt='User Avatar' class='avatar'></li>";
		echo "<li><img width='50' height='50' src='$level' alt='User Level' class='level'></li>";
		echo "<li class='username'>$username</li>";
		echo "<li>Tank Rank: {$tankDivision}</li>";
		echo "<li>DPS Rank: {$damageDivision}</li>";
		echo "<li>Support Rank: {$supportDivision}</li>";
		echo "</ul>";
		echo "</div>";

		
    }

    // Fetch stats for each username
    foreach ($usernames as $name) {
        fetchStats($name);
    }
	echo "</div>";
	

} else {
    echo "No usernames found in the database.";
}

// Close the database connection
$conn->close();

?>

<<<<<<< HEAD
=======
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
  <title>Account Tracker</title>
</head>
<body>
  
</body>
</html>
>>>>>>> f29e545e2efef8ef1b315a7fdc9d75ac08508f25
