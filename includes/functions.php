<?php
// includes/functions.php
require_once 'database.php';

class PDFPlatform {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Função para gerar shortcode único
    public function generateShortcode() {
        return 'pdf_' . bin2hex(random_bytes(4));
    }

    // Funções para CRUD de abas, seções e arquivos
    public function getAbas() {
        $query = "SELECT * FROM abas ORDER BY id";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getSecoesByAba($id_aba) {
        $query = "SELECT * FROM secoes WHERE id_aba = ? ORDER BY id";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id_aba);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getArquivosBySecao($id_secao) {
        $query = "SELECT * FROM arquivos WHERE id_secao = ? ORDER BY ordem";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id_secao);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addSecao($id_aba, $nome_secao) {
        $shortcode = $this->generateShortcode();
        $query = "INSERT INTO secoes (id_aba, nome, shortcode) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iss", $id_aba, $nome_secao, $shortcode);
        return $stmt->execute();
    }

    public function renameSecao($id_secao, $novo_nome) {
        $query = "UPDATE secoes SET nome = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $novo_nome, $id_secao);
        return $stmt->execute();
    }

    public function uploadArquivo($id_secao, $nome_arquivo, $caminho_arquivo) {
        // Obter a última ordem
        $query = "SELECT MAX(ordem) as max_ordem FROM arquivos WHERE id_secao = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id_secao);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $ordem = $row['max_ordem'] ? $row['max_ordem'] + 1 : 1;

        $query = "INSERT INTO arquivos (id_secao, nome, caminho, ordem) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("issi", $id_secao, $nome_arquivo, $caminho_arquivo, $ordem);
        return $stmt->execute();
    }

    public function deleteArquivo($id_arquivo) {
        // Primeiro obter o caminho para deletar o arquivo físico
        $query = "SELECT caminho FROM arquivos WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id_arquivo);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row && file_exists($row['caminho'])) {
            unlink($row['caminho']);
        }

        // Depois deletar do banco
        $query = "DELETE FROM arquivos WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id_arquivo);
        return $stmt->execute();
    }

    public function renameArquivo($id_arquivo, $novo_nome) {
        $query = "UPDATE arquivos SET nome = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $novo_nome, $id_arquivo);
        return $stmt->execute();
    }

    public function reorderArquivos($id_secao, $new_order) {
        $this->db->begin_transaction();
        try {
            foreach ($new_order as $ordem => $id_arquivo) {
                $query = "UPDATE arquivos SET ordem = ? WHERE id = ? AND id_secao = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("iii", $ordem, $id_arquivo, $id_secao);
                $stmt->execute();
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
}
?>