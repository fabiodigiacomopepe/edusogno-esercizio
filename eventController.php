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
        // Prepara la query di inserimento
        $stmt = $this->connect->prepare("INSERT INTO eventi (attendees, nome_evento, data_evento) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $attendees, $nome_evento, $data_evento);
        $stmt->execute();
    }

    public function modificaEvento($id, $attendees, $nome_evento, $data_evento)
    {
        // Prepara la query di aggiornamento
        $stmt = $this->connect->prepare("UPDATE eventi SET attendees=?, nome_evento=?, data_evento=? WHERE id=?");
        $stmt->bind_param("sssi", $attendees, $nome_evento, $data_evento, $id);
        $stmt->execute();
    }

    public function eliminaEvento($id)
    {
        // Prepara la query di eliminazione
        $stmt = $this->connect->prepare("DELETE FROM eventi WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    public function getEventi()
    {
        // Esegui la query per ottenere gli eventi dal database
        $result = $this->connect->query("SELECT * FROM eventi");

        // Crea un array per gli eventi
        $eventi_admin = array();

        // Itera sui risultati della query
        while ($row = $result->fetch_assoc()) {
            // Crea un oggetto Event per ogni riga e aggiungilo all'array degli eventi
            $evento = new Event($row['attendees'], $row['nome_evento'], $row['data_evento'], $row['id']);
            $eventi_admin[] = $evento;
        }

        // Restituisci l'elenco degli eventi
        return $eventi_admin;
    }
}
