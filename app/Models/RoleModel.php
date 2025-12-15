<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table      = 'roles';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'display_name',
        'description',
        'level',
        'is_active',
    ];

    protected $useTimestamps = false;
}
