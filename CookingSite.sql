-- Δημιουργία της βάσης δεδομένων και χρήση αυτής
CREATE DATABASE CookingSite;
USE CookingSite;

-- Δημιουργία των πινάκων
CREATE TABLE SYNTAGH (
    ID_Syntaghs VARCHAR(10) PRIMARY KEY,
    Onoma_Syntaghs VARCHAR(70) NOT NULL,
    Xronos_Paragwgis INT NOT NULL,
    Perigrafh TEXT,
    Duskolia INT,
    Vathmologia_Syntaghs FLOAT(2, 1),
    ID_kathgorias CHAR(10)
);

DELIMITER $$

CREATE TRIGGER before_insert_my_table
BEFORE INSERT ON SYNTAGH
FOR EACH ROW
BEGIN
    DECLARE new_id INT;
    
    -- Απόκτηση του επόμενου AUTO_INCREMENT ID
    SELECT COALESCE(MAX(CAST(SUBSTRING(ID_Syntaghs, 4) AS UNSIGNED)), 0) + 1 INTO new_id FROM SYNTAGH;

    -- Δημιουργία του νέου custom_id με πρόθεμα 'ABC'
    SET NEW.ID_Syntaghs = CONCAT('SYN', LPAD(new_id, 3, '0'));
END$$

DELIMITER ;

CREATE TABLE YLIKO (
    ID_Ylikou CHAR(5) PRIMARY KEY,
    Onomasia_Ylikou VARCHAR(30) NOT NULL,
    Plithos_Thermidon_per100g DECIMAL(4, 1) NOT NULL,
    Kathgoria VARCHAR(20),
    Onomasia_Xorhgou VARCHAR(40)
);

CREATE TABLE XORHGOS (
    Onomasia_Xorhgou VARCHAR(40) PRIMARY KEY,
    Edra_Etaireias VARCHAR(20),
    Posothta_Ylikwn_Xorhgias INT,
    Tilefwno_Epikoinwnias VARCHAR(10)
);

CREATE TABLE KATHGORIA (
    ID_kathgorias CHAR(10) PRIMARY KEY,
    Onoma_kathgorias VARCHAR(30) NOT NULL
);

CREATE TABLE XRHSTHS (
    Onoma_Xrhsth VARCHAR(30) PRIMARY KEY,
    Email VARCHAR(40) NOT NULL,
    Kwdikou_Prosbashs VARCHAR(20) NOT NULL,
    Hmeromhnia_Eggrafhs DATE
);

CREATE TABLE SXOLIO (
    ID_Sxolio INT AUTO_INCREMENT PRIMARY KEY,
    Onoma_xrhsth VARCHAR(30),
    Keimeno TEXT NOT NULL,
    Hmeromhnia_Dhmosieyshs DATE,
    ID_Syntaghs CHAR(10)
);

CREATE TABLE VATHMOLOGIA (
    ID_Vathmologias INT AUTO_INCREMENT PRIMARY KEY,
    Arithmos_Vathmologias INT CHECK (Arithmos_Vathmologias BETWEEN 1 AND 5),
    Hmeromhnia_Vathmologias DATE,
    Onoma_Xrhsth VARCHAR(30),
    ID_Syntaghs CHAR(10)
);

CREATE TABLE SYNTAGH_YLIKO (
    ID_Syntaghs VARCHAR(20),
    ID_Ylikou VARCHAR(20),
    Posotita INT,
    PRIMARY KEY (ID_Syntaghs, ID_Ylikou),
    FOREIGN KEY (ID_Syntaghs) REFERENCES SYNTAGH(ID_Syntaghs),
    FOREIGN KEY (ID_Ylikou) REFERENCES YLIKO(ID_Ylikou)
);

-- Προσθήκη των foreign keys και των unique constraints
ALTER TABLE SYNTAGH
    ADD CONSTRAINT fk_syntagh_kathgoria FOREIGN KEY (ID_kathgorias) REFERENCES KATHGORIA(ID_kathgorias);

ALTER TABLE YLIKO
    ADD CONSTRAINT unique_Onomasia_Ylikou UNIQUE (Onomasia_Ylikou);
