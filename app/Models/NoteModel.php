<?php

namespace App\Models;

use CodeIgniter\Model;

class NoteModel extends Model
{
    protected $table      = 'notes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'content'];
    protected $useTimestamps = true;
}
