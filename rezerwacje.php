<link rel="stylesheet" href="rezerwacje.css">
<?php
// Połączenie z bazą - ustaw swoje dane
$host = "localhost";
$user = "root";
$password = "";
$dbname = "modernforms_system";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Pobieramy 30 najbliższych rezerwacji
$sql = "SELECT Data, Godzina, Status, Rezerwacja FROM sala_konf1 WHERE Data >= CURDATE() ORDER BY Data, Godzina LIMIT 30";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wszystkie rezerwacje</title>
    <link rel="stylesheet" href="sala1.css">
</head>
<body>

<div id="glowny">
    <div id="blok_sali">
        <h1>Więcej dostępnych terminów</h1>

        <div id="rezerwacje">
            <table id="tabela-rezerwacji">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Godzina</th>
                        <th>Status</th>
                        <th>Rezerwacja</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>" . htmlspecialchars($row['Data']) . "</td>
                                <td>" . htmlspecialchars($row['Godzina']) . "</td>
                                <td>" . htmlspecialchars($row['Status']) . "</td>
                                <td>" . htmlspecialchars($row['Rezerwacja']) . "</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Brak dostępnych rezerwacji</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>

<?php
$conn->close();
?>
