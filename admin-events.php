<?php
// Riapro sessione per ricevere email
session_start();

// Importo PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

// Richiamo le credenziali dal file config.php e credential.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/credential.php';

// Richiamo l'eventController
require_once __DIR__ . '/eventController.php';

// Setto messaggio vuoto di default
$msg = "";

// Ottengo l'email dell'utente dalla variabile di sessione
$email = $_SESSION['email'];

// Ottengo il valore di admin in booleano
$sql_user = "SELECT admin FROM utenti WHERE email = '$email'";
$result_user = mysqli_query($connect, $sql_user);
$row_user = mysqli_fetch_array($result_user);
$admin = boolval($row_user['admin']);

// Verifico se l'utente è un admin
if ($admin !== true) {
    header('Location: index.php'); // Reindirizzamento alla pagina di login se l'utente non è un admin
    exit();
}

$eventController = new EventController($connect);

// Logica per gestire gli eventi quando viene inviato il form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_event'])) {
        // Aggiungo un evento
        $attendees = $_POST['attendees'];
        $nome_evento = $_POST['nome_evento'];
        $data_evento = $_POST['data_evento'];
        $eventController->aggiungiEvento($attendees, $nome_evento, $data_evento);

        $arrayAttendees = explode(', ', $attendees);

        foreach ($arrayAttendees as $partecipante) {
            $mail = new PHPMailer(true);

            try {
                // Impostazioni server
                $mail->SMTPDebug = SMTP::DEBUG_OFF;
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $smtp_mail;
                $mail->Password = $smtp_password;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom($smtp_mail, $smtp_name);
                $mail->addAddress($partecipante);
                $mail->addReplyTo($smtp_mail, $smtp_name);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Sei stato aggiunto a un nuovo evento';
                $mail->Body = "Ciao, sei stato appena aggiunto all'evento $nome_evento che si svolgera' il $data_evento.";

                $mail->send();
                $msg = '<h2 class="password_status green">Notifica email inviata correttamente ai partecipanti</h2>';
            } catch (Exception $e) {
                $msg = "<h2 class='password_status red'>Il messaggio non è stato inviato. Errore: {$mail->ErrorInfo}</h2>";
            }
        }

        $_SESSION['msg'] = $msg; // Salvo il valore di $msg nella sessione

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['edit_event'])) {
        // Modifico un evento
        $attendees = $_POST['attendees'];
        $nome_evento = $_POST['nome_evento'];
        $data_evento = $_POST['data_evento'];
        $indice_evento = $_POST['indice_evento'];
        $evento_vecchio = $eventController->modificaEvento($attendees, $nome_evento, $data_evento, $indice_evento);

        $nome_vecchio = $evento_vecchio['nome_evento'];
        $data_vecchia_orario = $evento_vecchio['data_evento'];
        $data_vecchia = date("Y-m-d", strtotime($data_vecchia_orario));

        $arrayAttendees = explode(', ', $attendees);

        foreach ($arrayAttendees as $partecipante) {
            $mail = new PHPMailer(true);

            try {
                // Impostazioni server
                $mail->SMTPDebug = SMTP::DEBUG_OFF;
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $smtp_mail;
                $mail->Password = $smtp_password;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom($smtp_mail, $smtp_name);
                $mail->addAddress($partecipante);
                $mail->addReplyTo($smtp_mail, $smtp_name);

                // Content
                $mail->isHTML(true);
                $mail->Subject = "Un evento a cui partecipi e' stato modificato";
                $mail->Body = "Ciao, un evento a cui partecipi e' stato modificato. Nome precedente evento: $nome_vecchio. Data precedente dell'evento: $data_vecchia. Nome nuovo evento: $nome_evento. Data nuova dell'evento: $data_evento.";

                $mail->send();
                $msg = '<h2 class="password_status green">Notifica email inviata correttamente ai partecipanti</h2>';
            } catch (Exception $e) {
                $msg = "<h2 class='password_status red'>Il messaggio non è stato inviato. Errore: {$mail->ErrorInfo}</h2>";
            }
        }

        $_SESSION['msg'] = $msg; // Salvo il valore di $msg nella sessione

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['delete_event'])) {
        // Elimino un evento
        $attendees = $_POST['attendees'];
        $nome_evento = $_POST['nome_evento'];
        $data_evento = $_POST['data_evento'];
        $indice_evento = $_POST['indice_evento'];
        $evento_vecchio = $eventController->eliminaEvento($indice_evento);

        $nome_vecchio = $evento_vecchio['nome_evento'];
        $data_vecchia_orario = $evento_vecchio['data_evento'];
        $data_vecchia = date("Y-m-d", strtotime($data_vecchia_orario));

        $arrayAttendees = explode(', ', $attendees);

        foreach ($arrayAttendees as $partecipante) {
            $mail = new PHPMailer(true);

            try {
                // Impostazioni server
                $mail->SMTPDebug = SMTP::DEBUG_OFF;
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $smtp_mail;
                $mail->Password = $smtp_password;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom($smtp_mail, $smtp_name);
                $mail->addAddress($partecipante);
                $mail->addReplyTo($smtp_mail, $smtp_name);

                // Content
                $mail->isHTML(true);
                $mail->Subject = "Un evento a cui partecipi e' stato eliminato";
                $mail->Body = "Ciao, un evento a cui partecipi e' stato eliminato. Nome evento eleminato: $nome_vecchio. Data evento eliminato: $data_vecchia.";

                $mail->send();
                $msg = '<h2 class="password_status green">Notifica email inviata correttamente ai partecipanti</h2>';
            } catch (Exception $e) {
                $msg = "<h2 class='password_status red'>Il messaggio non è stato inviato. Errore: {$mail->ErrorInfo}</h2>";
            }
        }

        $_SESSION['msg'] = $msg; // Salvo il valore di $msg nella sessione

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Dopo il reindirizzamento, per accedere al valore di $msg dalla sessione
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']); // Rimuovo il valore di $msg dalla sessione
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
    <!-- Importo font awesome per icona back -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <title>Edusogno</title>
    <title>Edusogno</title>
</head>

<body>
    <header>
        <img src="./assets/img/logo.svg" alt="logo">
    </header>

    <main>
        <!-- Stampo messaggio riuscita/errore -->
        <?php echo $msg; ?>

        <h1><i class="fa-solid fa-left-long"></i><a style="margin-left: 20px;" href='events.php'>Torna indietro</a></h1>

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
                    <h3>Nome evento: <?php echo $evento->nome_evento; ?></h3>
                    <h4 style="color: black; font-weight:bold;">Partecipanti (e-mail):</h4>
                    <h4 style="font-size:0.9rem;"><?php echo $evento->attendees; ?></h4>
                    <h4 style="color: black; font-weight:bold;">Data evento:</h4>
                    <h4 style="font-size:0.9rem;"><?php echo $evento->data_evento; ?></h4>
                    <br>

                    <form method='post' style='display:inline;'>
                        <h3>MODIFICA</h3>
                        <input type='hidden' name='indice_evento' value=<?php echo $evento->id; ?>>
                        <h5 class="modifica_label"><label for="nome_evento">Nome evento</label></h5>
                        <input style="width: 98%;" type='text' name='nome_evento' value="<?php echo htmlspecialchars($evento->nome_evento); ?>" placeholder='Nuovo nome evento' required>
                        <h5 class="modifica_label"><label for="attendees">Partecipanti (e-mail) | Separare gli indirizzi con una virgola seguita da uno spazio<br>(es: mariorossi@gmail.com, lucaverdi@libero.it)</label></h5>
                        <textarea name="attendees" placeholder='Nuovi partecipanti' cols="118" rows="7" required><?php echo htmlspecialchars($evento->attendees); ?></textarea>
                        <h5 class="modifica_label"><label for="data_evento">Data evento</label></h5>
                        <input style="width: 98%;" type='date' name='data_evento' value=<?php echo $evento->data_evento; ?>placeholder='Nuova data evento' required>
                        <button style="margin-top: 50px;" type='submit' name='edit_event'>Modifica evento</button>
                    </form>

                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='indice_evento' value=<?php echo $evento->id; ?>>
                        <input type='hidden' name='nome_evento' value="<?php echo htmlspecialchars($evento->nome_evento); ?>" required>
                        <input type="hidden" name="attendees" cols="118" rows="7" value="<?php echo htmlspecialchars($evento->attendees); ?>" required>
                        <input type='hidden' name='data_evento' value=<?php echo $evento->data_evento; ?>placeholder='Nuova data evento' required>
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
        width: 100%;
        background-color: white;
        border: 2px solid #2b5486;
        border-radius: 20px;
        padding: 0.5rem 2rem;
        font-size: 1.5rem;
        line-height: 0.5;
        margin-bottom: 2rem;
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