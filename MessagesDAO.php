<?php

include_once ('model/MessageFactory.php');
class MessagesDAO {

  private $connection;
  private $messageFactory;

  function __construct($connection, $messageFactory) {
    $this->connection = $connection;
    $this->messageFactory = $messageFactory;
  }

  function getMessageById($id) {
    $id;
    $message;
    $stmt = $this->connection->prepare("SELECT messageId, messageData FROM messages WHERE messageId=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->bind_result($id, $message);
    $isSuccessful = $stmt->fetch();
    $stmt->close();
    if ($isSuccessful) {
      return $this->messageFactory->create($id, $message);
    } else {
      return null;
    }
  }

  function getAllMessages() {
    $messages = array();
    $result = $this->connection->query("SELECT messageId, messageData FROM messages");
    while ($row = $result->fetch_assoc()) {
      $messages[] = $this->messageFactory->create($row['messageId'], $row["messageData"]);
    }
    if ($result) {
      return $messages;
    } else {
      return null;
    }
  }

  function getMessagesByIdRange($from, $to) {
    $id;
    $message;
    $messages = array();
    $stmt = $this->connection->prepare("SELECT messageId, messageData FROM messages WHERE messageId BETWEEN ? AND ?");
    $stmt->bind_param("ss", $from, $to);
    $stmt->execute();
    $isSuccessful = $stmt->bind_result($id, $message);
    while ($stmt->fetch()) {
      $messages[] = $this->messageFactory->create($id, $message);
    }
    $stmt->close();
    if ($isSuccessful) {
      return $messages;
    } else {
      return null;
    }
  }

  function updateMessage($id, $message) {
    $stmt = $this->connection->prepare("UPDATE messages SET messageData=? WHERE messageId=?");
    $stmt->bind_param("ss", $message, $id);
    $stmt->execute();
    $totalAffected = $stmt->affected_rows;
    $stmt->close();
    return $totalAffected > 0;
  }

  function insertMessage($id, $message) {
    $stmt = $this->connection->prepare("INSERT INTO messages (messageId, messageData) VALUES (?, ?)");
    $stmt->bind_param("ss", $id, $message);
    $stmt->execute();
    $totalAffected = $stmt->affected_rows;
    $stmt->close();
    return $totalAffected > 0;
  }

  function deleteMessageById($id) {
    $stmt = $this->connection->prepare("DELETE FROM messages WHERE messageId = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $totalAffected = $stmt->affected_rows;
    $stmt->close();
    return $totalAffected;
  }

  function getMaxId() {
    $result = $this->connection->query("SELECT MAX(messageId) as 'maxid' FROM messages");
    $row = $result->fetch_assoc();
    return $row["maxid"];
  }

}