ALTER TABLE YLIKO
	ADD CONSTRAINT fk_onomasia_xorhgou FOREIGN KEY (Onomasia_Xorhgou) REFERENCES XORHGOS(Onomasia_Xorhgou);

ALTER TABLE SXOLIO
    ADD CONSTRAINT fk_sxolio_syntagh FOREIGN KEY (ID_Syntaghs) REFERENCES SYNTAGH(ID_Syntaghs);
ALTER TABLE SXOLIO
	ADD CONSTRAINT fk_Sxolio_Xrhsths FOREIGN KEY (Onoma_Xrhsth) REFERENCES XRHSTHS(Onoma_Xrhsth);


ALTER TABLE VATHMOLOGIA
    ADD CONSTRAINT fk_vathmologia_xrhsths FOREIGN KEY (Onoma_Xrhsth) REFERENCES XRHSTHS(Onoma_Xrhsth),
    ADD CONSTRAINT fk_vathmologia_syntagh FOREIGN KEY (ID_Syntaghs) REFERENCES SYNTAGH(ID_Syntaghs);

ALTER TABLE XRHSTHS
    ADD CONSTRAINT unique_Email UNIQUE (Email);
ALTER TABLE XRHSTHS
    ADD CONSTRAINT unique_Onoma_Xrhsth UNIQUE (Onoma_Xrhsth);
    
-- Δημιουργία Trigger για αυτόματο υπολογισμό του μέσου όρου βαθμολογίας
DELIMITER //

CREATE TRIGGER update_average_rating
AFTER INSERT ON VATHMOLOGIA
FOR EACH ROW
BEGIN
    DECLARE avg_rating FLOAT;

    -- Υπολογισμός μέσου όρου βαθμολογιών για τη συγκεκριμένη συνταγή
    SELECT AVG(Arithmos_Vathmologias) INTO avg_rating
    FROM VATHMOLOGIA
    WHERE ID_Syntaghs = NEW.ID_Syntaghs;

    -- Ενημέρωση του πεδίου Vathmologia_Syntaghs στον πίνακα SYNTAGH
    UPDATE SYNTAGH
    SET Vathmologia_Syntaghs = avg_rating
    WHERE ID_Syntaghs = NEW.ID_Syntaghs;
END //

CREATE TRIGGER update_average_rating_after_update
AFTER UPDATE ON VATHMOLOGIA
FOR EACH ROW
BEGIN
    DECLARE avg_rating FLOAT;

    -- Υπολογισμός μέσου όρου βαθμολογιών για τη συγκεκριμένη συνταγή
    SELECT AVG(Arithmos_Vathmologias) INTO avg_rating
    FROM VATHMOLOGIA
    WHERE ID_Syntaghs = NEW.ID_Syntaghs;

    -- Ενημέρωση του πεδίου Vathmologia_Syntaghs στον πίνακα SYNTAGH
    UPDATE SYNTAGH
    SET Vathmologia_Syntaghs = avg_rating
    WHERE ID_Syntaghs = NEW.ID_Syntaghs;
END //



DELIMITER ;

-- Εισαγωγή δεδομένων στους πίνακες
INSERT INTO KATHGORIA (ID_kathgorias, Onoma_kathgorias) VALUES
('C1', 'Σαλάτες'),
('C2', 'Γλυκά'),
('C3', 'Κυρίως Πιάτα'),
('C4', 'Ορεκτικά'),
('C5', 'Σούπες'),
('C6', 'Επιδόρπια'),
('C7', 'Σνακς');

