<?php

require_once __DIR__ . '/config.php';

class Event
{
    public $attendees;
    public $nome_evento;
    public $data_evento;

    public $id;

    public function __construct($attendees, $nome_evento, $data_evento, ...$id)
    {
        $this->attendees = $attendees;
        $this->nome_evento = $nome_evento;
        $this->data_evento = $data_evento;

        $this->id = $id[0];
    }
}


class EventController
{
    private $connect;

    public function __construct($connect)
    {
        $this->connect = $connect;
    }

    public function aggiungiEvento($attendees, $nome_evento, $data_evento)
    {
        // Preparo la query di inserimento
        $stmt = $this->connect->prepare("INSERT INTO eventi (attendees, nome_evento, data_evento) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $attendees, $nome_evento, $data_evento);
        $stmt->execute();
    }

    public function modificaEvento($attendees, $nome_evento, $data_evento, $id)
    {
        $stmt = $this->connect->prepare("SELECT * FROM eventi WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $evento = $result->fetch_assoc();

        // Preparo la query di aggiornamento
        $stmt2 = $this->connect->prepare("UPDATE eventi SET attendees=?, nome_evento=?, data_evento=? WHERE id=?");
        $stmt2->bind_param("sssi", $attendees, $nome_evento, $data_evento, $id);
        $stmt2->execute();

        return $evento;
    }

    public function eliminaEvento($id)
    {
        $stmt = $this->connect->prepare("SELECT * FROM eventi WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $evento = $result->fetch_assoc();

        // Preparo la query di eliminazione
        $stmt2 = $this->connect->prepare("DELETE FROM eventi WHERE id=?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();

        return $evento;
    }

    public function getEventi()
    {
        // Eseguo la query per ottenere gli eventi dal database
        $result = $this->connect->query("SELECT * FROM eventi");

        // Creo un array per gli eventi
        $eventi_admin = array();

        // Itero sui risultati della query
        while ($row = $result->fetch_assoc()) {
            // Creo un oggetto Event per ogni riga e lo aggiungo all'array degli eventi
            $evento = new Event($row['attendees'], $row['nome_evento'], $row['data_evento'], $row['id']);
            $eventi_admin[] = $evento;
        }

        // Restituisco l'elenco degli eventi
        return $eventi_admin;
    }
}
