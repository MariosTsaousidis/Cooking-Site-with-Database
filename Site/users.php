<?php
// Συμπερίληψη του αρχείου config.php για σύνδεση με τη βάση δεδομένων
include 'config.php';

// Επιλογή όλων των χρηστών από τη βάση δεδομένων
$query = "SELECT * FROM XRHSTHS";
$filter_query = null; // Χρησιμοποιείται όταν πατηθεί το κουμπί
$filter_query1 = null; // Χρησιμοποιείται όταν πατηθεί το κουμπί
// Έλεγχος αν πατήθηκε κάποιο κουμπί
$show_filter_results = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['inner_join'])) {
        $filter_query = "SELECT XRHSTHS.Onoma_Xrhsth, VATHMOLOGIA.Arithmos_Vathmologias, VATHMOLOGIA.Hmeromhnia_Vathmologias, VATHMOLOGIA.ID_Syntaghs
                  FROM XRHSTHS
                  INNER JOIN VATHMOLOGIA ON XRHSTHS.Onoma_Xrhsth = VATHMOLOGIA.Onoma_Xrhsth
                  ORDER BY Arithmos_Vathmologias ASC";
        $show_filter_results = true;
    } elseif (isset($_POST['left_join'])) {
        $filter_query = "SELECT XRHSTHS.Onoma_Xrhsth, VATHMOLOGIA.Arithmos_Vathmologias, VATHMOLOGIA.Hmeromhnia_Vathmologias, VATHMOLOGIA.ID_Syntaghs
                         FROM XRHSTHS
                         LEFT JOIN VATHMOLOGIA ON XRHSTHS.Onoma_Xrhsth = VATHMOLOGIA.Onoma_Xrhsth
                         ORDER BY Arithmos_Vathmologias DESC";
        $show_filter_results = true;
    } elseif (isset($_POST['view'])) {
        $filter_query1 = "SELECT * FROM Όνομα_View GROUP BY Keimeno ORDER BY Onoma_Xrhsth";
        $show_filter_results = true;
    }
}
// Εκτέλεση queries
$result = $conn->query($query);
$filter_result = $filter_query ? $conn->query($filter_query) : null;
$filter_result1 = $filter_query1 ? $conn->query($filter_query1) : null;
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Χρήστες</title>
    <style>
        /* Γενικά Στυλ για το σώμα */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Κεντρικό δοχείο των χρήστων */
        .users-container, .filter-container {
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

        /* Στυλ τίτλου της σελίδας */
        h1 {
            text-align: center;
            color: #3498db;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }

        /* Στυλ για κάθε χρήστη */
        .XRHSTHS {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Εφέ hover για το κάθε χρήστη (κίνηση και σκιά) */
        .XRHSTHS:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        /* Στυλ τίτλου χρήστη */
        .XRHSTHS h3 {
            font-size: 1.8rem;
            color: #2c3e50;
            margin: 0 0 10px;
            font-weight: 600;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }

        /* Στυλ για τις πληροφορίες του χρήστη */
        .XRHSTHS p {
            font-size: 1.1rem;
            color: #34495e;
            line-height: 1.6;
            margin: 10px 0;
        }

        /* Έντονη εμφάνιση για τα labels των πληροφοριών */
        .XRHSTHS p strong {
            color: #3498db;
            font-weight: 600;
        }

        /* Στυλ για το κείμενο όταν δεν υπάρχουν διαθέσιμα χρήστες */
        .no-users {
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

            .XRHSTHS h3 {
                font-size: 1.5rem;
            }

            .users-container {
                padding: 20px;
            }
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
    <h1>Όλοι οι Χρήστες</h1>

    <!-- Κεντρικό δοχείο για όλες τους χρήστες -->
    <div class="users-container <?php echo $show_filter_results ? 'hidden' : ''; ?>">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='XRHSTHS'>";
                echo "<h3>" . $row['Onoma_Xrhsth'] . "</h3>";
                echo "<p><strong>Email:</strong> " . $row['Email'] . "</p>";
                echo "<p><strong>Ημερομηνία εγγραφής:</strong> " . $row['Hmeromhnia_Eggrafhs'] . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p class='no-comments'>Δεν υπάρχουν διαθέσιμα σχόλια.</p>";
        }
        ?>
    </div>


        <!-- Κεντρικό δοχείο για τα αποτελέσματα φιλτραρίσματος -->
    <div class="filter-container <?php echo !$show_filter_results ? 'hidden' : ''; ?>">
        <?php
        if ($filter_result && $filter_result->num_rows > 0) {
            while ($row = $filter_result->fetch_assoc()) {
                echo "<div class='XRHSTHS'>";
                echo "<h3>" . $row['Onoma_Xrhsth'] . "</h3>";
                echo "<p><strong>Αριθμός Βαθμολογίας:</strong> " . $row['Arithmos_Vathmologias'] . "</p>";
                echo "<p><strong>Ημερομηνία Βαθμολογίας:</strong> " . $row['Hmeromhnia_Vathmologias'] . "</p>";
                echo "<p><strong>ID Συνταγής:</strong> " . $row['ID_Syntaghs'] . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p class='no-ratings'>Δεν υπάρχουν αποτελέσματα για το φιλτράρισμα.</p>";
        }
        ?>
    </div>

      <!-- Κεντρικό δοχείο για τα αποτελέσματα φιλτραρίσματος -->
      <div class="filter-container <?php echo !$show_filter_results ? 'hidden' : ''; ?>">
        <?php
        if ($filter_result1 && $filter_result1->num_rows > 0) {
            while ($row = $filter_result1->fetch_assoc()) {
                echo "<div class='XRHSTHS'>";
                echo "<h3>" . $row['Onoma_Xrhsth'] . "</h3>";
                echo "<p><strong>Κείμενο:</strong> " . $row['Keimeno'] . "</p>";
                echo "<p><strong>Αριθμός Βαθμολογίας:</strong> " . $row['Arithmos_Vathmologias'] . "</p>";
                echo "<p><strong>ID Συνταγής:</strong> " . $row['ID_Syntaghs'] . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p class='no-ratings'>Δεν υπάρχουν αποτελέσματα για το φιλτράρισμα.</p>";
        }
        ?>
    </div>

    <h2>Φόρμα για φιλτράρισμα</h2>
    <form method="POST">
        <button type="submit" name="inner_join" class="filter-btn">6.a INNER JOIN</button>
        <button type="submit" name="left_join" class="filter-btn">6.b LEFT JOIN</button>
        <button type="submit" name="view" class="filter-btn">7 VIEW</button>
    </form>
</body>
</html>
