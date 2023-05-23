<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = "simple-website";
global $login;
$login = "admin"; 
$pass = "samplepass";
// tworzy nowe polaczenie z BD i wyswietla ew komunikaty o bledach
$conn = new mysqli($servername,$username,$password,$db) or die("Connect failed: %s\n" . $conn -> error); // utworzenie nowego polaczenia z BD
if ($conn -> connect_errno) { // jezeli polaczenie sie nie udalo
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error; // wyswietl komunikat o bledzie
  exit();
}
return $conn;
?>