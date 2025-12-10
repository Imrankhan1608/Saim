<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
// verif isset pour l'inscription
if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['matricule']) && isset($_POST['pass']) && isset($_POST['niveau'])) {
    // variables //
$user = 'root';
$pass = '';
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$niveau = $_POST['niveau'];
$matricule = $_POST['matricule'];
$passe = $_POST['pass'];

    try {
        $stmt = new PDO('mysql:host=localhost;dbname=saim','$user','$pass');
        $req = $stmt->prepare("INSERT INTO eleves (nom,prenom,niveau,matricule,pass) VALUES ('$nom',
        '$prenom','$niveau','$matricule','$passe')");
        $req->execute();
        echo"enregistrement reussi !"."</br>";
    }
    catch (PDOException $e){
     echo"Erreur".$e->getMessage()."</br>";
    }
    die(
        header("location:erreur.html")
    );

// pour les connexion //





}









?>




<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saim - Connexion / Inscription</title>
    <link rel="stylesheet" href="index.css">
    <style>
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        /* Conteneur principal centré */
        .conteneur-auth {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 20px 80px;
        }

        /* Carte du formulaire */
        .carte-auth {
            background: linear-gradient(to right, #e0e0e0, #ffffff);
            padding: 50px 40px;
            border-radius: 12px;
            border: 2px solid #000;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .carte-auth h1 {
            font-family: 'Bell MT', serif;
            font-size: 3.2rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
            color: #000;
        }

        /* Texte de bascule */
        .texte-bascule {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 1.1rem;
        }

        .lien-bascule {
            color: #000;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
        }

        /* Groupe de champ */
        .groupe-champ {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #111;
        }

        input,
        select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #000;
            border-radius: 8px;
            font-size: 1rem;
            background: transparent;
            color: #000;
        }

        input:focus,
        select:focus {
            outline: none;
            background: rgba(0, 0, 0, 0.03);
        }

        /* Caché */
        .caché {
            display: none;
        }

        /* Bouton pleine largeur */
        .btn-plein {
            width: 100%;
            margin-top: 10px;
            text-align: center;
        }

        /* Retour accueil */
        .retour-accueil {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #000;
            font-weight: 600;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <nav class="barre_navigation" id="nav_bar">
        <div class="logo">Saim</div>
        <div class="menu_navigation" id="menu">
            <a href="acceuil.html">Accueil</a>
            <a href="#filtre">Filtre</a>
            <a href="#apropos">À propos</a>
        </div>
        <div class="bouton_menu" id="bouton_menu">
            <span class="barre"></span>
            <span class="barre"></span>
            <span class="barre"></span>
        </div>
    </nav>

    <div class="conteneur-auth">
        <div class="carte-auth">

            <h1 id="titre-formulaire">Connexion</h1>
            <p class="texte-bascule" id="texte-bascule">
                Pas encore de compte ? <span class="lien-bascule" id="lien-bascule">S’inscrire</span>
            </p>

            <form id="formulaire-auth" method="post">

                <div class="groupe-champ">
                    <label>Numéro matricule</label>
                    <input type="text" required name="matricule">
                </div>

                <div class="groupe-champ">
                    <label>Mot de passe</label>
                    <input type="password" required name="pass">
                </div>

                <!-- Champs supplémentaires pour l'inscription -->
                <div id="champs-inscription" class="caché">
                    <div class="groupe-champ">
                        <label>Nom</label>
                        <input type="text" name="nom">
                    </div>
                    <div class="groupe-champ">
                        <label>Prénom</label>
                        <input type="text" name="prenom">
                    </div>
                    <div class="groupe-champ">
                        <label>Niveau</label>
                        <select name="Niveau">
                            <option value="L1">L1 Informatique</option>
                            <option value="L2">L2 Informatique</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-plein" id="bouton-soumettre">
                    Se connecter
                </button>
            </form>

            <a href="acceuil.html" class="retour-accueil">← Retour à l’accueil</a>
        </div>
    </div>

    <script src="index.js"></script>
    <script>
        // Sélection des éléments (en français)
        const titre = document.getElementById('titre-formulaire');
        const texteBascule = document.getElementById('texte-bascule');
        const champsExtra = document.getElementById('champs-inscription');
        const boutonSubmit = document.getElementById('bouton-soumettre');

        function basculerMode() {
            const estConnexion = titre.textContent === "Connexion";

            if (estConnexion) {
                // Passe en mode Inscription
                titre.textContent = "Inscription";
                texteBascule.innerHTML = 'Déjà un compte ? <span class="lien-bascule">Se connecter</span>';
                champsExtra.classList.remove('caché');
                boutonSubmit.textContent = "S’inscrire";
            } else {
                // Passe en mode Connexion
                titre.textContent = "Connexion";
                texteBascule.innerHTML = 'Pas encore de compte ? <span class="lien-bascule">S’inscrire</span>';
                champsExtra.classList.add('caché');
                boutonSubmit.textContent = "Se connecter";
            }

            // Ré-attache l'écouteur au nouveau lien créé
            document.querySelector('.lien-bascule').addEventListener('click', basculerMode);
        }

        // Écouteur initial
        document.getElementById('lien-bascule').addEventListener('click', basculerMode);
    </script>
</body>

</html>