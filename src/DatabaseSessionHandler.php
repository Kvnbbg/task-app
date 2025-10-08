<?php
class DatabaseSessionHandler implements SessionHandlerInterface {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function open($savePath, $sessionName): bool {
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read($id): string {
        $stmt = $this->pdo->prepare("SELECT data FROM sessions WHERE session_id = :id");
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['data'] : '';
        }
        return '';
    }

    public function write($id, $data): bool {
        $stmt = $this->pdo->prepare(
            "REPLACE INTO sessions (session_id, data, last_updated) VALUES (:id, :data, NOW())"
        );
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':data', $data);
        return $stmt->execute();
    }

    public function destroy($id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE session_id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function gc($maxlifetime): int|false {
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE last_updated < DATE_SUB(NOW(), INTERVAL :maxlifetime SECOND)");
        $stmt->bindParam(':maxlifetime', $maxlifetime, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
?>