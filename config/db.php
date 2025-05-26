<?php
$server = "localhost";
$username = "root";
$password = "";
$database = "agendahukum";

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
    echo ("Tidak dapat terhubung: " . mysqli_connect_error());
}
?>