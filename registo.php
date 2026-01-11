<?php 
include  './database/mysqli.php';
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <title>Escola Website - Login</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <link rel='stylesheet' type='text/css' media='screen' href='./src/main.css'>
    <meta charset="UTF-8">
</head>

<body>

    <header class="row">

        <div class="col-md-6 offset-md-3 text-center bg-light border border-secondary mt-5 col-sm-12">
            <!-- offset-md-3 desloca 3 colunas para a direita  -->

            <h4 class="display-4">Aplicação Escola</h4>
        </div>

    </header>



    <main class=" col-md-6 offset-md-3 text-center mt-5 mb-5">
        <H5 class="display-5 text-muted mb-5"> Novo utilizador </H5>

        <section class="row justify-content-center bg-light mt-3">


            <form action="registo.php" method="post">
                Username:<input type="text" class="form-control mb-3 " name=" username">
                Password:<input type="password" class="form-control mb-3" name=" password">
                <input type="submit" class="btn btn-success mb-5" value="Registar">
            </form>
        </section>



        <?php


if (isset($_POST['username']))
{
	$username = $_POST['username'];
	$password= md5("$_POST[password]");
	$sql = "insert into users (username, passw, user_type)
	VALUES ('$_POST[username]', '$password', 1)";

	
	if (mysqli_query($conn, $sql)) {

			echo '<section class= "mt-5" >';
				echo '<section class= "row justify-content-center">';
        			echo "Novo registo adicionado com sucesso!";
				echo "</section>";
				echo '<section class= "row justify-content-center">';
        			echo "<a href='index.php'>Início</a>";
				echo "</section>";
			echo "</section>";

        } else {
        echo "Erro: " . $sql . "<br>" . mysqli_error($conn);
        }
        //error_reporting(0); // N�o mostra avisos.
        /* header("Location: Menu.php \n"); */ // redireciona para p�gina protegida.
        }

        ?>
</body>

</html>