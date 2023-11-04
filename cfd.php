<?
// Connect to the database
$servername = 'localhost';
$username = 'username';
$password = 'password';
$dbname = 'database';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}