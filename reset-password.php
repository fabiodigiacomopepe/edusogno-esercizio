<?php
session_start();

require_once __DIR__ . '/config.php';

// Controlla se il token è stato fornito nell'URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Mostra il form per reimpostare la password solo se il token è valido
    $query = "SELECT * FROM utenti WHERE reset_token = '$token'";
    $result = $connect->query($query);

    if ($result->num_rows > 0) {
        // Token valido, mostra il form per reimpostare la password
        if (isset($_POST['reset_password'])) {
            // Ottieni la nuova password fornita dall'utente
            $new_password = $_POST['new_password'];

            // Cifra la nuova password (sostituisci con l'algoritmo di hashing sicuro che preferisci)
            $hashed_password = md5($new_password);

            // Aggiorna la password nel database
            $query = "UPDATE utenti SET password = '$hashed_password', reset_token = NULL WHERE reset_token = '$token'";
            $connect->query($query);

            // Mostra un messaggio di successo all'utente
            echo "La tua password è stata reimpostata con successo.";
        }
?>

        <!-- Form per reimpostare la password -->
        <form method="post">
            <input type="password" name="new_password" placeholder="Nuova password" required>
            <button type="submit" name="reset_password">Reimposta password</button>
        </form>

<?php
    } else {
        // Token non valido
        echo "Il token fornito non è valido.";
    }
} else {
    // Nessun token fornito nell'URL
    echo "Token non fornito.";
}
?>