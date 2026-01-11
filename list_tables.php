<?php
// Incluir arquivo de conexão existente
require_once 'database/mysqli.php';

echo "Tabelas no banco de dados 'escola':\n";
echo "===================================\n";

// Consulta para listar todas as tabelas
$query = "SHOW TABLES";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_array()) {
        $table = $row[0];
        echo "\n📋 Tabela: " . $table . "\n";
        
        // Consulta para obter estrutura de cada tabela
        $columns = $conn->query("SHOW COLUMNS FROM `$table`");
        if ($columns) {
            echo "   Colunas:\n";
            while ($col = $columns->fetch_assoc()) {
                echo "   - " . str_pad($col['Field'], 20) . " " . str_pad($col['Type'], 15) . 
                     ($col['Key'] == 'PRI' ? ' (Primary Key)' : '') . 
                     ($col['Null'] == 'NO' ? ' NOT NULL' : '') . "\n";
            }
        }
        echo "\n";
    }
} else {
    echo "Erro ao listar tabelas: " . $conn->error . "\n";
}

// Fechar conexão
$conn->close();
?>