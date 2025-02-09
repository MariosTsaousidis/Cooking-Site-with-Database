<?php
// Συμπερίληψη του αρχείου config.php για σύνδεση με τη βάση δεδομένων
include 'config.php';

// Έλεγχος αν η φόρμα έχει υποβληθεί
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Έλεγχος αν έχουν δοθεί οι παράμετροι username, email και password
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Έλεγχος αν ο χρήστης ήδη υπάρχει στη βάση δεδομένων με το ίδιο email
        $check_stmt = $conn->prepare("SELECT * FROM XRHSTHS WHERE Email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result && $check_result->num_rows > 0) {
            echo "Υπάρχει ήδη λογαριασμός με αυτό το email.";
        } else {
            // Εισαγωγή του νέου χρήστη στη βάση δεδομένων
            // Χρήση του password_hash για την αποθήκευση του κωδικού
            //$hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Εισαγωγή του χρήστη με την τρέχουσα ημερομηνία εγγραφής
            $stmt = $conn->prepare("INSERT INTO XRHSTHS (Onoma_Xrhsth, Email, Kwdikou_Prosbashs, Hmeromhnia_Eggrafhs) VALUES (?, ?, ?, CURDATE())");
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                echo "Η εγγραφή ήταν επιτυχής. Μπορείτε να συνδεθείτε τώρα.";
                // Μεταφορά στη σελίδα σύνδεσης
                header("Location: index.html");
                exit;
            } else {
                echo "Σφάλμα κατά την εγγραφή.";
            }

            // Κλείσιμο του Prepared Statement
            $stmt->close();
        }

        // Κλείσιμο του Prepared Statement για έλεγχο χρήστη
        $check_stmt->close();
    } else {
        echo "Παρακαλώ εισάγετε το όνομα χρήστη, το email και τον κωδικό πρόσβασής σας.";
    }
}

// Κλείσιμο της σύνδεσης
$conn->close();
?>