INSERT INTO SYNTAGH (ID_Syntaghs, Onoma_Syntaghs, Xronos_Paragwgis, Perigrafh, Duskolia, Vathmologia_Syntaghs, ID_kathgorias) VALUES
('SYN001', 'Χωριάτικη Σαλάτα', 10, 'Παραδοσιακή ελληνική σαλάτα.', 2, NULL, 'C1'),
('SYN002', 'Μους Σοκολάτας', 20, 'Ελαφρύ γλυκό με σοκολάτα.', 3, NULL, 'C2'),
('SYN003', 'Μακαρόνια με Κιμά', 40, 'Κλασικό ιταλικό πιάτο.', 4, NULL, 'C3'),
('SYN004', 'Κρητικός Ντάκος', 15, 'Παραδοσιακό κρητικό ορεκτικό με παξιμάδι και ντομάτα.', 2, NULL, 'C4'),
('SYN005', 'Σούπα Λαχανικών', 30, 'Υγιεινή σούπα με ποικιλία λαχανικών.', 1, NULL, 'C5'),
('SYN006', 'Τιραμισού', 25, 'Ιταλικό γλυκό με κρέμα και καφέ.', 3, NULL, 'C6'),
('SYN007', 'Σπανακόπιτα', 50, 'Ελληνική πίτα με σπανάκι και φέτα.', 4, NULL, 'C4'),
('SYN008', 'Παστίτσιο', 60, 'Παραδοσιακό ελληνικό πιάτο με κιμά και μακαρόνια.', 4, NULL, 'C3'),
('SYN009', 'Κέικ Καρότου', 50, 'Αφράτο κέικ με γεύση καρότου και γλάσο.', 3, NULL, 'C2'),
('SYN010', 'Σαλάτα Ρόκα-Παρμεζάνα', 15, 'Φρέσκια σαλάτα με ρόκα και παρμεζάνα.', 2, NULL, 'C1'),
('SYN011', 'Γαλακτομπούρεκο', 90, 'Παραδοσιακό ελληνικό γλυκό με κρέμα και φύλλο.', 5, NULL, 'C6'),
('SYN012', 'Κεφτεδάκια με Πουρέ', 45, 'Τηγανητά κεφτεδάκια σερβιρισμένα με πουρέ πατάτας.', 3, NULL, 'C3'),
('SYN013', 'Κοτόπουλο με Πουρέ', 45, 'Μπουτάκια κοτόπουλο στον φούρνο με πουρέ πατάτας.', 3, NULL, 'C3');



INSERT INTO XORHGOS (Onomasia_Xorhgou, Edra_Etaireias, Posothta_Ylikwn_Xorhgias, Tilefwno_Epikoinwnias) VALUES
('Company A', 'Αθήνα', 1, '2101234567'),
('Company B', 'Θεσσαλονίκη', 1, '2310123456'),
('Company C', 'Πάτρα', 1, '2610123456'),
('Company D', 'Ηράκλειο', 1, '2810123456'),
('Company E', 'Χανιά', 1, '2821023456'),
('Company F', 'Βόλος', 3, '2421023456'),
('Company G', 'Λαμία', 2, '2231023456'),
('Company H', 'Κέρκυρα', 2, '2661023456'),
('Company I', 'Σπάρτη', 1, '2731023456'),
('Company J', 'Λάρισα', 1, '2410123456'),
('Company K', 'Καλαμάτα', 1, '2721023456'),
('Company L', 'Κομοτηνή', 1, '2531023456');



INSERT INTO YLIKO (ID_Ylikou, Onomasia_Ylikou, Plithos_Thermidon_per100g, Kathgoria, Onomasia_Xorhgou) VALUES
('Y001', 'Ντομάτα', 18.0, 'Λαχανικά', 'Company A'),
('Y002', 'Ελαιόλαδο', 884.0, 'Λίπη', 'Company B'),
('Y003', 'Σοκολάτα', 546.0, 'Γλυκά', 'Company C'),
('Y004', 'Φέτα', 264.0, 'Γαλακτοκομικά', 'Company D'),
('Y005', 'Σπανάκι', 23.0, 'Λαχανικά', 'Company E'),
('Y006', 'Μπισκότα', 475.0, 'Γλυκά', 'Company G'),
('Y007', 'Καφές', 2.0, 'Ροφήματα', 'Company I'),
('Y008', 'Καρότο', 41.0, 'Λαχανικά', 'Company F'),
('Y009', 'Ζάχαρη', 387.0, 'Γλυκά', 'Company G'),
('Y010', 'Ρόκα', 25.0, 'Λαχανικά', 'Company F'),
('Y011', 'Φύλλο Κρούστας', 334.0, 'Δημητριακά', 'Company H'),
('Y012', 'Πατάτα', 77.0, 'Λαχανικά', 'Company F'),
('Y013', 'Κιμάς', 250.0, 'Κρέας', 'Company J'),
('Y014', 'Μακαρόνια', 350.0, 'Δημητριακά', 'Company H'),
('Y015', 'Τυρί Παρμεζάνα', 431.0, 'Γαλακτοκομικά', 'Company K'),
('Y016', 'Αυγά', 155.0, 'Πρωτεΐνες', 'Company L');




