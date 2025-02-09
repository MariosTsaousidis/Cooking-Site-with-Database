<?php
// Συμπερίληψη του αρχείου config.php για σύνδεση με τη βάση δεδομένων
include 'config.php';

// Έλεγχος αν η φόρμα υποβλήθηκε
if (isset($_POST['submit_recipe'])) {
    $username = $_POST['username'];
    //$recipe_ID = $_POST['recipe_ID'];
    $recipe_name = $_POST['recipe_name'];
    $description = $_POST['description'];
    $prep_time = $_POST['prep_time'];
    $difficulty = $_POST['difficulty'];
    //$rating = $_POST['rating'];
    $category_id = $_POST['category_id'];

    // Αναζήτηση του χρήστη στη βάση δεδομένων
    $user_query = "SELECT Onoma_Xrhsth FROM XRHSTHS WHERE Onoma_Xrhsth = '$username'";
    $user_result = $conn->query($user_query);

    // Αν δεν βρεθεί χρήστης, εμφανίζουμε μήνυμα σφάλματος
    if ($user_result->num_rows == 0) {
        echo "<script>alert('Ο χρήστης δεν βρέθηκε. Παρακαλώ δοκιμάστε ξανά.');</script>";
    } else {
        $user_row = $user_result->fetch_assoc();
        $user_id = $user_row['Onoma_Xrhsth'];

        // Εισαγωγή της νέας συνταγής στη βάση δεδομένων
        $insert_recipe = "INSERT INTO SYNTAGH (Onoma_Syntaghs, Perigrafh, Xronos_Paragwgis, Duskolia , ID_kathgorias) 
        VALUES ('$recipe_name', '$description', '$prep_time', '$difficulty', '$category_id')";

        if ($conn->query($insert_recipe) === TRUE) {
            // Απόκτηση του ID της τελευταίας συνταγής που προστέθηκε
            $recipe_id = $conn->insert_id;

            // Εισαγωγή συσχετίσεων υλικών για τη συνταγή
            foreach ($ingredients as $ingredient_id) {
                $insert_ingredient = "INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES ('$recipe_id', '$ingredient_id')";
                $conn->query($insert_ingredient);
            }
            echo "<script>alert('Η συνταγή προστέθηκε επιτυχώς!');</script>";
            echo "<script>window.location.href = window.location.href;</script>"; // Ανανέωση της σελίδας για εμφάνιση της νέας συνταγής
            } else {
            echo "<script>alert('Υπήρξε σφάλμα κατά την προσθήκη της συνταγής.');</script>";
            }
        }
}

// Αρχικοποίηση μεταβλητής για το query
$filter_query = "SELECT * FROM SYNTAGH";

// Έλεγχος αν πατήθηκε κάποιο κουμπί
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['filter_like_4a'])) {
        $filter_query = "SELECT * FROM SYNTAGH WHERE ID_Kathgorias LIKE '%C1%' GROUP BY Onoma_Syntaghs";
    } elseif (isset($_POST['filter_count_5a'])){
        $filter_query = "SELECT Onoma_Xrhsth, COUNT(*) as 'Πληθος σχολιων' FROM SXOLIO GROUP BY Onoma_Xrhsth";
    } elseif (isset($_POST['filter_avg_sum_5b'])) {
        $filter_query = "SELECT AVG(Arithmos_Vathmologias), SUM(Arithmos_Vathmologias), ID_Syntaghs FROM VATHMOLOGIA GROUP BY ID_Syntaghs";
    } elseif (isset($_POST['filter_inner_join_6a'])) {
        $filter_query = "SELECT XRHSTHS.Onoma_Xrhsth, VATHMOLOGIA.Arithmos_Vathmologias, VATHMOLOGIA.Hmeromhnia_Vathmologias, VATHMOLOGIA.ID_Syntaghs
                         FROM XRHSTHS
                         INNER JOIN VATHMOLOGIA ON XRHSTHS.Onoma_Xrhsth = VATHMOLOGIA.Onoma_Xrhsth
                         ORDER BY Arithmos_Vathmologias ASC";
    } elseif (isset($_POST['filter_left_join_6b'])) {
        $filter_query = "SELECT XRHSTHS.Onoma_Xrhsth, VATHMOLOGIA.Arithmos_Vathmologias, VATHMOLOGIA.Hmeromhnia_Vathmologias, VATHMOLOGIA.ID_Syntaghs
                         FROM XRHSTHS
                         LEFT JOIN VATHMOLOGIA ON XRHSTHS.Onoma_Xrhsth = VATHMOLOGIA.Onoma_Xrhsth
                         ORDER BY Arithmos_Vathmologias DESC";
    }
}

// Εκτέλεση του query
$result = $conn->query($filter_query);
?>


<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Συνταγές</title>
    <style>
        /* Γενικά Στυλ Σώματος */
