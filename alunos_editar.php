<?php
require_once __DIR__ . "/database/mysqli.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: index.php");
    exit();
}

$message = '';
$messageType = '';
$aluno = [
    'id' => 0,
    'nome' => '',
    'idade' => '',
    'morada' => '',
    'localidade' => '',
    'codigopostal' => '',
];

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = mysqli_prepare($conn, "SELECT id, nome, idade, morada, localidade, codigopostal FROM alunos WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $alunoId, $alunoNome, $alunoIdade, $alunoMorada, $alunoLocalidade, $alunoCodigoPostal);
        if (mysqli_stmt_fetch($stmt)) {
            $aluno['id'] = $alunoId;
            $aluno['nome'] = $alunoNome;
            $aluno['idade'] = $alunoIdade;
            $aluno['morada'] = $alunoMorada;
            $aluno['localidade'] = $alunoLocalidade;
            $aluno['codigopostal'] = $alunoCodigoPostal;
        }
        mysqli_stmt_close($stmt);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $idade = isset($_POST['idade']) ? (int)$_POST['idade'] : 0;
    $morada = isset($_POST['morada']) ? trim($_POST['morada']) : '';
    $localidade = isset($_POST['localidade']) ? trim($_POST['localidade']) : '';
    $codigopostal = isset($_POST['codigopostal']) ? (int)$_POST['codigopostal'] : 0;

    if ($id <= 0 || $nome === '' || $idade <= 0 || $morada === '' || $localidade === '' || $codigopostal <= 0) {
        $message = "Preencha todos os campos.";
        $messageType = "danger";
    } elseif (strlen($nome) > 100 || strlen($morada) > 50) {
        $message = "Verifique o tamanho dos campos.";
        $messageType = "danger";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE alunos SET nome = ?, idade = ?, morada = ?, localidade = ?, codigopostal = ? WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sissii", $nome, $idade, $morada, $localidade, $codigopostal, $id);
            if (mysqli_stmt_execute($stmt)) {
                $message = "Aluno atualizado com sucesso.";
                $messageType = "success";
            } else {
                $message = "Erro ao atualizar o aluno.";
                $messageType = "danger";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "Erro ao preparar a atualizacao.";
            $messageType = "danger";
        }
    }

    $aluno['id'] = $id;
    $aluno['nome'] = $nome;
    $aluno['idade'] = $idade;
    $aluno['morada'] = $morada;
    $aluno['localidade'] = $localidade;
    $aluno['codigopostal'] = $codigopostal;
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <title>Editar aluno</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" media="screen" href="./src/main.css">
</head>

<body>
    <?php require_once __DIR__ . "/menu_nav.php"; ?>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Editar aluno</h1>
            <a href="alunos.php" class="btn btn-secondary btn-sm">Voltar</a>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>" role="alert">
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php endif; ?>

        <?php if ($aluno['id'] <= 0): ?>
        <div class="alert alert-warning" role="alert">
            Aluno nao encontrado.
        </div>
        <?php else: ?>
        <div class="card">
            <div class="card-body">
                <form action="alunos_editar.php" method="post">
                    <input type="hidden" name="id" value="<?php echo (int)$aluno['id']; ?>">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" maxlength="100" required
                            value="<?php echo htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="idade">Idade</label>
                        <input type="number" class="form-control" id="idade" name="idade" min="1" required
                            value="<?php echo htmlspecialchars($aluno['idade'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="morada">Morada</label>
                        <input type="text" class="form-control" id="morada" name="morada" maxlength="50" required
                            value="<?php echo htmlspecialchars($aluno['morada'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="localidade">Localidade</label>
                        <input type="text" class="form-control" id="localidade" name="localidade" required
                            value="<?php echo htmlspecialchars($aluno['localidade'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="codigopostal">Codigo postal</label>
                        <input type="number" class="form-control" id="codigopostal" name="codigopostal" min="1" required
                            value="<?php echo htmlspecialchars($aluno['codigopostal'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php require_once __DIR__ . "/utils/footer.php"; ?>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>

</html>
