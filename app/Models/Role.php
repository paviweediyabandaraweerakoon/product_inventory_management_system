<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Role extends Model
{
    // Table name එක property එකක් විදියට තියාගන්න එක ලේසියි
    protected string $table = 'roles';

    /**
     * සියලුම Roles ලබා ගැනීම
     */
    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY id ASC";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ID එක අනුව Role එකක් සෙවීම
     */
    public function find(int $id): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        return $this->query($sql, ['id' => $id])->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Active roles විතරක් ගන්න ඕනේ නම් මේ වගේ method එකක් දාන්න පුළුවන්
     */
    public function getActiveRoles(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active'";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}