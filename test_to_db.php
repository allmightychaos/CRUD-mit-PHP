<!--
    * Filename: ./CRUD-mit-PHP/testDB.php
    * Created Date: Monday, June 5th 2023, 10:02:53 pm
    * Author: Samuel Weghofer
    * 
    * Copyright (c) 2023 Samuel Weghofer
-->

<!-- 
Nur um zu Testen, ob man Anfragen an die Datenbank stellen kann.
-->

<?php
$servername = "localhost";
$username = "y23_2A_NACHNAME";
$password = "";
$database = "y23_2A_NACHNAME";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}

$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Tabellen in der Datenbank $database:<br>";
    while ($row = $result->fetch_assoc()) {
        echo $row['Tables_in_'.$database]."<br>";
    }
} else {
    echo "Keine Tabellen in der Datenbank $database gefunden.";
}

$conn->close();
?>