/* Γενικά Στυλ Σώματος */
/* Γενικά Στυλ Σώματος */
body {
    font-family: 'Arial', sans-serif;
    background-image: url('recipes.jpg'); /* Εικόνα φόντου για το site */
    background-size: cover;
    background-position: center;
    margin: 0;
    padding: 0;
    color: #333;
}

/* Κεντρικό δοχείο για τις συνταγές */
.recipe-container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 20px;
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 12px;
    box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
}

/* Στυλ τίτλου */
h1 {
    text-align: center;
    font-size: 2rem;
    color: #4CAF50;
    margin-bottom: 20px;
    font-weight: 700;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.2);
}

/* Στυλ για τα κουμπιά */
.buttons-container {
    text-align: center;
    margin-bottom: 20px;
}

.button {
    padding: 8px 16px;
    font-size: 1rem;
    color: white;
    background-color: #3498db;
    text-decoration: none;
    border-radius: 6px;
    margin: 8px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.button:hover {
    background-color: #2980b9;
    transform: scale(1.05);
}

/* Στυλ για κάθε συνταγή */
.recipe {
    background-color: #ffffff;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 10px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Εφέ hover στις συνταγές */
.recipe:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.2);
}

/* Στυλ τίτλου της συνταγής */
.recipe h2 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 10px;
    font-weight: 600;
    text-transform: capitalize;
}

/* Στυλ για τις πληροφορίες της συνταγής */
.recipe p {
    font-size: 0.95rem;
    line-height: 1.5;
    margin: 6px 0;
    color: #555;
}

.recipe p span {
    font-weight: 600;
    color: #4CAF50;
}

/* Στυλ για τον σύνδεσμο 'Δείτε λεπτομέρειες' */
.recipe a {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 16px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    text-transform: uppercase;
    transition: background-color 0.3s, transform 0.2s ease-in-out;
}

/* Εφέ hover στον σύνδεσμο */
.recipe a:hover {
    background-color: #0056b3;
    transform: scale(1.08);
}

/* Στυλ για το μήνυμα όταν δεν υπάρχουν συνταγές */
p.no-recipes {
    text-align: center;
    font-size: 1rem;
    color: #ff4d4d;
    font-weight: bold;
}

/* Responsive Στυλ για μικρές οθόνες */
@media (max-width: 768px) {
    .recipe-container {
        padding: 15px;
    }

    .recipe {
        padding: 10px;
    }

    h1 {
        font-size: 1.8rem;
    }

    .recipe h2 {
        font-size: 1.3rem;
    }
}

/* Στυλ για τη φόρμα προσθήκης νέας συνταγής */
.recipe-form-container {
    margin-top: 20px;
    background-color: rgba(255, 255, 255, 0.95);
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
}

.recipe-form-container h2 {
    color: #3498db;
    font-size: 1.3rem;
    border-bottom: 1px solid #ddd;
    padding-bottom: 8px;
}

.recipe-form-container form {
    display: flex;
    flex-direction: column;
}

