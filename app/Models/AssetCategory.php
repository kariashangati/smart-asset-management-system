<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class AssetCategory extends Model
{
    use HasFactory;
     use LogsActivity;

    protected $fillable = [
        'name',
        'description',
    ];
}