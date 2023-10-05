<?php
session_start();

require_once __DIR__ . '/config.php';

// Setto messaggio vuoto di default
$msg = "";

// Controllo se il token è stato fornito nell'URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $query = "SELECT * FROM utenti WHERE reset_token = '$token'";
    $result = $connect->query($query);

    if ($result->num_rows > 0) {
        if (isset($_POST['reset_password'])) {
            // Ottiengo la nuova password fornita dall'utente
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if ($new_password === $confirm_password) {

                // Cifro la nuova password
                $hashed_password = md5($new_password);

                // Aggiorno la password nel database
                $query = "UPDATE utenti SET password = '$hashed_password', reset_token = NULL WHERE reset_token = '$token'";
                $connect->query($query);

                // Mostro un messaggio di successo all'utente
                $msg = "<h2 class='password_status green'>La tua password è stata reimpostata con successo.<br>
                Clicca <a href='index.php'>QUI</a> per accedere nuovamente.</h2>";
            } else {
                // Le password non corrispondono, mostra un messaggio di errore
                $msg = "<h2 class='password_status red'>Le password non corrispondono. Riprova.</h2>";
            }
        }
    } else {
        // Token non valido
        $msg = "<h2 class='password_status red'>Il token fornito non è valido.<br>Ogni azione è nulla.</h2>";
    }
} else {
    // Nessun token fornito nell'URL
    $msg = "<h2 class='password_status red'>Token non fornito.<br>Ogni azione è nulla.</h2>";
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
    <!-- Importo font awesome per icona occhio-password -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <title>Edusogno</title>
</head>

<body>
    <header>
        <img src="./assets/img/logo.svg" alt="logo">
    </header>

    <main>
        <!-- Stampo messaggio riuscita/errore -->
        <?php echo $msg; ?>

        <h1 class="text-center titolo">Reimposta la tua password</h1>
        <div class="container">

            <!-- Form per reimpostare la password -->
            <form method="POST">
                <h5><label for="password">Inserisci la nuova password</label></h5>
                <div class="password-container">
                    <input type="password" name="new_password" id="user-password" placeholder="Nuova password" required>
                    <i class="fa-solid fa-eye" id="eye"></i>
                </div>

                <h5><label for="confirm_password">Conferma la nuova password</label></h5>
                <div class="password-container">
                    <input type="password" name="confirm_password" id="confirm-password" placeholder="Conferma password" required>
                    <i class="fa-solid fa-eye" id="eye-confirm"></i>
                </div>

                <button type="submit" id="reimposta" name="reset_password">Reimposta password</button>
            </form>
        </div>
    </main>
</body>

<script>
    // Funzione per rendere visibile/invisibile password al click su occhio
    const passwordInput = document.getElementById("user-password");
    const confirmInput = document.getElementById("confirm-password");
    const eye = document.getElementById("eye");
    const eye_confirm = document.getElementById("eye-confirm");

    eye.addEventListener("click", function() {
        this.classList.toggle("fa-eye-slash");
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
        passwordInput.setAttribute("type", type);
    });

    eye_confirm.addEventListener("click", function() {
        this.classList.toggle("fa-eye-slash");
        const type = confirmInput.getAttribute("type") === "password" ? "text" : "password";
        confirmInput.setAttribute("type", type);
    });
</script>

<style>
    .password_status {
        background-color: white;
        padding: 1rem;
    }

    .green {
        color: green;
    }

    .red {
        color: red;
    }

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
        width: 50%;
    }

    .text-center {
        text-align: center;
    }

    .titolo,
    h5 {
        color: #134077;
    }

    .container {
        background-color: white;
        border-radius: 20px;
        border: 2px solid #2b5486;
        margin-bottom: 20rem;
    }

    form {
        padding: 2rem 2rem 0 2rem;
    }

    input {
        border: none;
        width: 100%;
        height: 1.5rem;
        border-bottom: 1px solid black;
        margin-bottom: 1.5rem;
    }

    ::placeholder {
        color: gray;
        opacity: 0.3;
        font-size: 1.2rem;
    }

    .password-container {
        width: 100%;
        position: relative;
    }

    .password-container input[type="password"],
    .password-container input[type="text"] {
        width: 100%;
        padding: 12px 36px 12px 0px;
        box-sizing: border-box;
    }

    .fa-eye {
        position: absolute;
        top: 5%;
        right: 2%;
        cursor: pointer;
        color: lightgray;
        scale: 1.3;
        color: blue;
    }

    #reimposta {
        cursor: pointer;
        width: 100%;
        margin-top: 2rem;
        margin-bottom: 2rem;
        background-color: #0057ff;
        color: white;
        border-radius: 20px;
        padding: 1.2rem 0;
        height: auto;
        font-size: 1.5rem;
    }
</style>

</html>