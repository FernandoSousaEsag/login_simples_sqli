<?php 
session_start();
require_once __DIR__."/database/mysqli.php";
// Verificar se o usuário está logado
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: index.php");
    exit();
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    
    if (empty($username)) {
        $message = "Por favor, insira um nome de utilizador.";
        $messageType = "warning";
    } else {
        // Evitar que o usuário apague a si mesmo
        if ($username === $_SESSION['username']) {
            $message = "Não é possível apagar o seu próprio utilizador.";
            $messageType = "danger";
        } else {
            // Usar prepared statement para evitar SQL injection
            $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE username = ?");
            mysqli_stmt_bind_param($stmt, "s", $username);
            
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_affected_rows($conn) > 0) {
                    $message = "Utilizador '" . htmlspecialchars($username) . "' foi removido com sucesso.";
                    $messageType = "success";
                } else {
                    $message = "Utilizador não encontrado.";
                    $messageType = "warning";
                }
            } else {
                $message = "Erro ao tentar remover o utilizador.";
                $messageType = "danger";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Escola Website - Remover Utilizador</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body>
    <?php require_once __DIR__ . "/menu_nav.php"; ?>
    <div class="container">
        <div class="col-md-6 offset-md-3 text-center">
            <div class="card mt-5">
                <div class="card-body">
                    <h4 class="card-title mb-4">Remover Utilizador</h4>

                    <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> mb-4" role="alert">
                        <?php echo $message; ?>
                    </div>
                    <?php endif; ?>

                    <form action="apagardados.php" method="post"
                        onsubmit="return confirm('Tem certeza que deseja remover este utilizador?');">
                        <div class="form-group">
                            <label for="username">Nome de Utilizador:</label>
                            <input type="text" class="form-control" id="username" name="username" required
                                placeholder="Insira o nome do utilizador">
                        </div>
                        <div class="mt-4">
                            <a href="mostrardados.php" class="btn btn-secondary mr-2">Voltar</a>
                            <button type="submit" class="btn btn-danger">Remover</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php mysqli_close($conn); ?>