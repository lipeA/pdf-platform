<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pdfPlatform = new PDFPlatform();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo_pdf'])) {
    $id_secao = $_POST['id_secao'];
    $nome_arquivo = $_POST['nome_arquivo'];
    $arquivo = $_FILES['arquivo_pdf'];
    
    // Validar extensão
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    if ($extensao !== 'pdf') {
        echo json_encode(['success' => false, 'message' => 'Apenas arquivos PDF são permitidos.']);
        exit;
    }
    
    // Gerar nome único para o arquivo
    $nome_arquivo_sistema = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $arquivo['name']);
    $caminho_arquivo = UPLOAD_DIR . $nome_arquivo_sistema;
    
    if (move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
        if ($pdfPlatform->uploadArquivo($id_secao, $nome_arquivo, $caminho_arquivo)) {
            echo json_encode(['success' => true]);
        } else {
            unlink($caminho_arquivo);
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar no banco de dados.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload do arquivo.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
}
?>