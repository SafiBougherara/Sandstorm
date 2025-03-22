<?php
namespace Models;

use PDO;

class Model
{
    protected PDO $db;
    protected string $table; // Le nom de la table associée au modèle

    public function __construct(PDO $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    /**
     * Récupérer une ligne par son ID.
     *
     * @param int $id
     * @param bool $fetchAsObject Indique si le retour doit être un objet (true) ou un tableau associatif (false).
     * @return object|array|null
     */
    public function findById(int $id, bool $fetchAsObject = true)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $fetchAsObject ? $stmt->fetch(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer toutes les lignes.
     *
     * @param bool $fetchAsObject Indique si les retours doivent être des objets (true) ou des tableaux associatifs (false).
     * @return array
     */
    public function findAll(bool $fetchAsObject = true): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $fetchAsObject ? $stmt->fetchAll(PDO::FETCH_OBJ) : $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insérer une nouvelle ligne.
     *
     * @param string[] $columns Les colonnes à insérer.
     * @param mixed ...$values Les valeurs à insérer (dans le même ordre que les colonnes).
     * @return int|null L'ID de la nouvelle ligne ou null en cas d'échec.
     */
    public function create(array $columns, ...$values): ?int
    {
        $columnsStr = implode(', ', $columns);
        $placeholders = implode(', ', array_fill(0, count($values), '?'));

        $sql = "INSERT INTO {$this->table} ($columnsStr) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        // INSERT INTO (username,mail)
        if ($stmt->execute($values)) {
            return (int)$this->db->lastInsertId();
        }

        return null;
    }

    /**
     * Mettre à jour une ligne par son ID.
     *
     * @param int $id
     * @param string[] $columns Les colonnes à mettre à jour.
     * @param mixed ...$values Les valeurs à mettre à jour (dans le même ordre que les colonnes).
     * @return bool
     */
    public function update(int $id, array $columns, ...$values): bool
    {
        {
            $setPart = implode(', ', array_map(fn($col) => "$col = ?", $columns));
            $sql = "UPDATE {$this->table} SET $setPart WHERE id = ?";
            $stmt = $this->db->prepare($sql);
    
            return $stmt->execute([...$values, $id]);
        }
    }

    /**
     * Supprimer une ligne par son ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }


    public function get_last_id(): int
    {
        return (int)$this->db->lastInsertId();
    }
}