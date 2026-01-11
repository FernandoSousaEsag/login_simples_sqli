<?php
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/database/mysqli.php";
?>
<header>
    <?php
        require_once __DIR__ . "/utils/header.php";
        if (!empty($_SESSION['username'])) {
            $roleLabel = '';
            $stmt = mysqli_prepare($conn, "SELECT LOWER(TRIM(ut.designacao)) FROM users u JOIN users_type ut ON u.users_type_id = ut.id WHERE u.username = ? LIMIT 1");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $designacao);
                if (mysqli_stmt_fetch($stmt) && ($designacao === 'admin' || $designacao === 'administrador')) {
                    $roleLabel = " (Administrador)";
                }
                mysqli_stmt_close($stmt);
            }

            echo "<span class='ml-3 user-greeting'>Ola " . htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') . $roleLabel . "</span>";
        }
    ?>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand text-dark d-flex align-items-center" href="menu.php">
            <img src="imagens/logo_esag_cinza_pequeno.png" alt="Logo" class="menu-logo mr-2">
            In&iacute;cio
        </a>
        <div class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Utilizadores
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="inseredados.php">Inserir</a>
                    <a class="dropdown-item" href="mostrardados.php">Mostrar</a>
                    <a class="dropdown-item" href="apagardados.php">Apagar</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark" href="#" id="produtosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Produtos
                </a>
                <div class="dropdown-menu" aria-labelledby="produtosDropdown">
                    <a class="dropdown-item" href="insereprodutos.php">Inserir</a>
                </div>
            </li>
        </div>
        <a class="navbar-brand text-dark" href="logout.php">Logout</a>
    </nav>
</header>
