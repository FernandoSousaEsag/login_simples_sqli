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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $turma = isset($_POST['turma']) ? trim($_POST['turma']) : '';
    $anoletivo = isset($_POST['anoletivo']) ? (int)$_POST['anoletivo'] : 0;

    if ($turma === '' || $anoletivo <= 0) {
        $message = "Preencha todos os campos.";
        $messageType = "danger";
    } elseif (strlen($turma) > 10) {
        $message = "A designacao da turma deve ter no maximo 10 caracteres.";
        $messageType = "danger";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO turma (turma, anoletivo) VALUES (?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $turma, $anoletivo);
            if (mysqli_stmt_execute($stmt)) {
                $message = "Turma adicionada com sucesso.";
                $messageType = "success";
            } else {
                $message = "Erro ao inserir a turma.";
                $messageType = "danger";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "Erro ao preparar a insercao.";
            $messageType = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <title>Inserir turma</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" media="screen" href="./src/main.css">
</head>

<body>
    <?php require_once __DIR__ . "/menu_nav.php"; ?>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Inserir turma</h1>
            <a href="turmas.php" class="btn btn-secondary btn-sm">Voltar</a>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>" role="alert">
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form action="turmas_inserir.php" method="post">
                    <div class="form-group">
                        <label for="turma">Turma</label>
                        <input type="text" class="form-control" id="turma" name="turma" maxlength="10" required>
                    </div>
                    <div class="form-group">
                        <label for="anoletivo">Ano letivo</label>
                        <input type="number" class="form-control" id="anoletivo" name="anoletivo" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . "/utils/footer.php"; ?>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>

</html>
