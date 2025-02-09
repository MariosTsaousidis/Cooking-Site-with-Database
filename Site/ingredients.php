<?php
// Συμπερίληψη του αρχείου config.php για σύνδεση με τη βάση δεδομένων
include 'config.php';

// Επιλογή όλων των υλικών από τη βάση δεδομένων
$query = "SELECT * FROM YLIKO";
$filter_query = null; // Χρησιμοποιείται όταν πατηθεί το κουμπί
// Έλεγχος αν πατήθηκε κάποιο κουμπί
$show_filter_results = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['filter_sort_4b'])) {
        $filter_query = "SELECT * FROM YLIKO WHERE Plithos_Thermidon_per100g > 100 ORDER BY Plithos_Thermidon_per100g DESC";
    } elseif (isset($_POST['filter_logic_4c'])) {
        $filter_query = "SELECT * FROM YLIKO WHERE (Onomasia_Xorhgou = 'Company F' AND Kathgoria = 'Λαχανικά') OR Kathgoria = 'Δημητριακά'";
    } elseif (isset($_POST['procedure'])) {
        $filter_query = "CALL Example_Procedure";
    } 
}
// Εκτέλεση queries
$result = $conn->query($query);
$filter_result = $filter_query ? $conn->query($filter_query) : null;
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Υλικά</title>
    <style>
        .filter-button {
            padding: 10px 20px;
            background-color: #FF6347;
            color: white;
            font-size: 1em;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .filter-button:hover {
            background-color: #FF4500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
        /* Γενικά Στυλ για το σώμα */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Κεντρικό δοχείο των υλικών */
        .ingredients-container {
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
        .YLIKO {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Εφέ hover για το κάθε υλικό (κίνηση και σκιά) */
        .YLIKO:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        /* Στυλ τίτλου υλικού */
        .YLIKO h3 {
            font-size: 1.8rem;
            color: #2c3e50;
            margin: 0 0 10px;
            font-weight: 600;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }

        /* Στυλ για τις πληροφορίες του υλικού */
        .YLIKO p {
            font-size: 1.1rem;
            color: #34495e;
            line-height: 1.6;
            margin: 10px 0;
        }

        /* Έντονη εμφάνιση για τα labels των πληροφοριών */
        .YLIKO p strong {
            color: #3498db;
            font-weight: 600;
        }

        /* Στυλ για το κείμενο όταν δεν υπάρχουν διαθέσιμα υλικά */
        .no-ingredients {
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

            .YLIKO h3 {
                font-size: 1.5rem;
            }

            .ingredients-container {
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
        .button-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .back-button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
        }

        .back-button:hover {
            background-color: #2980b9;
        }

        .filter-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            text-align: center;
            margin: 10px;
        }

        .filter-btn:hover {
            background-color: #45a049;
        }

        .filter-form {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <a href="recipes.php" class="title-button">Συνταγές</a>
    <h1>Όλα τα Υλικά</h1>

    <!-- Κεντρικό δοχείο για όλες τις βαθμολογίες -->
    <div class="ingredients-container <?php echo $show_filter_results ? 'hidden' : ''; ?>">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='YLIKO'>";
                echo "<h3>" . $row['Onomasia_Ylikou'] . "</h3>";
                echo "<p><strong>Πλήθος Θερμιδών ανά 100γρ:</strong> " . $row['Plithos_Thermidon_per100g'] . "</p>";
                echo "<p><strong>Χορηγός υλικού:</strong> " . $row['Onomasia_Xorhgou'] . "</p>";
                echo "<p><strong>Κατηγορία υλικού:</strong> " . $row['Kathgoria'] . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p class='no-ingredients'>Δεν υπάρχουν διαθέσιμα υλικά.</p>";
        }
        ?>
    </div>


        <!-- Κεντρικό δοχείο για τα αποτελέσματα φιλτραρίσματος -->
    <div class="filter-container <?php echo !$show_filter_results ? 'hidden' : ''; ?>">
        <?php
        if ($filter_result && $filter_result->num_rows > 0) {
            while ($row = $filter_result->fetch_assoc()) {
                echo "<div class='VATHMOLOGIA'>";
                echo "<h3>" . $row['Onomasia_Ylikou'] . "</h3>";
                echo "<p><strong>Μέσος Όρος:</strong> " . $row['ID_Ylikou'] . "</p>";
                echo "<p><strong>Συνολικός Αριθμός:</strong> " . $row['Plithos_Thermidon_per100g'] . "</p>";
                echo "<p><strong>Μέσος Όρος:</strong> " . $row['Kathgoria'] . "</p>";
                echo "<p><strong>Συνολικός Αριθμός:</strong> " . $row['Onomasia_Xorhgou'] . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p class='no-ratings'>Δεν υπάρχουν αποτελέσματα για το φιλτράρισμα.</p>";
        }
        ?>
    </div>

    <!-- Φόρμα για φιλτράρισμα -->
    <form method="POST">
        <button type="submit" name="filter_sort_4b" class="filter-btn">4.b Φιλτράρισμα και Ταξινόμηση</button>
        <button type="submit" name="filter_logic_4c" class="filter-btn">4.c Λογικοί Τελεστές</button>
        <button type="submit" name="procedure" class="filter-btn">8 Procedure</button>
    </form>
</body>
</html>
