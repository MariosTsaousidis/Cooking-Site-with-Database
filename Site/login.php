<?php
// Συμπερίληψη του αρχείου config.php για σύνδεση με τη βάση δεδομένων
include 'config.php';

// Έλεγχος αν η φόρμα έχει υποβληθεί
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Έλεγχος αν έχουν δοθεί οι παράμετροι email και password
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Έλεγχος των στοιχείων του χρήστη
        $stmt = $conn->prepare("SELECT * FROM XRHSTHS WHERE Email = ? AND Kwdikou_Prosbashs = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            // Αν ο χρήστης βρέθηκε, μεταφορά στην αρχική σελίδα
            header("Location: recipes.php");
            exit;
        } else {
            echo "Λανθασμένα στοιχεία.";
        }

        // Κλείσιμο του Prepared Statement
        $stmt->close();
    } else {
        echo "Παρακαλώ εισάγετε το email και τον κωδικό πρόσβασής σας.";
    }
}

// Κλείσιμο της σύνδεσης
$conn->close();
?>
