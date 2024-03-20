<?
// Connect to the database
$servername = 'localhost';
$username = 'ramonm_acc';
$password = 'KDxzVe^AInra';
$dbname = 'ramonm_account';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}