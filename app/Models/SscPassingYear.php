<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SscPassingYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'passing_year_name',
    ];
}