INSERT INTO XRHSTHS (Onoma_Xrhsth, Email, Kwdikou_Prosbashs, Hmeromhnia_Eggrafhs) VALUES
('User1', 'user1@example.com', 'pass123', '2022-01-01'),
('User2', 'user2@example.com', 'pass456', '2022-02-01'),
('User3', 'user3@example.com', 'pass789', '2022-03-01'),
('User4', 'user4@example.com', 'pass001', '2022-04-01'),
('User5', 'user5@example.com', 'pass002', '2022-05-01'),
('User6', 'user6@example.com', 'pass003', '2022-06-01'),
('User7', 'user7@example.com', 'pass004', '2022-07-01'),
('User8', 'user8@example.com', 'pass005', '2022-08-01'),
('User9', 'user9@example.com', 'pass006', '2022-09-01'),
('User10', 'user10@example.com', 'pass007', '2022-10-01'),
('User11', 'user11@example.com', 'pass008', '2022-11-01'),
('User12', 'user12@example.com', 'pass009', '2022-12-01'),
('User13', 'user13@example.com', 'pass010', '2023-01-01'),
('User14', 'user14@example.com', 'pass011', '2023-02-01'),
('User15', 'user15@example.com', 'pass012', '2023-03-01'),
('User16', 'user16@example.com', 'pass013', '2023-04-01'),
('User17', 'user17@example.com', 'pass014', '2023-05-01'),
('User18', 'user18@example.com', 'pass015', '2023-06-01');
	


INSERT INTO SXOLIO (Keimeno, Hmeromhnia_Dhmosieyshs, ID_Syntaghs,Onoma_Xrhsth) VALUES
('Πολύ νόστιμο!', '2022-01-05', 'SYN001','User1'),
('Αρκετά γλυκό για μένα.', '2022-01-06', 'SYN002','User2'),
('Απλό και χορταστικό!', '2022-01-07', 'SYN003','User3'),
('Εξαιρετική συνταγή!', '2022-02-05', 'SYN004','User4'),
('Πολύ υγιεινή επιλογή.', '2022-02-06', 'SYN005','User5'),
('Υπέροχη γεύση, το συνιστώ.', '2022-02-07', 'SYN006','User6'),
('Πολύ νόστιμη πίτα.', '2022-02-08', 'SYN007','User7'),
('Μου άρεσε πολύ η απλότητα της συνταγής.', '2023-05-01', 'SYN001', 'User1'),
('Θα το ξαναφτιάξω, ήταν πολύ νόστιμο!', '2023-05-05', 'SYN002', 'User2'),
('Πολύ εύκολο και γρήγορο!', '2023-05-10', 'SYN003', 'User3'),
('Χρειάζεται λίγο περισσότερη αλάτι.', '2023-05-15', 'SYN005', 'User4'),
('Υπέροχο πιάτο για παρέα!', '2023-05-20', 'SYN007', 'User5'),
('Η σοκολάτα ταιριάζει απόλυτα εδώ.', '2023-05-25', 'SYN006', 'User6'),
('Απλό αλλά πολύ νόστιμο!', '2023-05-30', 'SYN001', 'User7'),
('Εντυπωσιακό αποτέλεσμα!', '2023-06-01', 'SYN004', 'User1'),
('Πολύ ωραία εμπειρία στην κουζίνα!', '2023-06-05', 'SYN003', 'User3'),
('Θα το ξαναφτιάξω, πολύ γευστικό.', '2023-06-10', 'SYN006', 'User2');



