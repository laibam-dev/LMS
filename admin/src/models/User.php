<?php
<?php
declare(strict_types=1);

class User
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_INSTRUCTOR = 'instructor';
    public const ROLE_STUDENT = 'student';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_SUSPENDED = 'suspended';

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, role, status, created_at, updated_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('
            INSERT INTO users (name, email, password_hash, role, status, created_at, updated_at)
            VALUES (:name, :email, :password_hash, :role, :status, NOW(), NOW())
        ');
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password_hash' => $passwordHash,
            ':role' => $data['role'] ?? self::ROLE_STUDENT,
            ':status' => $data['status'] ?? self::STATUS_ACTIVE,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['name'])) {
            $fields[] = 'name = :name';
            $params[':name'] = $data['name'];
        }
        if (isset($data['email'])) {
            $fields[] = 'email = :email';
            $params[':email'] = $data['email'];
        }
        if (!empty($data['password'])) {
            $fields[] = 'password_hash = :password_hash';
            $params[':password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (isset($data['role'])) {
            $fields[] = 'role = :role';
            $params[':role'] = $data['role'];
        }
        if (isset($data['status'])) {
            $fields[] = 'status = :status';
            $params[':status'] = $data['status'];
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = 'updated_at = NOW()';
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get list of users with optional search and pagination.
     */
    public function list(int $limit = 50, int $offset = 0, ?string $search = null): array
    {
        if ($search) {
            $stmt = $this->db->prepare('
                SELECT id, name, email, role, status, created_at, updated_at
                FROM users
                WHERE name LIKE :q OR email LIKE :q
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset
            ');
            $stmt->bindValue(':q', '%' . $search . '%', PDO::PARAM_STR);
        } else {
            $stmt = $this->db->prepare('
                SELECT id, name, email, role, status, created_at, updated_at
                FROM users
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset
            ');
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function verifyPassword(string $email, string $password): bool
    {
        $stmt = $this->db->prepare('SELECT password_hash, status FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }
        if ($row['status'] !== self::STATUS_ACTIVE) {
            return false;
        }
        return password_verify($password, $row['password_hash']);
    }
}