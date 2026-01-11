<?php 
require_once __DIR__ . "/database/mysqli.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = [
    'id' => 0,
    'username' => '',
    'passw' => '',
    'users_type_id' => 0,
];
$isAdmin = false;
$message = '';
$messageType = '';
$userTypes = [];
$userTypesById = [];

if (!empty($_SESSION['username'])) {
    $stmt = mysqli_prepare($conn, "SELECT ut.designacao FROM users u JOIN users_type ut ON u.users_type_id = ut.id WHERE u.username = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $designacao);
        if (mysqli_stmt_fetch($stmt) && strtolower(trim($designacao)) === 'admin') {
            $isAdmin = true;
        }
        mysqli_stmt_close($stmt);
    }
}

if ($isAdmin) {
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
    }
}

if (!empty($_GET['id'])) { // Ver a informaÇõÇœo.
    $id = (int)$_GET['id'];
    $stmt = mysqli_prepare($conn, "SELECT id, username, passw, users_type_id FROM users WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $userId, $userName, $userPass, $userTypeId);
        if (mysqli_stmt_fetch($stmt)) {
            $user['id'] = $userId;
            $user['username'] = $userName;
            $user['passw'] = $userPass;
            $user['users_type_id'] = (int)$userTypeId;
        }
        mysqli_stmt_close($stmt);
    }
} else if ($_POST) {
    $id = (int)$_POST['id'];
    $username = trim($_POST['username']);
    $password = trim($_POST['passw']);
    $usersTypeId = isset($_POST['users_type_id']) ? (int)$_POST['users_type_id'] : 0;

    if ($username !== '' && $password !== '') {
        if ($isAdmin && $usersTypeId > 0 && !isset($userTypesById[$usersTypeId])) {
            $message = "Tipo de utilizador invalido.";
            $messageType = "danger";
        } else {
            if ($isAdmin && $usersTypeId > 0) {
                $sql = "UPDATE users SET username = ?, passw = ?, users_type_id = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ssii", $username, $password, $usersTypeId, $id);
                }
            } else {
                $sql = "UPDATE users SET username = ?, passw = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ssi", $username, $password, $id);
                }
            }

            if (!empty($stmt) && mysqli_stmt_execute($stmt)) {
                $message = "Registo atualizado com sucesso";
                $messageType = "success";
            } else {
                $message = "Erro ao atualizar registo: " . $conn->error;
                $messageType = "danger";
            }

            if (!empty($stmt)) {
                mysqli_stmt_close($stmt);
            }
        }
    }
} else {
    // Mandar utilizador para a pagina 404 (nof found)
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <title>Escola Website - Mostrar dados</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <meta charset="UTF-8">
</head>

<body>
    <?php require_once __DIR__ . "/menu_nav.php"; ?>

<div class="col-md-6 offset-md-3 text-center bg-light  border-secondary mt-5 col-sm-12">
    <h6> Alterar dados do utilizador </h6>

    <?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?> mb-4" role="alert">
        <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php endif; ?>

    <form class="table" action="alterardados.php" method="post">

        <input type="text" hidden name="id" class="input" value="<?= $user['id'] ?>" />
        <label for="name">Utilizador:</label>
        <input type="text" name="username" class="input" value="<?= $user['username'] ?>" />
        <br>
        <label for="name">Password: </label>
        <input type="password" name="passw" class="input" value="<?= $user['passw'] ?>" />
        <br>
        <?php if ($isAdmin): ?>
        <label for="users_type_id">Tipo de utilizador:</label>
        <select class="input" id="users_type_id" name="users_type_id">
            <?php foreach ($userTypes as $type): ?>
            <option value="<?php echo (int)$type['id']; ?>" <?php echo ((int)$type['id'] === (int)$user['users_type_id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($type['label'], ENT_QUOTES, 'UTF-8'); ?>
            </option>
            <?php endforeach; ?>
        </select>
        <br>
        <?php endif; ?>
        <input class="btn btn-info" type="submit" value="Alterar" class="button" />
    </form>
</div>
</body>
</html>
