<?php
include 'config.php';

// Λήψη της ID_Syntaghs από το URL
$id = $_GET['id'];

// Έλεγχος αν η φόρμα υποβλήθηκε για σχόλια
if (isset($_POST['submit_comment'])) {
    $username = $_POST['username'];
    $comment = $_POST['comment'];
    $current_date = date('Y-m-d H:i:s');
    $rating = $_POST['rating'];

    // Αναζήτηση του χρήστη στη βάση δεδομένων
    $user_query = "SELECT Onoma_Xrhsth FROM XRHSTHS WHERE Onoma_Xrhsth = '$username'";
    $user_result = $conn->query($user_query);

    if ($user_result->num_rows == 0) {
        echo "<script>alert('Ο χρήστης δεν βρέθηκε. Παρακαλώ δοκιμάστε ξανά.');</script>";
    } else {
        $user_row = $user_result->fetch_assoc();
        $user_id = $user_row['Onoma_Xrhsth'];

        // Εισαγωγή του νέου σχολίου στη βάση δεδομένων
        $insert_comment = "INSERT INTO SXOLIO (ID_Syntaghs, Onoma_Xrhsth, Keimeno, Hmeromhnia_Dhmosieyshs) 
                           VALUES ('$id', '$user_id', '$comment', '$current_date')";
        
        if ($conn->query($insert_comment) === TRUE) {
            $sxolio_id = $conn->insert_id; // Παίρνουμε το ID του σχολίου που μόλις εισάχθηκε

            // Εισαγωγή της βαθμολογίας στον πίνακα Vatmologia
            $insert_rating = "INSERT INTO VATHMOLOGIA (Arithmos_Vathmologias, Hmeromhnia_Vathmologias, Onoma_Xrhsth, ID_Syntaghs) VALUES ('$rating', '$current_date', '$user_id', '$id')";
            if ($conn->query($insert_rating) === TRUE) {
                echo "<script>alert('Το σχόλιο και η βαθμολογία προστέθηκαν επιτυχώς!');</script>";
                echo "<script>window.location.href = window.location.href;</script>";
            } else {
                echo "<script>alert('Υπήρξε σφάλμα κατά την προσθήκη της βαθμολογίας.');</script>";
            }
        } else {
            echo "<script>alert('Υπήρξε σφάλμα κατά την προσθήκη του σχολίου.');</script>";
        }
    }
}

// *** Έλεγχος για επεξεργασία, προσθήκη ή διαγραφή υλικού ***
if (isset($_POST['update_ingredient'])) {
    // Επεξεργασία υπάρχοντος υλικού
    $ingredient_id = $_POST['ingredient_id'];
    $quantity = $_POST['quantity'];

    $update_query = "UPDATE SYNTAGH_YLIKO SET Posotita = '$quantity' WHERE ID_Ylikou = '$ingredient_id' AND ID_Syntaghs = '$id'";
    $conn->query($update_query);
    echo "<script>alert('Το υλικό ενημερώθηκε επιτυχώς!');</script>";
    echo "<script>window.location.href = window.location.href;</script>";
}

if (isset($_POST['delete_ingredient'])) {
    // Διαγραφή υπάρχοντος υλικού
    $ingredient_id = $_POST['ingredient_id'];

    $delete_query = "DELETE FROM SYNTAGH_YLIKO WHERE ID_Ylikou = '$ingredient_id' AND ID_Syntaghs = '$id'";
    $conn->query($delete_query);
    echo "<script>alert('Το υλικό διαγράφηκε επιτυχώς!');</script>";
    echo "<script>window.location.href = window.location.href;</script>";
}

if (isset($_POST['add_ingredient'])) {
    // Προσθήκη νέου υλικού
    $new_ingredient = $_POST['new_ingredient'];
    $new_quantity = $_POST['new_quantity'];

    // Εισαγωγή του νέου υλικού για τη συνταγή
    $insert_ingredient = "INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) 
                          VALUES ('$id', '$new_ingredient', '$new_quantity')";
    $conn->query($insert_ingredient);
    echo "<script>alert('Το νέο υλικό προστέθηκε επιτυχώς!');</script>";
    echo "<script>window.location.href = window.location.href;</script>";
}

// Επιλογή της συνταγής από τη βάση δεδομένων
$query = "SELECT * FROM SYNTAGH WHERE ID_Syntaghs = '$id'";
$result = $conn->query($query);
$recipe = $result->fetch_assoc();

// Επιλογή των υλικών που σχετίζονται με αυτή τη συνταγή
$query_ingredients = "SELECT Y.ID_Ylikou, Y.Onomasia_Ylikou, SY.Posotita 
                      FROM SYNTAGH_YLIKO SY 
                      JOIN YLIKO Y ON SY.ID_Ylikou = Y.ID_Ylikou 
                      WHERE SY.ID_Syntaghs = '$id'";
