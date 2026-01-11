<?php
session_start();
require_once './database/mysqli.php';

$emptyUsernameOrPassword = false;
$loginError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpar e validar inputs
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        $emptyUsernameOrPassword = true;
    } else {
        // Proteção contra SQL Injection
        $username = mysqli_real_escape_string($conn, $username);
        $hashedPassword = md5($password); // Note: MD5 não é recomendado para produção

        // Preparar e executar a query de forma mais segura
        $query = "SELECT id, username FROM users WHERE username = ? AND passw = ?";
        
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, "ss", $username, $hashedPassword);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) === 1) {
                // Login bem sucedido
                $_SESSION['username'] = $username;
                $_SESSION['loggedIn'] = true;

                // Limpar qualquer output antes do redirecionamento
                ob_clean();
                header("Location: menu.php");
                exit();
            } else {
                $loginError = true;
            }

            mysqli_stmt_close($stmt);
        } else {
            $loginError = true;
        }
    }
}

// Se houver erro no login, redirecionar para index.php com mensagem de erro
if ($emptyUsernameOrPassword || $loginError) {
    $_SESSION['login_error'] = $loginError ? 'Usuário ou senha inválidos' : 'Preencha todos os campos';
    header("Location: index.php");
    exit();
}
?>