.recipe-form-container input[type="text"],
.recipe-form-container input[type="number"],
.recipe-form-container textarea {
    margin-bottom: 8px;
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

.recipe-form-container button {
    padding: 8px;
    border: none;
    background-color: #4CAF50;
    color: white;
    font-size: 1em;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 8px;
}

.recipe-form-container button:hover {
    background-color: #45a049;
}

/* Νέα προσθήκη: Εφέ σκιάς σε hover για πιο κομψά κουμπιά */
.filter-btn {
    padding: 10px 18px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease, background-color 0.3s ease;
}

.filter-btn:hover {
    background-color: #45a049;
    box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
}

.title-button {
    display: inline-block;
    text-decoration: none;
    padding: 10px 18px;
    background-color: #4CAF50;
    color: white;
    font-size: 1.3em;
    font-weight: bold;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    text-align: center;
    transition: transform 0.3s ease, background-color 0.3s ease;
}

.title-button:hover {
    background-color: #45a049;
    transform: translateY(-3px);
}
    </style>
</head>
<body>
    <a href="recipes.php" class="title-button">Διαθέσιμες Συνταγές</a>

    <!-- Κουμπιά για Υλικά, Χορηγούς, Χρήστες -->
    <div class="buttons-container">
        <a href="ingredients.php" class="button">Υλικά</a>
        <a href="sponsors.php" class="button">Χορηγοί</a>
        <a href="users.php" class="button">Χρήστες</a>
        <a href="rating.php" class="button">Βαθμολογία</a>
        <a href="comments.php" class="button">Σχόλια</a>
    </div>

    <div class="recipe-container">
        <?php
        // Εμφάνιση όλων των συνταγών σε οριζόντια πλαίσια
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='recipe'>";
                echo "<h2>" . $row['Onoma_Syntaghs'] . "</h2>";
                echo "<p><span>Περιγραφή:</span> " . $row['Perigrafh'] . "</p>";
                echo "<p><span>Χρόνος προετοιμασίας:</span> " . $row['Xronos_Paragwgis'] . " λεπτά</p>";
                echo "<p><span>Δυσκολία:</span> " . $row['Duskolia'] . "</p>";
                echo "<p><span>Βαθμολογία Συνταγής:</span> " . $row['Vathmologia_Syntaghs'] . "</p>";
                echo "<p><span>ID Κατηγορίας:</span> " . $row['ID_kathgorias'] . "</p>";
                echo "<a href='recipe_details.php?id=" . $row['ID_Syntaghs'] . "'>Δείτε λεπτομέρειες</a>"; // Σύνδεσμος για λεπτομέρειες συνταγής
                echo "</div>";
            }
        } else {
            echo "<p style='text-align:center;'>Δεν υπάρχουν διαθέσιμες συνταγές.</p>";
        }

        // Κλείσιμο της σύνδεσης
        $conn->close();
        ?>
    </div>

    <h2>Φόρμα για φιλτράρισμα</h2>
    <form method="POST">
        <button type="submit" name="filter_like_4a" class="filter-btn">4.a Χρήση τελεστή LIKE</button>
        <button type="submit" name="filter_sort_4b" class="filter-btn">4.b Φιλτράρισμα και Ταξινόμηση</button>
        <button type="submit" name="filter_logic_4c" class="filter-btn">4.c Λογικοί Τελεστές</button>
        <button type="submit" name="filter_count_5a" class="filter-btn">5.a Συναρτησιακή Συνάρτηση COUNT</button>
        <button type="submit" name="filter_avg_sum_5b" class="filter-btn">5.b Συναρτησιακή Συνάρτηση AVG/SUM</button>
        <button type="submit" name="filter_inner_join_6a" class="filter-btn">6.a INNER JOIN</button>
        <button type="submit" name="filter_left_join_6b" class="filter-btn">6.b LEFT JOIN</button>
    </form>


    <div class="recipes">
        <?php
        if ($result->num_rows > 0) {
            while ($recipe = $result->fetch_assoc()) {
                echo "<div class='recipe-container'>";
                echo "<h2>" . htmlspecialchars($recipe['Onoma_Syntaghs']) . "</h2>";
                echo "<p><strong>Περιγραφή:</strong> " . htmlspecialchars($recipe['Perigrafh']) . "</p>";
                echo "<p><strong>Χρόνος Προετοιμασίας:</strong> " . htmlspecialchars($recipe['Xronos_Paragwgis']) . " λεπτά</p>";
                echo "<p><strong>Δυσκολία:</strong> " . htmlspecialchars($recipe['Duskolia']) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>Δεν βρέθηκαν συνταγές.</p>";
        }
        ?>
    </div>

    <div class="buttons-container">
        <button class="button" id="addRecipeButton">Προσθήκη Νέας Συνταγής</button>
    </div>

    <!-- Φόρμα για την προσθήκη νέας συνταγής (κρυφή αρχικά) -->
    <div class="recipe-form-container" id="recipeFormContainer" style="display: none;">
        <h2>Προσθέστε Νέα Συνταγή</h2>
        <form id="recipeForm" method="POST">
            <label for="username">Όνομα Χρήστη:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="recipe_name">Όνομα Συνταγής:</label>
            <input type="text" id="recipe_name" name="recipe_name" required><br><br>

            <label for="description">Περιγραφή:</label>
            <textarea id="description" name="description" rows="4" required></textarea><br><br>

            <label for="prep_time">Χρόνος Προετοιμασίας (σε λεπτά):</label>
            <input type="number" id="prep_time" name="prep_time" required><br><br>

            <label for="difficulty">Δυσκολία:</label>
            <input type="text" id="difficulty" name="difficulty" required><br><br>

            <!--<label for="rating">Βαθμολογία Συνταγής (0-5):</label>
            <input type="number" id="rating" name="rating" min="0" max="5" required><br><br>-->

            <label for="category_id">ID Κατηγορίας(C1:Σαλάτες, C2:Γλυκά, C3:Κυρίως Πιάτα, C4:Ορεκτικά, C5:Σούπες, C6:Επιδόρπια, C7:Σνακς):</label>
            <input type="text" id="category_id" name="category_id" required><br><br>

            <button type="submit" name="submit_recipe">Υποβολή Συνταγής</button>
        </form>
    </div>
<script>
    document.getElementById('addRecipeButton').addEventListener('click', function() {
        var formContainer = document.getElementById('recipeFormContainer');
        if (formContainer.style.display === 'none' || formContainer.style.display === '') {
            formContainer.style.display = 'block';
        } else {
            formContainer.style.display = 'none';
        }
    });
</script>
</body>
</html>
