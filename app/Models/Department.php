<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Department extends Model
{
    use HasFactory;
     use LogsActivity;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];
}