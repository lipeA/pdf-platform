<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pdfPlatform = new PDFPlatform();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_secao']) && isset($_POST['new_order'])) {
    $id_secao = $_POST['id_secao'];
    $new_order = json_decode($_POST['new_order']);
    
    if ($pdfPlatform->reorderArquivos($id_secao, $new_order)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao reordenar os arquivos.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
}
?>