INSERT INTO VATHMOLOGIA (Arithmos_Vathmologias, Hmeromhnia_Vathmologias, Onoma_Xrhsth, ID_Syntaghs) VALUES
(5, '2022-01-05', 'User1', 'SYN001'),
(3, '2022-01-06', 'User2', 'SYN002'),
(4, '2022-01-07', 'User3', 'SYN003'),
(4, '2022-01-08', 'User1', 'SYN001'),
(2, '2022-01-09', 'User2', 'SYN002'),
(5, '2022-02-05', 'User4', 'SYN004'),
(3, '2022-02-05', 'User5', 'SYN004'),
(4, '2022-02-06', 'User5', 'SYN005'),
(5, '2022-02-07', 'User6', 'SYN006'),
(3, '2022-02-08', 'User7', 'SYN007'),
(4, '2023-05-01', 'User1', 'SYN001'),
(3, '2023-05-05', 'User2', 'SYN002'),
(5, '2023-05-10', 'User3', 'SYN003'),
(2, '2023-05-15', 'User4', 'SYN005'),
(4, '2023-05-20', 'User5', 'SYN007'),
(5, '2023-05-25', 'User6', 'SYN006'),
(4, '2023-05-30', 'User7', 'SYN001'),
(5, '2023-06-01', 'User1', 'SYN004'),
(3, '2023-06-05', 'User3', 'SYN003'),
(5, '2023-06-10', 'User2', 'SYN006');

-- Χωριάτικη Σαλάτα
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN001', 'Y001', 3),  -- Ντομάτα
('SYN001', 'Y002', 10), -- Ελαιόλαδο
('SYN001', 'Y004', 100); -- Φέτα

-- Μους Σοκολάτας
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN002', 'Y003', 150), -- Σοκολάτα
('SYN002', 'Y009', 50),  -- Ζάχαρη
('SYN002', 'Y016', 2);   -- Αυγά

-- Μακαρόνια με Κιμά
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN003', 'Y013', 300), -- Κιμάς
('SYN003', 'Y014', 200), -- Μακαρόνια
('SYN003', 'Y002', 5);   -- Ελαιόλαδο

-- Κρητικός Ντάκος
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN004', 'Y001', 2),  -- Ντομάτες
('SYN004', 'Y002', 10), -- Ελαιόλαδο
('SYN004', 'Y004', 50); -- Φέτα

-- Σούπα Λαχανικών
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN005', 'Y001', 1),  -- Ντομάτα
('SYN005', 'Y008', 2),  -- Καρότα
('SYN005', 'Y012', 3);  -- Πατάτες

-- Τιραμισού
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN006', 'Y006', 150), -- Μπισκότα
('SYN006', 'Y007', 20),  -- Καφές
('SYN006', 'Y009', 80);  -- Ζάχαρη

-- Σπανακόπιτα
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN007', 'Y005', 300),  -- Σπανάκι
('SYN007', 'Y004', 100),  -- Φέτα
('SYN007', 'Y011', 200);  -- Φύλλο Κρούστας

-- Παστίτσιο
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN008', 'Y013', 400), -- Κιμάς
('SYN008', 'Y014', 300), -- Μακαρόνια
('SYN008', 'Y016', 2);   -- Αυγά

-- Κέικ Καρότου
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN009', 'Y008', 3),   -- Καρότα
('SYN009', 'Y009', 100), -- Ζάχαρη
('SYN009', 'Y011', 200); -- Φύλλο Κρούστας

-- Σαλάτα Ρόκα-Παρμεζάνα
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN010', 'Y010', 50),  -- Ρόκα
('SYN010', 'Y002', 5),   -- Ελαιόλαδο
('SYN010', 'Y015', 30);  -- Παρμεζάνα

-- Γαλακτομπούρεκο
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN011', 'Y011', 250),  -- Φύλλο Κρούστας
('SYN011', 'Y009', 150);  -- Ζάχαρη

-- Κεφτεδάκια με Πουρέ
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN012', 'Y012', 300),  -- Πατάτες
('SYN012', 'Y002', 10),   -- Ελαιόλαδο
('SYN012', 'Y013', 500);   -- Κιμά

-- Κοτόπουλο με Πουρέ
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN013', 'Y012', 300),  -- Πατάτες
('SYN013', 'Y002', 10);   -- Ελαιόλαδο


