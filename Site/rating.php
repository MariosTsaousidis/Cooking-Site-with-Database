<?php
// Συμπερίληψη του αρχείου config.php για σύνδεση με τη βάση δεδομένων
include 'config.php';

// Αρχικοποίηση των queries
$query = "SELECT * FROM VATHMOLOGIA";
$filter_query = null; // Χρησιμοποιείται όταν πατηθεί το κουμπί

// Έλεγχος αν πατήθηκε κάποιο κουμπί
$show_filter_results = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['filter_avg_sum_5b'])) {
        $filter_query = "SELECT AVG(Arithmos_Vathmologias) AS avg_rating, SUM(Arithmos_Vathmologias) AS sum_rating, ID_Syntaghs FROM VATHMOLOGIA GROUP BY ID_Syntaghs";
        $show_filter_results = true;
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
    <title>Βαθμολογίες</title>
    <style>
        /* Γενικά Στυλ για το σώμα */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Κεντρικό δοχείο των υλικών */
        .rating-container, .filter-container {
            width: 90%;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
        }

         /* Απόκρυψη */
         .hidden {
            display: none;
        }

        /* Στυλ για το κουμπί */
        .filter-btn {
            display: inline-block;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            font-size: 1.2em;
            font-weight: bold;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            text-align: center;
            margin: 20px auto;
            display: block;
            width: 250px;
            text-align: center;
        }
        .filter-btn:hover {
            background-color: #45a049;
        }

        /* Στυλ τίτλου της σελίδας */
        h1 {
            text-align: center;
            color: #3498db;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }

        /* Στυλ για κάθε υλικό */
        .VATHMOLOGIA {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Εφέ hover για το κάθε υλικό (κίνηση και σκιά) */
        .VATHMOLOGIA:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        /* Στυλ τίτλου υλικού */
        .VATHMOLOGIA h3 {
            font-size: 1.8rem;
            color: #2c3e50;
            margin: 0 0 10px;
            font-weight: 600;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }

        /* Στυλ για τις πληροφορίες του υλικού */
        .VATHMOLOGIA p {
            font-size: 1.1rem;
            color: #34495e;
            line-height: 1.6;
            margin: 10px 0;
        }

        /* Έντονη εμφάνιση για τα labels των πληροφοριών */
        .VATHMOLOGIA p strong {
            color: #3498db;
            font-weight: 600;
        }

        /* Στυλ για το κείμενο όταν δεν υπάρχουν διαθέσιμα υλικά */
        .no-ratings {
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

            .VATHMOLOGIA h3 {
                font-size: 1.5rem;
            }

            .rating-container {
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
    <h1>Όλες οι βαθμολογίες</h1>
    
    <!-- Κεντρικό δοχείο για όλες τις βαθμολογίες -->
    <div class="rating-container <?php echo $show_filter_results ? 'hidden' : ''; ?>">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='VATHMOLOGIA'>";
                echo "<h3>" . $row['Onoma_Xrhsth'] . "</h3>";
                echo "<p><strong>ID_Syntaghs:</strong> " . $row['ID_Syntaghs'] . "</p>";
                echo "<p><strong>Arithmos_Vathmologias:</strong> " . $row['Arithmos_Vathmologias'] . "</p>";
                echo "<p><strong>Hmeromhnia_Vathmologias:</strong> " . $row['Hmeromhnia_Vathmologias'] . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p class='no-ratings'>Δεν υπάρχουν διαθέσιμες βαθμολογίες.</p>";
        }
        ?>
    </div>


        <!-- Κεντρικό δοχείο για τα αποτελέσματα φιλτραρίσματος -->
    <div class="filter-container <?php echo !$show_filter_results ? 'hidden' : ''; ?>">
        <?php
        if ($filter_result && $filter_result->num_rows > 0) {
            while ($row = $filter_result->fetch_assoc()) {
                echo "<div class='VATHMOLOGIA'>";
                echo "<h3>" . $row['ID_Syntaghs'] . "</h3>";
                echo "<p><strong>Μέσος Όρος:</strong> " . $row['avg_rating'] . "</p>";
                echo "<p><strong>Συνολικός Αριθμός:</strong> " . $row['sum_rating'] . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p class='no-ratings'>Δεν υπάρχουν αποτελέσματα για το φιλτράρισμα.</p>";
        }
        ?>
    </div>

    <!-- Φόρμα για φιλτράρισμα -->
    <form method="POST">
        <button type="submit" name="filter_avg_sum_5b" class="filter-btn">5.b Συναρτησιακή Συνάρτηση AVG/SUM</button>
    </form>
</body>
</html>
