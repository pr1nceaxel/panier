<?php 
session_start();
include_once "con_dbb.php";

// Supprimer les produits
if(isset($_GET['del'])){
    $id_del = $_GET['del'];
    // Réduire la quantité de l'article
    if(isset($_SESSION['panier'][$id_del])){
        $_SESSION['panier'][$id_del]--;
        // Si la quantité est zéro, on le retire du panier
        if($_SESSION['panier'][$id_del] == 0){
            unset($_SESSION['panier'][$id_del]);
        }
    }
    // Rediriger pour éviter la suppression continue sur actualisation
    header("Location: panier.php");
    exit();
}

// Vider le panier
if(isset($_POST['vider'])){
    unset($_SESSION['panier']);
    header("Location: panier.php");
    exit();
}

// Appliquer la remise
$remise = isset($_POST['remise']) ? $_POST['remise'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="panier">
    <a href="index.php" class="link">Retour</a>
    <section>

    <div class="flex  justify-between" >
        <!-- Formulaire remise -->
        <form method="POST" action="">
            <label for="remise">Remise (%): </label>
            <input type="number" id="remise" name="remise" value="<?= $remise ?>" min="0" max="100">
            <button type="submit" class="bg-gray-300 rounded p-2 mx-4">Appliquer Remise</button>
        </form>

        <!-- Bouton pour vider le panier -->
        <form method="post" action="">
            <input type="hidden" name="vider" value="1">
            <button type="submit" class="bg-red-500 mx-4 rounded p-2  ">Vider le panier</button>
        </form>
    </div>
        <table>
            <tr>
                <th></th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Action</th>
            </tr>
            <?php 
            $total = 0;
            $ids = isset($_SESSION['panier']) ? array_keys($_SESSION['panier']) : [];
            if(empty($ids)){
                echo "<tr><td colspan='5'>Votre panier est vide</td></tr>";
            } else {
                $products = mysqli_query($con, "SELECT * FROM products WHERE id IN (".implode(',', $ids).")");

                foreach($products as $product):
                    $total += $product['price'] * $_SESSION['panier'][$product['id']];
            ?>
            <tr>
                <td><img src="project_images/<?=$product['img']?>"></td>
                <td><?=$product['name']?></td>
                <td><?=$product['price']?> Fcfa</td>
                <td><?=$_SESSION['panier'][$product['id']] // Quantité?></td>
                <td>
                    <form method="GET" action="">
                        <input type="hidden" name="del" value="<?=$product['id']?>">
                        <button type="submit"><img src="delete.png" alt="Supprimer"></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; } ?>

            <?php 
            // Calcul du total avec remise
            $total_avec_remise = $total - ($total * ($remise / 100));
            ?>
            <tr class="total">
                <th colspan="5">Total : <?=$total_avec_remise?> Fcfa</th>
            </tr>
        </table>
        <form method="POST" action="generer_pdf.php" id="saveOrderForm">
            <input type="hidden" name="total" value="<?=$total_avec_remise?>">
            <input type="hidden" name="remise" value="<?=$remise?>">
            <button type="submit" class="bg-green-500 my-6 rounded p-4">Enregistrer la commande</button>
        </form>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        document.getElementById('saveOrderForm').addEventListener('submit', function(e) {
            // Vérifier si le panier est vide
            var panierVide = <?= empty($ids) ? 'true' : 'false' ?>;
            if (panierVide) {
                e.preventDefault(); 
                alert('Votre panier est vide. Vous ne pouvez pas enregistrer une commande vide.');
            }
        });
    });
    </script>
</body>
</html>
