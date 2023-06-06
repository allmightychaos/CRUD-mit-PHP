<!-- 
    * Filename: ./CRUD-mit-PHP/music.php
    * Created Date: Monday, June 5th 2023, 10:37:33 pm
    * Author: Samuel Weghofer
    * 
    * Copyright (c) 2023 Samuel Weghofer 
-->

<?php
// Verbindung zur Datenbank herstellen
$servername = "localhost";
$username = "y23_2A_NACHNAME";
$password = "";
$database = "y23_2A_NACHNAME";

$conn = new mysqli($servername, $username, $password, $database);

// Überprüfen, ob die Verbindung erfolgreich hergestellt wurde
if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}

/* --------------------------------------------- Abschnitt 1: Einträge in DB einfügen -----------------------------------------------*/

if (isset($_POST['submit']) && $_POST['submit'] === "Neu erstellen") {
    // Daten aus dem Formular abrufen
    $artist = $_POST['artist'];
    $album = $_POST['album'];
    $published = intval($_POST['published']); // Nur das Jahr als Ganzzahl speichern
    $format = $_POST['format'];

    // Neue Daten in die Datenbank einfügen - SQL Injection vermeiden mit `?`
    $stmt = $conn->prepare("INSERT INTO music_collection (artist, album, published, format) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $artist, $album, $published, $format);
    $stmt->execute();
    $stmt->close();

    // Weiterleitung zur aktuellen Seite, um einen Neuladen ohne Wiederholung der Aktion zu veranlassen
    header("Location: music.php");
    exit();
}

/* --------------------------------------------- Abschnitt 2: Einträge aus DB löschen -----------------------------------------------*/

if (isset($_POST['delete'])) {
    $id = $_POST['delete'];

    // Eintrag aus der Datenbank löschen
    $stmt = $conn->prepare("DELETE FROM music_collection WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Weiterleitung zur aktuellen Seite, um einen Neuladen ohne Wiederholung der Aktion zu veranlassen
    header("Location: music.php");
    exit();
}

/* ---------------------------------------- Abschnitt 3: Funktion zur Ausgabe in die Tabelle ----------------------------------------*/

function displayEntries($conn)
{
    $sql = "SELECT * FROM music_collection";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $artist = $row['artist'];
            $album = $row['album'];
            $published = $row['published'];
            $format = $row['format'];

            // Format umwandeln
            $displayFormat = '';
            switch ($format) {
                case 'v':
                    $displayFormat = 'Vinyl';
                    break;
                case 'k':
                    $displayFormat = 'Kassette';
                    break;
                case 'c':
                    $displayFormat = 'CD';
                    break;
                case 'd':
                    $displayFormat = 'Digital';
                    break;
                default:
                    $displayFormat = $format;
                    break;
            }

            echo "<tr><td>$artist</td><td>$album</td><td>$published</td><td>$displayFormat</td><td><form method='POST'><button id='deleteButton' type='submit' name='delete' value='$id'>Löschen</button></form></td></tr>";
        }
    } else {
        echo "<tr><td style='text-align: center; padding-top: 26px;' colspan='5'>Keine Einträge vorhanden / gefunden.</td></tr>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD-mit-PHP</title>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
            padding: 0;
            margin: 0;
        }

        .container {
            position: absolute;
            left: 50%;
            top: 10%;
            transform: translateX(-50%);
            border: 1px solid black;
            width: 600px;
            height: 450px;
            padding: 36px;
        }

        .ctr {
            display: flex;
        }

        .ctr > label {
            width: 130px;
            text-align: right;
            padding: 5px 15px 5px 0;
        }

        .ctr:not(:first-child) {
            margin-top: 10px;
        }

        .ctr > input,
        ctr > select {
            width: 335px;
            height: 16px;
            text-align: center;
            padding: 5px;
            color: black;
            border: 1px solid black;
        }

        .ctr > input::placeholder {
            color: black;
        }

        .ctr-published > input {
            width: 80px;
            height: 16px;
            padding: 5px 15px;
        }

        .ctr-format > select {
            width: 112px;
            height: 28px;
        }

        input[type="submit"] {
            width: 160px;
            height: 28px;
            margin: 15px 0 0 145px;
            background-color: white;
            border: 1px solid black;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border: 1px solid black;
            padding: 17px;
            margin-top: 36px;
        }

        tr {
            text-align: left;
        }

        tr:first-child > th {
            width: 466px;
            max-width: 466px;
        }

        #deleteButton {
            width: 100%;
            height: 100%;
            padding: 5px;
            background-color: white;
            border: 1px solid black;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="formDiv">
            <form action="music.php" method="POST">
                <div class="createNew">
                    <div class="ctr ctr-artist">
                        <label for="artist">Künstler:</label>
                        <input type="text" id="artist" name="artist" placeholder="Textbox">
                    </div>

                    <div class="ctr ctr-album">
                        <label for="album">Album:</label>
                        <input type="text" id="album" name="album" placeholder="Textbox">
                    </div>

                    <div class="ctr ctr-published">
                        <label for="published">Veröffentlicht am: </label>
                        <input type="text" id="published" name="published" placeholder="Datum">
                    </div>

                    <div class="ctr ctr-format">
                        <label for="format">Format</label>
                        <select name="format" id="format">
                            <option disabled selected>Selectbox</option>
                            <option value="c">CD</option>
                            <option value="k">Kassette</option>
                            <option value="v">Vinyl</option>
                            <option value="d">Digital</option>
                        </select>
                    </div>

                    <input type="submit" name="submit" value="Neu erstellen">
                </div>
                
                <table>
                    <tr>
                        <th>Künstler</th>
                        <th>Album</th>
                        <th>Erscheinungsjahr</th>
                        <th>Format</th>
                        <th></th>
                    </tr>
                    
                    <?php
                    displayEntries($conn);
                    ?>

                </table>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
