<?php
// index.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pdfPlatform = new PDFPlatform();
$abas = $pdfPlatform->getAbas();
$aba_selecionada = isset($_GET['aba']) ? $_GET['aba'] : $abas[0]['id'];
$secoes = $pdfPlatform->getSecoesByAba($aba_selecionada);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma de PDF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Plataforma de Gerenciamento de PDFs</h1>
        
        <!-- Abas -->
        <ul class="nav nav-tabs" id="abasTab" role="tablist">
            <?php foreach ($abas as $aba): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $aba['id'] == $aba_selecionada ? 'active' : '' ?>" 
                            id="aba-<?= $aba['id'] ?>-tab" data-bs-toggle="tab" 
                            data-bs-target="#aba-<?= $aba['id'] ?>" type="button" role="tab" 
                            aria-controls="aba-<?= $aba['id'] ?>" aria-selected="<?= $aba['id'] == $aba_selecionada ? 'true' : 'false' ?>">
                        <?= htmlspecialchars($aba['nome']) ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
        
        <!-- Conteúdo das Abas -->
        <div class="tab-content p-3 border border-top-0 rounded-bottom" id="abasTabContent">
            <?php foreach ($abas as $aba): ?>
                <div class="tab-pane fade <?= $aba['id'] == $aba_selecionada ? 'show active' : '' ?>" 
                     id="aba-<?= $aba['id'] ?>" role="tabpanel" aria-labelledby="aba-<?= $aba['id'] ?>-tab">
                    
                    <!-- Formulário para adicionar nova seção -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Adicionar Nova Seção</h5>
                        </div>
                        <div class="card-body">
                            <form id="formAddSecao" class="row g-3">
                                <input type="hidden" name="id_aba" value="<?= $aba['id'] ?>">
                                <div class="col-md-8">
                                    <label for="nome_secao" class="form-label">Nome da Seção</label>
                                    <input type="text" class="form-control" id="nome_secao" name="nome_secao" required>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Adicionar Seção</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Lista de Seções -->
                    <div id="secoesContainer">
                        <?php 
                        $secoes_aba = $pdfPlatform->getSecoesByAba($aba['id']);
                        foreach ($secoes_aba as $secao): 
                            $arquivos = $pdfPlatform->getArquivosBySecao($secao['id']);
                        ?>
                            <div class="card mb-4 secao-card" data-secao-id="<?= $secao['id'] ?>">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 secao-titulo"><?= htmlspecialchars($secao['nome']) ?></h5>
                                    <div>
                                        <button class="btn btn-sm btn-outline-secondary rename-secao" 
                                                data-secao-id="<?= $secao['id'] ?>">
                                            <i class="fas fa-edit"></i> Renomear
                                        </button>
                                        <span class="badge bg-info ms-2">Shortcode: [<?= $secao['shortcode'] ?>]</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Formulário de upload -->
                                    <form class="row g-3 mb-4 form-upload" enctype="multipart/form-data">
                                        <input type="hidden" name="id_secao" value="<?= $secao['id'] ?>">
                                        <div class="col-md-4">
                                            <label for="nome_arquivo" class="form-label">Nome do Arquivo</label>
                                            <input type="text" class="form-control" name="nome_arquivo" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="arquivo_pdf" class="form-label">Arquivo PDF</label>
                                            <input type="file" class="form-control" name="arquivo_pdf" accept=".pdf" required>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-upload"></i> Upload
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Lista de arquivos -->
                                    <div class="arquivos-list" data-secao-id="<?= $secao['id'] ?>">
                                        <?php if (count($arquivos) > 0): ?>
                                            <div class="list-group arquivos-sortable">
                                                <?php foreach ($arquivos as $arquivo): ?>
                                                    <div class="list-group-item d-flex justify-content-between align-items-center arquivo-item" 
                                                         data-arquivo-id="<?= $arquivo['id'] ?>" draggable="true">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-file-pdf me-3 text-danger" style="font-size: 1.5rem;"></i>
                                                            <div>
                                                                <h6 class="mb-0 arquivo-nome"><?= htmlspecialchars($arquivo['nome']) ?></h6>
                                                                <small class="text-muted"><?= basename($arquivo['caminho']) ?></small>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <button class="btn btn-sm btn-outline-primary rename-arquivo me-2" 
                                                                    data-arquivo-id="<?= $arquivo['id'] ?>">
                                                                <i class="fas fa-edit"></i> Renomear
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger delete-arquivo" 
                                                                    data-arquivo-id="<?= $arquivo['id'] ?>">
                                                                <i class="fas fa-trash"></i> Excluir
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">Nenhum arquivo cadastrado nesta seção.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal para renomear -->
    <div class="modal fade" id="renameModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="renameModalTitle">Renomear</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="renameForm">
                        <input type="hidden" id="renameItemType">
                        <input type="hidden" id="renameItemId">
                        <div class="mb-3">
                            <label for="newName" class="form-label">Novo Nome</label>
                            <input type="text" class="form-control" id="newName" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmRename">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>