<?php
namespace Models;

use PDO;

class UserModel extends Model
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'users');
    }

    /**
     * Get user by email
     */
    public function getUserByEmail(string $email, bool $fetchAsObject = true)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE mail = :mail");
        $stmt->execute([':mail' => $email]);
        return $fetchAsObject ? $stmt->fetch(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user by ID
     */
    public function getUserById(int $id, bool $fetchAsObject = true)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $fetchAsObject ? $stmt->fetch(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all users
     */
    public function getAllUsers()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Register a new user
     */
    public function register(string $username, string $email, string $password): ?int
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("INSERT INTO {$this->table} (username, mail, pass) VALUES (:username, :email, :password)");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_BCRYPT)
            ]);

            $userId = $this->db->lastInsertId();
            $this->db->commit();

            return $userId;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Authenticate user
     */
    public function authenticate(string $email, string $password): ?int
    {
        $user = $this->getUserByEmail($email);
        
        if ($user && password_verify($password, $user->pass)) {
            return $user->id;
        }
        
        return null;
    }

    /**
     * Update user profile
     */
    public function updateProfile(int $userId, array $data): bool
    {
        try {
            $this->db->beginTransaction();

            $updates = [];
            $params = [':id' => $userId];

            foreach ($data as $key => $value) {
                if ($key !== 'id' && $value !== null) {
                    $updates[] = "{$key} = :{$key}";
                    $params[":{$key}"] = $value;
                }
            }

            if (empty($updates)) {
                return false;
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);

            $this->db->commit();
            return $result;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Change user password
     */
    public function changePassword(int $userId, string $newPassword): bool
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET pass = :password WHERE id = :id");
            return $stmt->execute([
                ':password' => password_hash($newPassword, PASSWORD_BCRYPT),
                ':id' => $userId
            ]);
        } catch (\PDOException $e) {
            throw $e;
        }
    }
}
