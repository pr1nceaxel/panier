<?php
require('fpdf186/fpdf.php');
session_start();
include_once "con_dbb.php"; // Assurez-vous que ce fichier est correct et qu'il établit une connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total = isset($_POST['total']) ? $_POST['total'] : 0;
    $remise = isset($_POST['remise']) ? $_POST['remise'] : 0;


    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Votre Commande', 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Nom');
    $pdf->Cell(40, 10, 'Prix');
    $pdf->Cell(40, 10, 'Quantite');
    $pdf->Ln();

    $ids = array_keys($_SESSION['panier']);
    if (!empty($ids)) {
        $idList = implode(',', $ids);
        $query = "SELECT * FROM products WHERE id IN ($idList)";
        $result = mysqli_query($con, $query);

        if ($result) {
            while ($product = mysqli_fetch_assoc($result)) {
                $pdf->Cell(40, 10, $product['name']);
                $pdf->Cell(40, 10, $product['price'] . ' Fcfa');
                $pdf->Cell(40, 10, $_SESSION['panier'][$product['id']]);
                $pdf->Ln();
            }
        } else {
            die("Erreur de requête: " . mysqli_error($con));
        }
    }

    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Remise: ' . $remise . ' % ', 0, 1, 'R');

    $pdf->Cell(0, 10, 'Total commande: ' . $total . ' Fcfa', 0, 1, 'R');

    $pdf->Output('D', 'commande.pdf');
}
?>
