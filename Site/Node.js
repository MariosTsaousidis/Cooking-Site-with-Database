app.post('/submit_comment', (req, res) => {
    const { username, comment, rating } = req.body;

    // Ξεκινάμε με την εισαγωγή του σχολίου στον πίνακα SXOLIO
    db.query(
        'INSERT INTO SXOLIO (username, comment) VALUES (?, ?)',
        [username, comment],
        (error, result) => {
            if (error) {
                console.error('Σφάλμα κατά την εισαγωγή στο SXOLIO:', error);
                res.status(500).send('Σφάλμα διακομιστή');
                return;
            }

            // Αποθηκεύουμε το ID του σχολίου για να το συνδέσουμε με τη βαθμολογία
            const sxolioId = result.insertId;

            // Εισαγωγή της βαθμολογίας στον πίνακα Vatmologia
            db.query(
                'INSERT INTO Vatmologia (sxolio_id, rating) VALUES (?, ?)',
                [sxolioId, rating],
                (error) => {
                    if (error) {
                        console.error('Σφάλμα κατά την εισαγωγή στο Vatmologia:', error);
                        res.status(500).send('Σφάλμα διακομιστή');
                        return;
                    }

                    res.status(200).send('Το σχόλιο και η βαθμολογία καταχωρήθηκαν επιτυχώς!');
                }
            );
        }
    );
});