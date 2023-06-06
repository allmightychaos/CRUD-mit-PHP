<!--
    * Filename: ./www/music.php
    * Created Date: Monday, June 5th 2023, 6:37:12 pm
    * Author: Samuel Weghofer
    * 
    * Copyright (c) 2023 Samuel Weghofer
-->

<?php
// Verbindung zur Datenbank herstellen
$servername = "localhost";
$username = "y23_2A_Weghofer";
$password = "P&bS2ynaJyKD^Vj";
$database = "y23_2A_Weghofer";

$conn = new mysqli($servername, $username, $password, $database);
// Überprüfen, ob die Verbindung erfolgreich hergestellt wurde
if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}

/* --------------------------------------------- Abschnitt 1: Einträge in DB einfügen -----------------------------------------------*/

if (isset($_POST['create']) && $_POST['create'] === "Neu erstellen") {
    // Daten aus dem Formular abrufen
    $artist = $_POST['artist'];
    $album = $_POST['album'];
    $published = $_POST['published'];
    $format = $_POST['format'];
    
    // Neue Daten in die Datenbank einfügen
    $stmt = $conn->prepare("INSERT INTO music_collection (artist, album, published, format) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $artist, $album, $published, $format);
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

            // Datum in Jahr umwandeln
            $year = date('Y', strtotime($published));

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

            echo "<tr>
                    <td>$artist</td>
                    <td>$album</td>
                    <td class='center'>$year<span class='invisibleSpan'>$published</span></td>
                    <td class='center'>$displayFormat</td>
                    <td>
                        <form method='POST'>
                            <button class='deleteButton' type='submit' name='delete' value='$id'>Löschen</button>
                        </form>
                    </td>
                    <td class='TDsvg'>
                        <svg class='editSVG' width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                            <path d='M3.5 20.4999C4.33 21.3299 5.67 21.3299 6.5 20.4999L19.5 7.49994C20.33 6.66994 20.33 5.32994 19.5 4.49994C18.67 3.66994 17.33 3.66994 16.5 4.49994L3.5 17.4999C2.67 18.3299 2.67 19.6699 3.5 20.4999Z' stroke='#FFFFFF' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/>
                            <path d='M18.01 8.98999L15.01 5.98999' stroke='#FFFFFF' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/>
                            <path d='M8.5 2.44L10 2L9.56 3.5L10 5L8.5 4.56L7 5L7.44 3.5L7 2L8.5 2.44Z' stroke='#FFFFFF' stroke-linecap='round' stroke-linejoin='round'/>
                            <path d='M4.5 8.44L6 8L5.56 9.5L6 11L4.5 10.56L3 11L3.44 9.5L3 8L4.5 8.44Z' stroke='#FFFFFF' stroke-linecap='round' stroke-linejoin='round'/>
                            <path d='M19.5 13.44L21 13L20.56 14.5L21 16L19.5 15.56L18 16L18.44 14.5L18 13L19.5 13.44Z' stroke='#FFFFFF' stroke-linecap='round' stroke-linejoin='round'/>
                        </svg>
                    </td>
                    <td class='hidden'>$id</td>
                </tr>";
        
        }
    } else {
        echo "<tr>
                <td style='text-align: center; padding-top: 26px;' colspan='5'>Keine Einträge vorhanden / gefunden.</td>
            </tr>";
    }
}
/* --------------------------------------------- Abschnitt 4: Bearbeiten & Speichern -----------------------------------------------*/
if (isset($_POST['save']) && $_POST['save'] === "Speichern") {
    // Daten aus dem Formular abrufen
    $artist = $_POST['artist'];
    $album = $_POST['album'];
    $published = $_POST['published'];
    $format = $_POST['format'];

    
    // ID des aktuellen Eintrags abrufen
    $entryId = $_POST['entry_id'];
    
    // Aktualisierte Daten in die Datenbank einfügen
    $stmt = $conn->prepare("UPDATE music_collection SET artist = ?, album = ?, published = ?, format = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $artist, $album, $published, $format, $entryId);
    $stmt->execute();
    $stmt->close();

    // Debugging: Output success message
    echo "Data updated successfully!\n";

    // Weiterleitung zur aktuellen Seite
    header("Location: music.php");
    exit();
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
            width: 112px;
            height: 16px;
            padding: 5px 15px;
        }

        .ctr-format > select {
            width: 144px;
            height: 28px;

            text-align: center;
        }

        input[type="submit"] {
            width: 160px;
            height: 28px;
            margin: 15px 0 0 145px;
            background-color: white;
            border: 1px solid black;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            cursor: pointer;
        }

        table {
            width: 100%;
            border: 1px solid black;
            padding: 13px;
            margin-top: 36px;
        }

        tr {
            text-align: left;
        }

        tr:first-child > th {
            width: 466px;
            max-width: 466px;   
        }

        tr:first-child > th:not(:first-child, :nth-child(2)) {
            text-align: center;
        }

        .editSVG {
            width: 23.5px;
            height: 23.5px;
            padding: 1px;
            
            border: 1px solid black;

            display: block;
            margin-left: auto;
            margin-right: auto;

            border-radius: 2px 5px 5px 2px;
        }

        .editSVG path {
            stroke: black;
        }

        .editSVG:hover {
            cursor: pointer;
        }

        .TDsvg {
            padding-left: 5px;
        }

        .deleteButton {
            width: 100%;
            height: 100%;
            padding: 5px;
            background-color: white;
            border: 1px solid black;
            border-radius: 5px 2px 2px 5px;
        }

        .deleteButton:hover {
            cursor: pointer;
        }

        .center {
            text-align: center;
        }

        .invisibleSpan {
            display: none;
        }

        .buttons {
            display: flex;
        }

        .buttons input:nth-child(2){
            margin-left: 25px;
        }

        .hidden {
            display: none;
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
                        <input type="date" id="published" name="published" placeholder="Datum">
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

                    <div class="buttons">
                        <input type="submit" name="create" value="Neu erstellen">
                        <input type="submit" name="save" id="saveButton" value="Speichern" class="hidden">
                    </div>
                    
                    <input type="hidden" name="entry_id" id="entryId" value="">
                </div>
                
                <table>
                    <tr>
                        <th>Künstler</th>
                        <th>Album</th>
                        <th>Erscheinungsjahr</th>
                        <th>Format</th>
                        <th></th>
                    </tr>
                    <tr>
                    </tr>
                    
                    <?php
                    displayEntries($conn);
                    ?>

                </table>
            </form>
        </div>
    </div>

    <script>
        const editSVGs = document.getElementsByClassName('editSVG');

        Array.from(editSVGs).forEach((editSVG, index) => {
            editSVG.addEventListener('click', () => {
                const tableRow = editSVG.closest('tr');

                const artist = tableRow.querySelector('td:nth-child(1)').textContent;
                const album = tableRow.querySelector('td:nth-child(2)').textContent;
                const year = tableRow.querySelector('td:nth-child(3)').textContent.substring(4);
                const displayFormat = tableRow.querySelector('td:nth-child(4)').textContent;
                const entryId = tableRow.querySelector('td:nth-child(7)').textContent;

                const artistField = document.getElementById('artist');
                const albumField = document.getElementById('album');
                const yearField = document.getElementById('published');
                const formatField = document.getElementById('format');
                const saveButton = document.getElementById('saveButton');
                const entryIdField = document.getElementById('entryId');

                artistField.value = artist;
                albumField.value = album;
                yearField.value = year;

                if (displayFormat === 'Vinyl') {
                    formatField.value = 'v';
                } else if (displayFormat === 'Kassette') {
                    formatField.value = 'k';
                } else if (displayFormat === 'Digital') {
                    formatField.value = 'd';
                } else if (displayFormat === 'CD') {
                    formatField.value = 'c';
                }

                entryIdField.value = entryId; 

                saveButton.classList.remove('hidden');
            });
        });
    </script> 
</body>
</html>

<?php
$conn->close();
?>