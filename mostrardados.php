<?php
session_start();
require_once("./utilsData.php");
require_once('./database/mysqli.php');

// Verificar se o usuário está logado
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: index.php");
    exit();
}

// Inicializar variáveis de mensagem
$message = '';
$messageType = '';
$currentUserId = 0;

if (!empty($_SESSION['username'])) {
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $currentUserId);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

// Processar exclusão se solicitada
if (isset($_GET['action']) && $_GET['action'] === 'del' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($id > 0) {
        // Verificar se não está tentando apagar o próprio utlizador
        $checkUser = mysqli_prepare($conn, "SELECT username FROM users WHERE id = ?");
        mysqli_stmt_bind_param($checkUser, "i", $id);
        mysqli_stmt_execute($checkUser);
        mysqli_stmt_bind_result($checkUser, $username);
        mysqli_stmt_fetch($checkUser);
        mysqli_stmt_close($checkUser);

        if ($currentUserId > 0 && $id === (int)$currentUserId) {
            $message = "Não é possível apagar o seu próprio utilizador.";
            $messageType = "danger";
        } else {
            $deleteQuery = "DELETE FROM users WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $deleteQuery)) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $message = "Utilizador removido com sucesso.";
                    $messageType = "success";
                } else {
                    $message = "Erro ao tentar remover o utilizador.";
                    $messageType = "danger";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Escola Website - Mostrar dados</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="./src/main.css">
</head>

<body>
    <?php require_once __DIR__ . "/menu_nav.php"; ?>
    <div class="container">
        <div class="col-md-8 offset-md-2 text-center">
            <div class="card mt-5">
                <div class="card-body">
                    <h4 class="card-title text-secondary mb-4">Listagem de utilizadores</h4>

                    <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php endif; ?>

                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Utilizador</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                    $query = "SELECT id, username FROM users ORDER BY username";
                    $sql = mysqli_query($conn, $query);
                    
                    if ($sql) {
                        $users = mysqli_fetch_all($sql, MYSQLI_ASSOC);
                        
                        foreach ($users as $user) {
                            $isCurrentUser = $currentUserId > 0 && (int)$user['id'] === (int)$currentUserId;
                            echo $isCurrentUser ? "<tr class='table-success'>" : "<tr>";
                            echo "<td>" . htmlspecialchars($user['username']) . ($isCurrentUser ? ' (Utilizador atual)' : '') . "</td>";
                            echo "<td class='text-center'>";
                            echo "<a href='alterardados.php?id=" . $user['id'] . "' class='btn btn-sm btn-outline-primary mx-1'>Alterar</a>";
                            if ($isCurrentUser) {
                                echo "<button class='btn btn-sm btn-outline-danger mx-1' disabled title='Não é possível apagar o utilizador atual'>Apagar</button>";
                            } else {
                                echo "<a href='mostrardados.php?action=del&id=" . $user['id'] . "' 
                                        class='btn btn-sm btn-outline-danger mx-1'
                                        onclick=\"return confirm('Tem certeza que deseja remover o utilizador \'" 
                                        . htmlspecialchars($user['username']) . "\'?');\">Apagar</a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>Erro ao carregar utilizadores</td></tr>";
                    }
                    ?>
                        </tbody>
                    </table>
                    <div class="mb-4"></div>
                </div>
            </div>

            <!-- Bootstrap JS e dependências -->
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>

</html>
<?php mysqli_close($conn); ?>
