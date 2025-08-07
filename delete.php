<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pdfPlatform = new PDFPlatform();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_arquivo'])) {
    $id_arquivo = $_POST['id_arquivo'];
    
    if ($pdfPlatform->deleteArquivo($id_arquivo)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir o arquivo.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
}
?>