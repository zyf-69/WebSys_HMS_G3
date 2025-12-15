<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'username',
        'email',
        'password',
        'first_name',
        'middle_name',
        'last_name',
        'address',
        'role_id',
        'status',
    ];

    protected $useTimestamps = true;
}