$result_ingredients = $conn->query($query_ingredients);

// Επιλογή των σχολίων που σχετίζονται με αυτή τη συνταγή
$query_comments = "SELECT X.Onoma_Xrhsth, S.Keimeno, S.Hmeromhnia_Dhmosieyshs 
                   FROM SXOLIO S
                   JOIN XRHSTHS X ON S.Onoma_Xrhsth = X.Onoma_Xrhsth
                   WHERE S.ID_Syntaghs = '$id'
                   ORDER BY S.Hmeromhnia_Dhmosieyshs DESC";
$result_comments = $conn->query($query_comments);
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Λεπτομέρειες Συνταγής</title>
    <style>
        /* Προσαρμοσμένα στυλ */
        body {
    font-family: 'Poppins', sans-serif;
    background-image: url('recipe_details.jpg');
    background-size: cover;
    background-position: center;
    color: #333;
    margin: 0;
    padding: 0;
}

/* Στυλ για το δοχείο των λεπτομερειών */
.recipe-details {
    max-width: 750px;
    margin: 40px auto;
    padding: 25px;
    background-color: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(8px);
}

/* Στυλ για τον τίτλο */
.recipe-details h1 {
    color: #4CAF50;
    font-size: 2.2em;
    border-bottom: 3px solid #e0e0e0;
    padding-bottom: 8px;
    margin-bottom: 15px;
    text-align: center;
    letter-spacing: 1px;
}

/* Στυλ για τα υλικά */
.ingredients {
    margin-top: 25px;
}

.ingredients h2 {
    color: #4CAF50;
    font-size: 1.8em;
    margin-bottom: 10px;
    text-align: left;
}

.ingredients ul {
    list-style: none;
    padding: 0;
}

