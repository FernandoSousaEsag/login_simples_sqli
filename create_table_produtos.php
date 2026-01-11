<?php
// Incluir arquivo de conexão existente
require_once 'database/mysqli.php';

// SQL para criar a tabela produtos
$sql = "CREATE TABLE IF NOT EXISTS produtos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    referencia VARCHAR(50) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// Executar a query
if ($conn->query($sql) === TRUE) {
    echo "Tabela 'produtos' criada com sucesso!\n";
    
    // Mostrar estrutura da tabela
    $result = $conn->query("DESCRIBE produtos");
    if ($result) {
        echo "\nEstrutura da tabela 'produtos':\n";
        echo "===========================\n";
        while ($row = $result->fetch_assoc()) {
            echo str_pad($row['Field'], 15) . " " . 
                 str_pad($row['Type'], 15) . " " . 
                 ($row['Key'] == 'PRI' ? 'PRIMARY KEY ' : '') . 
                 ($row['Null'] == 'NO' ? 'NOT NULL' : '') . "\n";
        }
    }
} else {
    echo "Erro ao criar tabela: " . $conn->error . "\n";
}

$conn->close();
?>