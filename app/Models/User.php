<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasPermissions;

class User extends Authenticatable
{
  use HasFactory, Notifiable, HasPermissions;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'profile_picture',
    'rule_id'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function rule()
  {
    return $this->belongsTo(Rule::class);
  }

  public function ledgerEntries()
  {
    return $this->hasMany(LedgerEntry::class, 'created_by');
  }

  public function rateHistories()
  {
    return $this->hasMany(RateHistory::class, 'created_by');
  }

  public function imports()
  {
    return $this->hasMany(Import::class, 'imported_by');
  }
}