-- Κεφτεδάκια με Πουρέ
INSERT INTO SYNTAGH_YLIKO (ID_Syntaghs, ID_Ylikou, Posotita) VALUES
('SYN014', 'Y014', 300),  -- Μακαρόνια
('SYN014', 'Y002', 10),   -- Ελαιόλαδο
('SYN014', 'Y013', 500);   -- Κιμά

SELECT * FROM SYNTAGH_YLIKO;
SELECT * FROM YLIKO;
SELECT * FROM SYNTAGH;
SELECT * FROM SXOLIO;
SELECT * FROM KATHGORIA;
SELECT * FROM XRHSTHS;
SELECT 
    X.Onoma_Xrhsth AS 'Όνομα Χρήστη',
    S.Keimeno AS 'Κείμενο Σχολίου',
    S.Hmeromhnia_Dhmosieyshs AS 'Ημερομηνία Δημοσίευσης'
FROM 
    SXOLIO S
JOIN 
    XRHSTHS X ON S.Onoma_Xrhsth = X.Onoma_Xrhsth
ORDER BY 
    S.Hmeromhnia_Dhmosieyshs DESC;

SELECT 
    S.ID_Syntaghs AS ID_Συνταγής,
    S.Onoma_Syntaghs AS Όνομα_Συνταγής,
    S.Xronos_Paragwgis AS Χρόνος_Παραγωγής,
    S.Perigrafh AS Περιγραφή,
    S.Duskolia AS Δυσκολία,
    S.Vathmologia_Syntaghs AS Βαθμολογία_Συνταγής,
    K.Onoma_kathgorias AS Κατηγορία_Συνταγής,
    V.Arithmos_Vathmologias AS Βαθμολογία,
    V.Hmeromhnia_Vathmologias AS Ημερομηνία_Βαθμολογίας,
    V.Onoma_Xrhsth AS Χρήστης_Βαθμολογίας,
    X.Keimeno AS Σχόλιο,
    X.Hmeromhnia_Dhmosieyshs AS Ημερομηνία_Σχολίου,
    X.Onoma_Xrhsth AS Χρήστης_Σχολίου
FROM 
    SYNTAGH S
LEFT JOIN 
    KATHGORIA K ON S.ID_kathgorias = K.ID_kathgorias
LEFT JOIN 
    VATHMOLOGIA V ON S.ID_Syntaghs = V.ID_Syntaghs
LEFT JOIN 
    SXOLIO X ON S.ID_Syntaghs = X.ID_Syntaghs
ORDER BY 
    S.ID_Syntaghs, V.Hmeromhnia_Vathmologias, X.Hmeromhnia_Dhmosieyshs;
SELECT 
    KATHGORIA.Onoma_kathgorias AS Κατηγορία,
    SYNTAGH.Onoma_Syntaghs AS Συνταγή
FROM KATHGORIA JOIN SYNTAGH ON KATHGORIA.ID_kathgorias = SYNTAGH.ID_kathgorias ORDER BY Κατηγορία;

SELECT SXOLIO.ID_Sxolio, SXOLIO.Keimeno, SXOLIO.Hmeromhnia_Dhmosieyshs, XRHSTHS.Onoma_Xrhsth, SYNTAGH.Onoma_Syntaghs
FROM SXOLIO
LEFT JOIN XRHSTHS ON SXOLIO.Onoma_Xrhsth = XRHSTHS.Onoma_Xrhsth
LEFT JOIN SYNTAGH ON SXOLIO.ID_Syntaghs = SYNTAGH.ID_Syntaghs
ORDER BY SXOLIO.Hmeromhnia_Dhmosieyshs DESC;


SELECT Onoma_Syntaghs, Vathmologia_Syntaghs
FROM SYNTAGH
ORDER BY Vathmologia_Syntaghs DESC;

SELECT Y.Onomasia_Ylikou, SY.Posotita
FROM SYNTAGH_YLIKO SY
JOIN YLIKO Y ON SY.ID_Ylikou = Y.ID_Ylikou
WHERE SY.ID_Syntaghs = 'SYN001';

