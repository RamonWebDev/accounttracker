<?php
$dbhost = "localhost";
$dbuser = "ramonm_acc";
$dbpassword = "KDxzVe^AInra";
$dbdatabase = "ramonm_account";
$config_basedir = "http://ramonmorales831.com/account/";//this line
$db = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbdatabase) or die("Error " . mysqli_error($db));
date_default_timezone_set('America/Chicago');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch names from the database
$sql = "SELECT username FROM username";
$result = $conn->query($sql);

// Loop through the results and create a card for each name
while ($row = $result->fetch_assoc()) {
    $name = $row['username'];
    echo '<div class="card">' . $name . '</div>';
}

// Close the database connection
$conn->close();


?>

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