<?php
include 'config.php'; // Σύνδεση με τη βάση δεδομένων

// Εκτέλεση της εντολής
$query = "SELECT * FROM YLIKO WHERE Plithos_Thermidon_per100g > 400 ORDER BY Plithos_Thermidon_per100g DESC";
$result = $conn->query($query);

$company_query = "SELECT * FROM YLIKO WHERE (Onomasia_Xorhgou = 'Company F' AND Kathgoria = 'Λαχανικά') OR Kathgoria = 'Δημητριακά'";
$result = $conn->query($company_query);
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Υλικά με Πολλές Θερμίδες</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #4CAF50;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 1em;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #dff0d8;
        }
        .container1 {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #4CAF50;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 1em;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #dff0d8;
        }
    </style>
</head>
<body>
<div class="container" id="yliko>400" style="display: none;">
    <h1>Υλικά με πάνω από 400 Θερμίδες ανά 100g</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Όνομα Υλικού</th>
                <th>Πλήθος Θερμίδων ανά 100g</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Onomasia_Ylikou']); ?></td>
                    <td><?php echo htmlspecialchars($row['Plithos_Thermidon_per100g']); ?> kcal</td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align: center;">Δεν βρέθηκαν υλικά με πάνω από 400 θερμίδες ανά 100g.</p>
    <?php endif; ?>
</div>
<div class="container1" id="company" style="display: none;">
    <h1>Υλικά από 'Company F' και Κατηγορίες 'Λαχανικά' ή 'Δημητριακά'</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Όνομα Υλικού</th>
                <th>Χορηγός</th>
                <th>Κατηγορία</th>
                <th>Πλήθος Θερμίδων ανά 100g</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Onomasia_Ylikou']); ?></td>
                    <td><?php echo htmlspecialchars($row['Onomasia_Xorhgou']); ?></td>
                    <td><?php echo htmlspecialchars($row['Kathgoria']); ?></td>
                    <td><?php echo htmlspecialchars($row['Plithos_Thermidon_per100g']); ?> kcal</td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align: center;">Δεν βρέθηκαν αποτελέσματα για την επιλεγμένη αναζήτηση.</p>
    <?php endif; ?>
</div>


<script>
    document.getElementById('yliko>400').addEventListener('click', function() {
        var formContainer = document.getElementById('container');
        if (formContainer.style.display === 'none' || formContainer.style.display === '') {
            formContainer.style.display = 'block';
        } else {
            formContainer.style.display = 'none';
        }
    });
</script>
<script>
    document.getElementById('company').addEventListener('click', function() {
        var formContainer = document.getElementById('container1');
        if (formContainer.style.display === 'none' || formContainer.style.display === '') {
            formContainer.style.display = 'block';
        } else {
            formContainer.style.display = 'none';
        }
    });
</script>
</body>
</html>

<?php
$conn->close(); // Κλείσιμο της σύνδεσης
?>