SELECT Onoma_Syntaghs, Vathmologia_Syntaghs
FROM SYNTAGH
ORDER BY Vathmologia_Syntaghs DESC
LIMIT 3;

SELECT SX.Keimeno, XR.Onoma_Xrhsth, SX.Hmeromhnia_Dhmosieyshs
FROM SXOLIO SX
JOIN XRHSTHS XR ON SX.Onoma_Xrhsth = XR.Onoma_Xrhsth
WHERE SX.ID_Syntaghs = 'SYN002'
ORDER BY SX.Hmeromhnia_Dhmosieyshs DESC;

SELECT Y.Onomasia_Ylikou, Y.Kathgoria, Y.Plithos_Thermidon_per100g
FROM YLIKO Y
WHERE Y.Onomasia_Xorhgou = 'Company F';

-- Παράδειγμα ερωτημάτων με συνθήκες
-- 4.a. Χρήση του τελεστή "LIKE"
SELECT * FROM SYNTAGH WHERE ID_Kathgorias LIKE '%C1%';

-- 4.b. Φιλτράρισμα και ταξινόμηση
SELECT * FROM YLIKO WHERE Plithos_Thermidon_per100g > 100 ORDER BY Plithos_Thermidon_per100g DESC;

-- 4.c. Χρήση λογικών τελεστών "AND", "OR", "NOT"
SELECT * FROM YLIKO WHERE (Onomasia_Xorhgou = 'Company F' AND Kathgoria = 'Λαχανικά') OR Kathgoria = 'Δημητριακά';

-- Παράδειγμα ερωτημάτων με συναρτησιακές συναρτήσεις
-- 5.a. Χρήση COUNT και GROUP BY
SELECT Onoma_Xrhsth, COUNT(*) as 'Πληθος σχολιων' FROM SXOLIO GROUP BY Onoma_Xrhsth;

-- 5.b. Χρήση AVG και SUM
SELECT AVG(Arithmos_Vathmologias), SUM(Arithmos_Vathmologias), ID_Syntaghs FROM VATHMOLOGIA GROUP BY ID_Syntaghs;

-- Παράδειγμα Join
-- 6.a. Χρήση INNER JOIN
SELECT XRHSTHS.Onoma_Xrhsth, VATHMOLOGIA.Arithmos_Vathmologias, VATHMOLOGIA.Hmeromhnia_Vathmologias, VATHMOLOGIA.ID_Syntaghs
FROM XRHSTHS
INNER JOIN VATHMOLOGIA ON XRHSTHS.Onoma_Xrhsth = VATHMOLOGIA.Onoma_Xrhsth
ORDER BY Arithmos_Vathmologias ASC;

-- 6.b. Χρήση LEFT JOIN
SELECT XRHSTHS.Onoma_Xrhsth, VATHMOLOGIA.Arithmos_Vathmologias, VATHMOLOGIA.Hmeromhnia_Vathmologias, VATHMOLOGIA.ID_Syntaghs
FROM XRHSTHS
LEFT JOIN VATHMOLOGIA ON XRHSTHS.Onoma_Xrhsth = VATHMOLOGIA.Onoma_Xrhsth
ORDER BY Arithmos_Vathmologias DESC;

-- 7. Παράδειγμα δημιουργίας View
CREATE VIEW Όνομα_View AS
SELECT XRHSTHS.Onoma_Xrhsth, SXOLIO.Keimeno, VATHMOLOGIA.Arithmos_Vathmologias, VATHMOLOGIA.ID_Syntaghs
FROM XRHSTHS
JOIN SXOLIO ON XRHSTHS.Onoma_Xrhsth = SXOLIO.Onoma_Xrhsth
JOIN VATHMOLOGIA ON SXOLIO.ID_Syntaghs = VATHMOLOGIA.ID_Syntaghs;

SELECT * FROM Όνομα_View GROUP BY Keimeno ORDER BY Onoma_Xrhsth;

-- 8. Παράδειγμα δημιουργίας Procedure
DELIMITER //

CREATE PROCEDURE Όνομα_Procedure()
BEGIN
    SELECT * FROM YLIKO WHERE Onomasia_Xorhgou = 'Company F';
END //

DELIMITER //

CALL Όνομα_Procedure;