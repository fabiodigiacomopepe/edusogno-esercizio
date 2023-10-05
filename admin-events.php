<?php
// Riapro sessione per ricevere email
session_start();

// Importo PHPMiler
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

// Richiamo le credenziali dal file config.php e credential.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/credential.php';
require_once __DIR__ . '/eventController.php';

// Setto messaggio vuoto di default
$msg = "";

// Verifico se l'utente è loggato
if (!isset($_SESSION['email'])) {
    header('Location: index.php'); // Reindirizzo alla pagina di login se l'utente non è loggato
    exit();
}

// Ottengo l'email dell'utente dalla variabile di sessione
$email = $_SESSION['email'];

// Ottengo il nome e il cognome dell'utente loggato dalla tabella "utenti"
$sql_user = "SELECT nome, cognome FROM utenti WHERE email = '$email'";
$result_user = mysqli_query($connect, $sql_user);
$row_user = mysqli_fetch_array($result_user);
$nome = $row_user['nome'];
$cognome = $row_user['cognome'];

// Ottengo gli eventi in cui l'utente è presente dalla tabella "eventi"
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

// Controllo se l'utente ha fatto clic sul pulsante per reimpostare la password
if (isset($_POST['forgot_password'])) {
    // Ottiengo l'email fornita dall'utente
    $email = $_POST['email'];

    // Verifico se l'email esiste nel database
    $query = "SELECT * FROM utenti WHERE email = '$email'";
    $result = $connect->query($query);

    if ($result->num_rows > 0) {
        // Genero un token univoco per il reset della password
        $token = bin2hex(random_bytes(32));

        // IMPORTANTE - PER CREARE COLONNA RESET_TOKEN LA PRIMA VOLTA !!!!!!!!!!!!!!!!!!!!!!!!!!!
        // $alter_query = "ALTER TABLE utenti ADD reset_token VARCHAR(255) DEFAULT NULL";
        // $connect->query($alter_query);

        // Salvo il token nel database per l'utente
        $query = "UPDATE utenti SET reset_token = '$token' WHERE email = '$email'";
        $connect->query($query);

        // Invio un'email all'utente con il link per reimpostare la password
        $reset_link = "http://localhost/edusogno-esercizio/reset-password.php?token=$token"; // Sostituisci con l'URL corretto

        $mail = new PHPMailer(true); //se true, vengono sollevate eventuali eccezioni utili per il debugging

        try {
            //Impostazioni server
            $mail->SMTPDebug = SMTP::DEBUG_OFF; //Debug mode - OFF TOGLIE I LOG A SCHERMO
            $mail->isSMTP(); //Invio tramite SMTP
            $mail->Host = 'smtp.gmail.com'; //Server SMTP
            $mail->SMTPAuth = true; //Abilita autenticazione SMTP
            $mail->Username = $smtp_mail; //SMTP username
            $mail->Password = $smtp_password; //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //Abilita TLS implicito
            $mail->Port = 587; //Porta SMTP

            //Recipients
            $mail->setFrom($smtp_mail, $smtp_name);
            $mail->addAddress($email, $nome); //Indirizzo destinatario
            $mail->addReplyTo($smtp_mail, $smtp_name); //Indirizzo di risposta

            //Content
            $mail->isHTML(true); //Abilita invio in HTML
            $mail->Subject = 'Reimposta password'; //Oggetto
            $mail->Body = "Ciao, per reimpostare la tua password clicca su questo link: $reset_link"; //Corpo email

            $mail->send();
            $msg = '<h2 class="email_status green">Una messaggio con le istruzioni per reimpostare la password è stato inviato alla tua email</h2>';
        } catch (Exception $e) {
            $msg = "<h2 class='email_status red'>Il messaggio non è stato inviato. Errore: {$mail->ErrorInfo}</h2>";
        }
    } else {
        // L'email non esiste nel database
        $msg = "<h2 class='email_status red'>L'email fornita non è valida.</h2>";
    }
}

$eventController = new EventController($connect);

