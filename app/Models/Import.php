<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'file_path',
        'type',
        'imported_by',
        'imported_at',
        'rows_imported',
        'errors'
    ];

    protected $casts = [
        'imported_at' => 'datetime',
        'errors' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function importedBy()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    // Accessors
    public function getHasErrorsAttribute()
    {
        return !empty($this->errors);
    }

    public function getErrorCountAttribute()
    {
        return is_array($this->errors) ? count($this->errors) : 0;
    }
}
