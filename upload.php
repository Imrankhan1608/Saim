<?php
// ================= CONNEXION BD =================
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=saim;charset=utf8",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erreur connexion BD");
}

$message = "";

// ================= TRAITEMENT UPLOAD =================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $type   = $_POST['type']; // cours | exercice
    $titre  = $_POST['titre'];
    $niveau = $_POST['niveau'];
    $annee  = $_POST['annee'];

    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === 0) {

        $ext = strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION));

        if ($ext !== "pdf") {
            $message = "❌ Fichier non autorisé (PDF seulement)";
        } else {

            $nomFichier = time() . "_" . basename($_FILES['pdf']['name']);
            $destination = "uploads/" . $nomFichier;

            if (move_uploaded_file($_FILES['pdf']['tmp_name'], $destination)) {

                $table = ($type === "cours") ? "cours" : "exercices";

                $stmt = $pdo->prepare("
                    INSERT INTO $table (titre, niveau, annee, fichier_pdf)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$titre, $niveau, $annee, $nomFichier]);

                $message = "✅ PDF ajouté avec succès";
            } else {
                $message = "❌ Erreur upload";
            }
        }
    } else {
        $message = "❌ Aucun fichier sélectionné";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Insertion PDF</title>

<style>
body {
    font-family: Poppins, sans-serif;
    background: #f5f5f5;
}
.container {
    width: 400px;
    margin: 60px auto;
    background: white;
    padding: 25px;
    border-radius: 10px;
}
input, select, button {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
}
button {
    background: #222;
    color: white;
    border: none;
    cursor: pointer;
}
.msg {
    margin-top: 15px;
    text-align: center;
}
</style>
</head>

<body>

<div class="container">
    <h2>📥 Ajouter un PDF</h2>

    <?php if ($message): ?>
        <p class="msg"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Type</label>
        <select name="type" required>
            <option value="cours">Cours</option>
            <option value="exercice">Exercice</option>
        </select>

        <label>Titre</label>
        <input type="text" name="titre" required>

        <label>Niveau</label>
        <select name="niveau">
            <option value="l1">L1</option>
            <option value="l2">L2</option>
            <option value="l3">L3</option>
            <option value="m1">M1</option>
            <option value="m2">M2</option>
        </select>

        <label>Année scolaire</label>
        <select name="annee">
            <option value="2024_2025">2024-2025</option>
            <option value="2025_2026">2025-2026</option>
        </select>

        <label>Fichier PDF</label>
        <input type="file" name="pdf" accept="application/pdf" required>

        <button type="submit">Uploader</button>
    </form>
</div>

</body>
</html>
