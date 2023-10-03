<?php
// Riapro sessione per ricevere email
session_start();

// Richiamo le credenziali dal file config.php
require_once __DIR__ . '/config.php';

// Verifico se l'utente è loggato
if (!isset($_SESSION['email'])) {
    header('Location: index.php'); // Reindirizzo alla pagina di login se l'utente non è loggato
    exit();
}

// Ottiengo l'email dell'utente dalla variabile di sessione
$email = $_SESSION['email'];

// Ottiengo il nome e il cognome dell'utente loggato dalla tabella "utenti"
$sql_user = "SELECT nome, cognome FROM utenti WHERE email = '$email'";
$result_user = mysqli_query($connect, $sql_user);
$row_user = mysqli_fetch_array($result_user);
$nome = $row_user['nome'];
$cognome = $row_user['cognome'];

// Ottiengo gli eventi in cui l'utente è presente dalla tabella "eventi"
$sql_eventi = "SELECT * FROM eventi WHERE attendees LIKE '%$email%'";
$result_eventi = mysqli_query($connect, $sql_eventi);

// Creo la lista degli eventi in cui l'utente è presente
$eventi = array();
while ($row_eventi = mysqli_fetch_array($result_eventi)) {
    $evento = new stdClass(); // Creo un nuovo oggetto per ogni evento
    $evento->nome_evento = $row_eventi['nome_evento'];
    $evento->data_evento = $row_eventi['data_evento'];
    $eventi[] = $evento; // Aggiungo l'oggetto "evento" all'array "eventi"
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Importo font da Google Font (DM Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100;0,9..40,200;0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;0,9..40,900;0,9..40,1000;1,9..40,100;1,9..40,200;1,9..40,300;1,9..40,400;1,9..40,500;1,9..40,600;1,9..40,700;1,9..40,800;1,9..40,900;1,9..40,1000&display=swap" rel="stylesheet">
    <title>Edusogno</title>
</head>

<body>
    <header>
        <img src="./assets/img/logo.svg" alt="logo">
    </header>

    <main>
        <h1 class="text-center titolo">Ciao <?php echo strtoupper($nome); ?> ecco i tuoi eventi</h1>
        <div class="container">

            <?php
            foreach ($eventi as $evento) {
            ?>
                <div class="card">
                    <h2 style="line-height: 1; height:60px"><?php echo $evento->nome_evento; ?></h2>
                    <h4><?php echo $evento->data_evento; ?></h4>
                    <button>JOIN</button>
                </div>
            <?php
            }
            ?>
        </div>
    </main>
</body>

<script>

</script>

<style>
    body {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        height: 100%;
        background-image: url('./assets/img/background.png');
        background-repeat: no-repeat;
        background-size: cover;
        background-position: bottom;
        font-family: 'DM Sans', sans-serif;
    }

    header {
        background-color: white;
        padding: 1.2rem 2.5rem;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
        box-shadow: 2px 10px 15px #8cb2f7;
    }

    main {
        margin: 3rem auto;
        width: 80%;
    }

    .text-center {
        text-align: center;
    }

    .titolo {
        color: #134077;
    }

    .container {
        margin-top: 3rem;
        margin-bottom: 20rem;

        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 30px;
    }

    .card {
        width: 25%;
        background-color: white;
        border: 2px solid #2b5486;
        border-radius: 20px;
        padding: 0.5rem 2rem;
        font-size: 1.5rem;
        line-height: 0.5;
        margin-bottom: 2rem;
        min-height: 240px;
    }

    h4 {
        color: gray;
        opacity: 0.5;
        font-size: 1.2rem;
        font-weight: normal;
    }

    button {
        width: 100%;
        background-color: #0057ff;
        color: white;
        padding: 1rem 0;
        font-size: 1.4rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        cursor: pointer;
        border: none;
    }
</style>

</html>