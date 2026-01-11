<?php 
require_once __DIR__."/database/mysqli.php";
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Escola Website - Inserir Produto</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body class="bg-light">
    <?php require_once __DIR__ . "/menu_nav.php"; ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Inserir Novo Produto</h2>
                        <form action="insereprodutos.php" method="post">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome do Produto</label>
                                <input type="text" class="form-control" id="nome" name="nome" required minlength="3"
                                    maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label for="referencia" class="form-label">Referência</label>
                                <input type="text" class="form-control" id="referencia" name="referencia" required
                                    minlength="3" maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label for="preco" class="form-label">Preço</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="number" class="form-control" id="preco" name="preco" required
                                        step="0.01" min="0.01">
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Registar Produto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>

</html>
<?php

if ($_POST) { // Se existir um post, entra!
    // Proteção contra SQL injection
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $referencia = mysqli_real_escape_string($conn, $_POST['referencia']);
    $preco = mysqli_real_escape_string($conn, $_POST['preco']);
    
    // Validações adicionais
    $preco = floatval($preco); // Converte para número
    
    $sql = "INSERT INTO produtos (nome, referencia, preco) VALUES ('$nome', '$referencia', $preco)";
    
    if (mysqli_query($conn, $sql)) {
        echo "<div class='container mt-3'><div class='alert alert-success'>Produto adicionado com sucesso!</div></div>";
    } else {
        echo "<div class='container mt-3'><div class='alert alert-danger'>Erro: " . mysqli_error($conn) . "</div></div>";
    }
}

mysqli_close($conn);
?>
