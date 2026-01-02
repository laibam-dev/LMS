<?php
<?php
declare(strict_types=1);

class ActivityLogger
{
    public static function log(PDO $db, int $adminId, string $action, ?array $meta = null): bool
    {
        $sql = 'INSERT INTO activity_logs (admin_id, action, meta, created_at) VALUES (:admin_id, :action, :meta, NOW())';
        $stmt = $db->prepare($sql);
        $params = [
            ':admin_id' => $adminId,
            ':action' => $action,
            ':meta' => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null
        ];
        return $stmt->execute($params);
    }

    public static function fetchRecent(PDO $db, int $limit = 10): array
    {
        $stmt = $db->prepare('
            SELECT al.id, al.action, al.meta, al.created_at, u.id AS admin_id, u.name AS admin_name
            FROM activity_logs al
            LEFT JOIN users u ON u.id = al.admin_id
            ORDER BY al.created_at DESC
            LIMIT :lim
        ');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}