<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'father_name',
        'mother_name',
        'spouse_name',
        'mobile',
        'email',
        'nid_number',
        'picture',
        'diposit_account_number',
        'designation_id',
        'date_of_join',
        'branch_id',
        'present_address',
        'permanent_address',
        'unique_id',
        'introducer_id',
        'religion_id',
        'employees_id',
        'nominee_name',
        'nominee_father_name',
        'nominee_mother_name',
        'nominee_spouse_name',
        'nominee_relation_id',
        'nominee_phone',
        'nominee_present_address',
        'nominee_permanent_address',
        'temp_username',
        'temp_password',
        'user_id'
    ];

    protected $casts = [
        'date_of_join' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function introducer()
    {
        return $this->belongsTo(Member::class, 'introducer_id');
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nomineeRelation()
    {
        return $this->belongsTo(Relation::class, 'nominee_relation_id');
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function memberUniqueId()
    {
        return $this->hasOne(MemberUniqueId::class);
    }

    /**
     * Get the member_unique_id from the related member_unique_ids record (for display in forms).
     */
    public function getMemberUniqueIdAttribute()
    {
        $record = $this->memberUniqueId()->first();
        return (is_object($record) && isset($record->member_unique_id)) ? $record->member_unique_id : null;
    }

    // Auto-generate unique ID
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($member) {
            if (empty($member->unique_id)) {
                $member->unique_id = static::generateUniqueId();
            }
            if (empty($member->temp_username)) {
                $member->temp_username = static::generateTempUsername($member->name);
            }
            if (empty($member->temp_password)) {
                $member->temp_password = static::generateTempPassword();
            }
        });
    }

    public static function generateUniqueId()
    {
        do {
            $uniqueId = 'MEM' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('unique_id', $uniqueId)->exists());
        
        return $uniqueId;
    }

    public static function generateTempUsername($name)
    {
        $baseUsername = Str::slug(Str::words($name, 2, ''), '');
        $username = $baseUsername . rand(100, 999);
        
        $counter = 1;
        while (static::where('temp_username', $username)->exists()) {
            $username = $baseUsername . rand(100, 999) . $counter;
            $counter++;
        }
        
        return $username;
    }

    public static function generateTempPassword()
    {
        return Str::random(8);
    }
}

