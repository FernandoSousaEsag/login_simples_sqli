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

if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'del') {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM turma WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                $message = "Turma removida com sucesso.";
                $messageType = "success";
            } else {
                $message = "Nao foi possivel remover a turma. Verifique se existem alunos associados.";
                $messageType = "danger";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "Erro ao preparar a remocao.";
            $messageType = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <title>Turmas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" media="screen" href="./src/main.css">
</head>

<body>
    <?php require_once __DIR__ . "/menu_nav.php"; ?>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Turmas</h1>
            <a href="turmas_inserir.php" class="btn btn-primary btn-sm">Inserir turma</a>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>" role="alert">
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Turma</th>
                            <th>Ano letivo</th>
                            <th class="text-center">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT id, turma, anoletivo FROM turma ORDER BY anoletivo, turma";
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['turma'], ENT_QUOTES, 'UTF-8') . "</td>";
                                echo "<td>" . htmlspecialchars($row['anoletivo'], ENT_QUOTES, 'UTF-8') . "</td>";
                                echo "<td class='text-center'>";
                                echo "<a class='btn btn-sm btn-outline-primary mr-2' href='turmas_editar.php?id=" . (int)$row['id'] . "'>Editar</a>";
                                echo "<a class='btn btn-sm btn-outline-danger' href='turmas.php?action=del&id=" . (int)$row['id'] . "' ";
                                echo "onclick=\"return confirm('Tem a certeza que pretende remover esta turma?');\">Apagar</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            mysqli_free_result($result);
                        } else {
                            echo "<tr><td colspan='3' class='text-center'>Sem turmas registadas.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . "/utils/footer.php"; ?>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>

</html>