// Logica per gestire gli eventi quando viene inviato il form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_event'])) {
        // Aggiungi un evento
        $attendees = $_POST['attendees'];
        $nome_evento = $_POST['nome_evento'];
        $data_evento = $_POST['data_evento'];
        $eventController->aggiungiEvento($attendees, $nome_evento, $data_evento);
        // Effettua la ricarica della pagina
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['edit_event'])) {
        // Modifica un evento
        $indice_evento = $_POST['indice_evento'];
        $attendees = $_POST['attendees'];
        $nome_evento = $_POST['nome_evento'];
        $data_evento = $_POST['data_evento'];
        $eventController->modificaEvento($indice_evento, $attendees, $nome_evento, $data_evento);
        // Effettua la ricarica della pagina
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['delete_event'])) {
        // Elimina un evento
        $indice_evento = $_POST['indice_evento'];
        $eventController->eliminaEvento($indice_evento);
        // Effettua la ricarica della pagina
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$eventi_admin = $eventController->getEventi();

$connect->close();

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
        <!-- Stampo messaggio riuscita/errore -->
        <?php echo $msg; ?>

        <form method='post'>
            <h2>Aggiungi un evento</h2>
            <h5><label for="attendees">Partecipanti (e-mail) | Separare gli indirizzi con una virgola seguita da uno spazio (es: mariorossi@gmail.com, lucaverdi@libero.it)</label></h5>
            <input class="nuovo_evento_style" type='text' name='attendees' placeholder='Partecipanti' required><br>
            <h5><label for="nome_evento">Nome evento</label></h5>
            <input class="nuovo_evento_style" type='text' name='nome_evento' placeholder='Nome evento' required><br>
            <h5><label for="data_evento">Data evento</label></h5>
            <input class="nuovo_evento_style" type='date' name='data_evento' required><br>
            <br><br>
            <button type='submit' name='add_event'>Aggiungi evento</button>
        </form>

        <div class="container">
            <?php
            foreach ($eventi_admin as $evento) {
            ?>
                <div class='card'>
                    <h3 style='line-height: 1; min-height:90px'>Nome evento:<br><?php echo $evento->nome_evento; ?></h3>
                    <h4 style="color: black; font-weight:bold;">Partecipanti (e-mail):</h4>
                    <h4 style="font-size:0.9rem; verflow-y: scroll; height:80px; line-height:1.5;"><?php echo $evento->attendees; ?></h4>

                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='indice_evento' value=<?php echo $evento->id; ?>>
                        <h5 class="modifica_label"><label for="attendees">Modifica<br>Partecipanti (e-mail)</label></h5>
                        <textarea name="attendees" placeholder='Nuovi partecipanti' cols="26" rows="10" required><?php echo htmlspecialchars($evento->attendees); ?></textarea>
                        <h5 class="modifica_label"><label for="nome_evento">Modifica<br>Nome evento</label></h5>
                        <input style="width: 95%;" type='text' name='nome_evento' value="<?php echo htmlspecialchars($evento->nome_evento); ?>" placeholder='Nuovo nome evento' required>
                        <h5 class="modifica_label"><label for="data_evento">Modifica<br>Data evento</label></h5>
                        <input style="width: 96.3%;" type='date' name='data_evento' value=<?php echo $evento->data_evento; ?>placeholder='Nuova data evento' required>
                        <br><br><br>
                        <button type='submit' name='edit_event'>Modifica evento</button>
                    </form>

                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='indice_evento' value=<?php echo $evento->id; ?>>
                        <button type='submit' name='delete_event'>Elimina</button>
                    </form>
                </div>
            <?php
            }
            ?>
        </div>
    </main>
</body>

<style>
    .modifica_label {
        line-height: 1;
    }

    .nuovo_evento_style {
        width: 100%;
        height: 2rem;
        border-radius: 20px;
        border: none;
        padding: 0 10px;
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
        width: 80%;
    }

    .container {
        margin-top: 3rem;
        margin-bottom: 10rem;

        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 30px;
    }

    .card {
        width: 22%;
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
        opacity: 0.9;
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