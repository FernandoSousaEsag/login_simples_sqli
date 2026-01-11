<?php	
	include  './login.php';
	//include  '../components/footer.php';
?>


<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Escola Website - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel='stylesheet' type='text/css' media='screen' href='./src/main.css'>
</head>

<body class="bg-light">
    <div class="container">
        <header class="text-center py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h1 class="display-4 mb-4">Aplicação Escola</h1>
                </div>
            </div>
        </header>

        <main>
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-4">Login</h2>

                            <form action="login.php" method="post">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Utilizador</label>
                                    <input type="text" name="username" id="username" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>

                                <?php if($emptyUsernameOrPassword): ?>
                                <div class="alert alert-danger" role="alert">
                                    Username ou password vazio
                                </div>
                                <?php endif; ?>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        Iniciar sessão
                                    </button>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <a href="registo.php" class="text-decoration-none">
                                    Novo utilizador
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="text-center text-muted mt-4">
                        <small>Sistema de login com mysqli</small>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php require_once __DIR__ . "/utils/footer.php"; ?>
</body>

</html>