.ingredients li {
    background: linear-gradient(135deg, #f9f9f9, #ffffff);
    padding: 12px 15px;
    margin-bottom: 10px;
    border-radius: 8px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.ingredients li:hover {
    transform: translateY(-3px);
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
}

/* Στυλ για τα σχόλια */
.comments {
    margin-top: 30px;
}

.comments h2 {
    color: #3498db;
    font-size: 1.8em;
    margin-bottom: 10px;
}

.comment {
    background-color: #f6f6f6;
    padding: 18px;
    margin-bottom: 12px;
    border-radius: 10px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.08);
    transition: background-color 0.3s ease;
}

.comment:hover {
    background-color: #e9f6ff;
}

/* Στυλ για την προσθήκη σχολίου */
.add-comment {
    margin-top: 25px;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
}

.add-comment form {
    display: flex;
    flex-direction: column;
}

.add-comment input[type="text"],
.add-comment textarea {
    margin-bottom: 12px;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 1rem;
}

.add-comment button {
    padding: 12px;
    background-color: #4CAF50;
    color: white;
    font-size: 1em;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s;
}

.add-comment button:hover {
    background-color: #45a049;
    transform: scale(1.05);
}

/* Στυλ κουμπιού τίτλου */
.title-button {
    display: inline-block;
    text-decoration: none;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    font-size: 1.3em;
    font-weight: bold;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    text-align: center;
    transition: background-color 0.3s, transform 0.2s;
}

.title-button:hover {
    background-color: #45a049;
    transform: scale(1.1);
}
/* Στυλ βαθμολογίας */
.rating-container {
    display: flex;
    margin: 10px 0;
    flex-direction: row-reverse; /* Διορθώνει τη φορά */
    justify-content: flex-start;
    gap: 5px;
}

.rating-container input[type="radio"] {
    display: none;
}

.rating-container label {
    font-size: 2rem;
    color: #ccc;
    cursor: pointer;
    transition: color 0.3s ease;
}

.rating-container input[type="radio"]:checked ~ label {
    color: #FFD700; /* Χρυσό για τις επιλεγμένες βαθμολογίες */
}

.rating-container label:hover,
.rating-container label:hover ~ label {
    color: #FFD700;
}
    </style>
</head>
<body>
    <a href="recipes.php" class="title-button">Συνταγές</a>
    <div class="recipe-details">
        <h1><?php echo $recipe['Onoma_Syntaghs']; ?></h1>
        <p><strong>Περιγραφή:</strong> <?php echo $recipe['Perigrafh']; ?></p>
        <p><strong>Χρόνος Προετοιμασίας:</strong> <?php echo $recipe['Xronos_Paragwgis']; ?> λεπτά</p>
        <p><strong>Δυσκολία:</strong> <?php echo $recipe['Duskolia']; ?></p>

        <div class="ingredients">
            <h2>Υλικά</h2>
            <ul>
                <?php
                if ($result_ingredients->num_rows > 0) {
                    while ($ingredient = $result_ingredients->fetch_assoc()) {
                        echo "<li><span>" . $ingredient['Onomasia_Ylikou'] . ":</span> " . $ingredient['Posotita'] . "";
                        echo "<form action='' method='POST' style='display:inline;'>
                                <input type='hidden' name='ingredient_id' value='" . $ingredient['ID_Ylikou'] . "'>
                                <input type='number' name='quantity' value='" . $ingredient['Posotita'] . "' style='width: 70px;'>
                                <button type='submit' name='update_ingredient'>Τροποποίηση</button>
                                <button type='submit' name='delete_ingredient' style='color: red;'>Διαγραφή</button>
                              </form>";
                        echo "</li>";
                    }
                } else {
                    echo "<li>Δεν υπάρχουν διαθέσιμα υλικά για αυτή τη συνταγή.</li>";
                }
                ?>
            </ul>

            <!-- Φόρμα για προσθήκη νέου υλικού -->
            <div class="add-ingredient">
                <h2>Προσθήκη Νέου Υλικού</h2>
                <form action="" method="POST">
                    <label for="new_ingredient">Υλικό:</label>
                    <select id="new_ingredient" name="new_ingredient" required>
                        <?php
                        // Επιλογή όλων των διαθέσιμων υλικών από τη βάση δεδομένων
                        $query_all_ingredients = "SELECT * FROM YLIKO";
                        $all_ingredients = $conn->query($query_all_ingredients);
                        while ($ingredient_option = $all_ingredients->fetch_assoc()) {
                            echo "<option value='" . $ingredient_option['ID_Ylikou'] . "'>" . $ingredient_option['Onomasia_Ylikou'] . "</option>";
                        }
                        ?>
                    </select><br><br>

                    <label for="new_quantity">Ποσότητα :</label>
                    <input type="number" id="new_quantity" name="new_quantity" required><br><br>

                    <button type="submit" name="add_ingredient">Προσθήκη Υλικού</button>
                </form>
            </div>
        </div>

        <div class="comments">
            <h2>Σχόλια</h2>
            <?php
            if ($result_comments->num_rows > 0) {
                while ($comment = $result_comments->fetch_assoc()) {
                    echo "<div class='comment'>";
                    echo "<p class='username'>" . $comment['Onoma_Xrhsth'] . "</p>";
                    echo "<p class='date'>" . $comment['Hmeromhnia_Dhmosieyshs'] . "</p>";
                    echo "<p>" . $comment['Keimeno'] . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>Δεν υπάρχουν σχόλια για αυτή τη συνταγή.</p>";
            }
            ?>

            <!-- Φόρμα για προσθήκη νέου σχολίου -->
            <div class="add-comment">
                <h2>Προσθέστε το σχόλιό σας</h2>
                <form action="" method="POST">
                    <label for="username">Όνομα Χρήστη:</label>
                    <input type="text" id="username" name="username" required><br><br>
            
                    <label for="comment">Σχόλιο:</label>
                    <textarea id="comment" name="comment" rows="4" required></textarea><br><br>
                    <label for="rating">Βαθμολογία:</label>
                    <div class="rating-container">
                        <input type="radio" id="star1" name="rating" value="1" required>
                        <label for="star1">☆</label>
                        <input type="radio" id="star2" name="rating" value="2">
                        <label for="star2">☆</label>
                        <input type="radio" id="star3" name="rating" value="3">
                        <label for="star3">☆</label>
                        <input type="radio" id="star4" name="rating" value="4">
                        <label for="star4">☆</label>
                        <input type="radio" id="star5" name="rating" value="5">
                        <label for="star5">☆</label>
                    </div>
            
                    <button type="submit" name="submit_comment">Υποβολή Σχολίου</button>
                </form>
            </div>
        </div>
    </div>
<script>
document.getElementById('comment-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const username = document.getElementById('username').value;
    const comment = document.getElementById('comment').value;
    const rating = document.querySelector('input[name="rating"]:checked').value;

    const formData = {
        username: username,
        comment: comment,
        rating: parseInt(rating),
    };

    try {
        const response = await fetch('/submit_comment', {  // Αντικατάστησε με την σωστή διαδρομή του backend
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData),
        });

        if (response.ok) {
            alert('Το σχόλιό σας και η βαθμολογία καταχωρήθηκαν επιτυχώς!');
            document.getElementById('comment-form').reset();
        } else {
            alert('Σφάλμα κατά την υποβολή. Προσπαθήστε ξανά.');
        }
    } catch (error) {
        console.error('Σφάλμα:', error);
        alert('Σφάλμα δικτύου.');
    }
});
</script>
</body>
</html>

<?php
// Κλείσιμο της σύνδεσης
$conn->close();
?>
