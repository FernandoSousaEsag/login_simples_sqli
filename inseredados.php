<?php

require_once __DIR__ . "/database/mysqli.php";

$insertSuccess = false;
$insertError = '';
$userTypes = [];
$userTypesById = [];
$userTypesError = '';

$typesQuery = "SELECT id, designacao FROM users_type ORDER BY designacao";
if ($typesResult = mysqli_query($conn, $typesQuery)) {
    while ($row = mysqli_fetch_assoc($typesResult)) {
        $id = (int)$row['id'];
        $userTypes[] = [
            'id' => $id,
            'label' => $row['designacao'],
        ];
        $userTypesById[$id] = true;
    }
    mysqli_free_result($typesResult);
} else {
    $userTypesError = 'Erro ao carregar tipos de utilizador.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $userTypeId = isset($_POST['users_type_id']) ? (int)$_POST['users_type_id'] : 0;

    if ($username === '' || $password === '') {
        $insertError = 'Preencha todos os campos.';
    } elseif ($userTypesError !== '' || !isset($userTypesById[$userTypeId])) {
        $insertError = 'Selecione um tipo de utilizador valido.';
    } else {
        $hashedPassword = md5($password);
        $sql = "INSERT INTO users (username, passw, users_type_id) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssi", $username, $hashedPassword, $userTypeId);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) === 1) {
                $insertSuccess = true;
            } else {
                $insertError = 'Nao foi possivel registar o utilizador.';
            }

            mysqli_stmt_close($stmt);
        } else {
            $insertError = 'Erro ao preparar a operacao.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Escola Website - Inserir Utilizador</title>
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
                        <h2 class="card-title text-center mb-4">Inserir Novo Utilizador</h2>
                        <?php if ($insertSuccess): ?>
                        <div class="alert alert-success" role="alert">
                            Novo registo adicionado com sucesso!
                        </div>
                        <?php elseif ($insertError !== ''): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($insertError, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($userTypesError !== ''): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($userTypesError, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <?php endif; ?>
                        <form action="inseredados.php" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nome de Utilizador</label>
                                <input type="text" class="form-control" id="username" name="username" required
                                    minlength="3" maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required
                                    minlength="8">
                            </div>
                            <div class="mb-3">
                                <label for="users_type_id" class="form-label">Tipo de Utilizador</label>
                                <select class="form-control" id="users_type_id" name="users_type_id" required>
                                    <option value="">Selecione</option>
                                    <?php foreach ($userTypes as $type): ?>
                                    <option value="<?php echo (int)$type['id']; ?>">
                                        <?php echo htmlspecialchars($type['label'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Registar Utilizador</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script></body>

</html>
