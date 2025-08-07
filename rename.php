<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pdfPlatform = new PDFPlatform();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_arquivo']) && isset($_POST['novo_nome'])) {
        $id_arquivo = $_POST['id_arquivo'];
        $novo_nome = $_POST['novo_nome'];
        
        if ($pdfPlatform->renameArquivo($id_arquivo, $novo_nome)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao renomear o arquivo.']);
        }
    } elseif (isset($_POST['id_secao']) && isset($_POST['novo_nome'])) {
        $id_secao = $_POST['id_secao'];
        $novo_nome = $_POST['novo_nome'];
        
        if ($pdfPlatform->renameSecao($id_secao, $novo_nome)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao renomear a seção.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
}
?>