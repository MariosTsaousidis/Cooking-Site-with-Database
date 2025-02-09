<?php
// Συμπερίληψη του αρχείου config.php για σύνδεση με τη βάση δεδομένων
include 'config.php';

// Επιλογή όλων των χορηγών από τη βάση δεδομένων
$query = "SELECT * FROM XORHGOS";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Χορηγοί</title>
    <style>
        /* Γενικά Στυλ για το σώμα */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Κεντρικό δοχείο των υλικών */
        .sponsors-container {
            width: 90%;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
        }

        /* Στυλ τίτλου της σελίδας */
        h1 {
            text-align: center;
            color: #3498db;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }

        /* Στυλ για κάθε υλικό */
        .XORHGOS {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Εφέ hover για το κάθε υλικό (κίνηση και σκιά) */
        .XORHGOS:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        /* Στυλ τίτλου υλικού */
        .XORHGOS h3 {
            font-size: 1.8rem;
            color: #2c3e50;
            margin: 0 0 10px;
            font-weight: 600;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }

        /* Στυλ για τις πληροφορίες του υλικού */
        .XORHGOS p {
            font-size: 1.1rem;
            color: #34495e;
            line-height: 1.6;
            margin: 10px 0;
        }

        /* Έντονη εμφάνιση για τα labels των πληροφοριών */
        .XORHGOS p strong {
            color: #3498db;
            font-weight: 600;
        }

        /* Στυλ για το κείμενο όταν δεν υπάρχουν διαθέσιμα υλικά */
        .no-sponsors {
            text-align: center;
            font-size: 1.2rem;
            color: #e74c3c;
            font-weight: bold;
        }

        /* Responsive Στυλ για μικρές οθόνες */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            .XORHGOS h3 {
                font-size: 1.5rem;
            }

            .sponsors-container {
                padding: 20px;
            }
        }
.title-button {
    display: inline-block;
    text-decoration: none;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    font-size: 1.5em;
    font-weight: bold;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    text-align: center;
}
.title-button:hover {
    background-color: #45a049;
        }
    </style>
</head>
<body>
    <a href="recipes.php" class="title-button">Συνταγές</a>
    <h1>Όλοι οι Χορηγοί</h1>

    <div class="sponsors-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='XORHGOS'>";
                echo "<h3>" . $row['Onomasia_Xorhgou'] . "</h3>";
                echo "<p><strong>Έδρα Εταιρείας:</strong> " . $row['Edra_Etaireias'] . "</p>";
                echo "<p><strong>Ποσότητα υλικών χορηγίας:</strong> " . $row['Posothta_Ylikwn_Xorhgias'] . "</p>";
                echo "<p><strong>Τηλέφωνο Επικοινωνίας:</strong> " . $row['Tilefwno_Epikoinwnias'] . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>Δεν υπάρχουν διαθέσιμοι χορηγοί.</p>";
        }
        ?>
    </div>
</body>
</html>
