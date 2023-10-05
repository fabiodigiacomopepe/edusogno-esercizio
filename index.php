<?php
// Richiamo le credenziali dal file config.php
require_once __DIR__ . '/config.php';

// Setto messaggio vuoto di default
$msg = "";

if (isset($_POST['bottone_accedi'])) {

    // Salvo nella variabili ciò che ricevo dal form
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = mysqli_real_escape_string($connect, md5($_POST['password']));

    // Verifico se l'email è registrata nel database
    $sql_email = "SELECT count(*) AS email FROM utenti WHERE email='" . $email . "'";
    $result_email = mysqli_query($connect, $sql_email);
    $row_email = mysqli_fetch_array($result_email);

    $count_email = $row_email['email'];

    if ($count_email > 0) { // Se l'email è registrata

        // Verifico che anche la password è corretta
        $sql_psw = "SELECT count(*) AS count FROM utenti WHERE email='" . $email . "' AND password='" . $password . "'";
        $result_psw = mysqli_query($connect, $sql_psw);
        $row_psw = mysqli_fetch_array($result_psw);
        $count_psw = $row_psw['count'];

        if ($count_psw > 0) {  // Se la password è corretta, accedo
            session_start();  // Avvio sessione per trasferire email dall'altra parte
            $_SESSION['email'] = $email;
            header('Location: events.php');
        } else {  // ALTRIMENTI (se non è corretta)
            $msg = "<div class='msg-error'>La password inserita è errata</div>";
        }
    } else { // Se l'email non è registrata (l'utente non esiste nel db)
        $msg = "<div class='msg-error'>Nessun utente trovato per questo indirizzo e-mail</div>";
    }
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
        <h1 class="text-center titolo">Hai già un account?</h1>
        <div class="container">

            <!-- Stampo messaggio riuscita/errore -->
            <?php echo $msg; ?>

            <form id="login-form" method="POST">
                <h5><label for="email">Inserisci l'e-mail</label></h5>
                <div><input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="user-email" placeholder="name@example.com">
                    <span id="email-error" class="invalid-feedback" role="alert"><strong></strong></span>
                </div>

                <h5><label for="password">Inserisci la password</label></h5>
                <div class="password-container"><input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="user-password" placeholder="Scrivila qui">
                    <i class="fa-solid fa-eye" id="eye"></i>
                    <span id="password-error" class="invalid-feedback" role="alert"><strong></strong></span>
                </div>

                <button type="sumbit" id="accedi" name="bottone_accedi">ACCEDI</button>
            </form>

            <h4 class="text-center">Non hai ancora un profilo? <a href="create-account.php"><span>Registrati</span></a></h4>
        </div>
    </main>
</body>

<script>
    // Funzione per rendere visibile/invisibile password al click su occhio
    const passwordInput = document.getElementById("user-password")
    const eye = document.getElementById("eye")

    eye.addEventListener("click", function() {
        this.classList.toggle("fa-eye-slash")
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password"
        passwordInput.setAttribute("type", type)
    })

    // Funzione di validazione dei dati del form
    document.addEventListener("DOMContentLoaded", function() {
        const loginForm = document.getElementById("login-form");
        const submitButton = document.getElementById("accedi");
        const emailField = document.getElementById("user-email");
        const passwordField = document.getElementById("user-password");


        submitButton.addEventListener("click", function() {
            if (validateForm()) {
                registerForm.submit();
            } else {
                event.preventDefault(); // Tolgo comportamento base del bottone (evito ricarica pagina)
            }
        });

        function validateForm() {
            let isValid = true;

            // per E-MAIL
            const emailValue = emailField.value.trim();
            if (emailValue === "") {
                isValid = false;
                document.getElementById("email-error").innerHTML = "L'email è obbligatoria.";
                emailField.classList.add("is-invalid");
            } else if (!isValidEmail(emailValue)) { // Controllo se email è nel formato corretto
                isValid = false;
                document.getElementById("email-error").innerHTML = "Inserisci un indirizzo email valido.";
                emailField.classList.add("is-invalid");
            } else {
                document.getElementById("email-error").innerHTML = "";
                emailField.classList.remove("is-invalid");
            }

            // per PASSWORD
            const passwordValue = passwordField.value.trim();
            if (passwordValue === "") {
                isValid = false;
                document.getElementById("password-error").innerHTML = "La password è obbligatoria.";
                passwordField.classList.add("is-invalid");
            } else {
                document.getElementById("password-error").innerHTML = "";
                passwordField.classList.remove("is-invalid");
            }

            return isValid;
        }

        // Controllo formato email
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    });
</script>

<style>
    .msg-error {
        padding: 2.5rem 2rem 0 2rem;
        font-weight: bold;
        font-size: 1.6rem;
        line-height: 1.5;
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
        background-position: top;
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

    .titolo {
        color: #134077;
    }

    .container {
        background-color: white;
        border-radius: 20px;
        border: 2px solid #2b5486;
        margin-bottom: 10rem;
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

    .invalid-feedback {
        color: red;
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

    #accedi {
        cursor: pointer;
        width: 100%;
        margin-top: 2rem;
        background-color: #0057ff;
        color: white;
        border-radius: 20px;
        padding: 1.2rem 0;
        height: auto;
        font-size: 1.5rem;
    }

    h4 {
        margin-bottom: 3rem;
        font-weight: normal;
    }

    span {
        text-decoration: underline;
        font-weight: bold;
        cursor: pointer;
        color: black;
    }
</style>

</html>