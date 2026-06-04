<?php

namespace App\Models;

use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Builder;
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
        'date_of_birth',
        'mobile',
        'email',
        'nid_number',
        'picture',
        'present_address',
        'permanent_address',
        'religion_id',
        'designation_id',
        'date_of_join_in_ibbl',
        'branch_id',
        'status',
        'employees_id',
        'member_unique_id',
        'serial',
        'deposit_account_number',
        'diposit_account_number',
        'account_opening_date',
        'nominee_name',
        'nominee_father_name',
        'nominee_mother_name',
        'nominee_spouse_name',
        'nominee_relation_id',
        'nominee_phone',
        'nominee_nid_number',
        'nominee_date_of_birth',
        'nominee_picture',
        'nominee_present_address',
        'nominee_permanent_address',
        'introducer_id',
        'user_id',
        'temp_username',
        'temp_password',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_join_in_ibbl' => 'date',
        'account_opening_date' => 'date',
        'nominee_date_of_birth' => 'date',
        'status' => MemberStatus::class,
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

    public function depositInstallmentAmounts()
    {
        return $this->hasMany(DepositInstallmentAmount::class);
    }

    public function memberDeductions()
    {
        return $this->hasMany(MemberDeduction::class);
    }

    public function quards()
    {
        return $this->hasMany(Quard::class);
    }

    public function hpsmOpeningAccounts()
    {
        return $this->hasMany(HpsmOpeningAccount::class, 'member_id');
    }

    /**
     * Members with at least one active deposit, investment, or qard (for bulk deduction generation).
     * Only members in {@see MemberStatus::ACTIVE} are included.
     */
    public function scopeActiveForDeductions(Builder $query): Builder
    {
        return $query->where('status', MemberStatus::ACTIVE)
            ->where(function (Builder $q) {
                $q->whereHas('deposits', function (Builder $d) {
                    $d->where('status', 'active');
                })
                    ->orWhereHas('investments', function (Builder $i) {
                        $i->where('status', 'active');
                    })
                    ->orWhereHas('quards', function (Builder $qd) {
                        $qd->where('status', 'active');
                    });
            });
    }

    /**
     * Office-assigned Member ID (member_unique_ids table).
     * Named distinctly from the member_unique_id attribute to avoid accessor collisions.
     */
    public function memberUniqueIdRecord()
    {
        return $this->hasOne(MemberUniqueId::class, 'member_id');
    }

    /**
     * Legacy alias — maps to member_unique_id (no separate unique_id column in DB).
     */
    public function getUniqueIdAttribute(): ?string
    {
        return $this->member_unique_id;
    }

    /**
     * Prefer office Member ID from member_unique_ids; fall back to column on members.
     */
    public function getMemberUniqueIdAttribute($value): ?string
    {
        if (! $this->exists) {
            return $value;
        }

        $record = $this->relationLoaded('memberUniqueIdRecord')
            ? $this->getRelation('memberUniqueIdRecord')
            : $this->memberUniqueIdRecord()->first();

        if ($record && ! empty($record->member_unique_id)) {
            return $record->member_unique_id;
        }

        return $value;
    }

    // Auto-generate unique ID
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($member) {
            if (empty($member->status)) {
                $member->status = MemberStatus::ACTIVE;
            }
            if (empty($member->member_unique_id)) {
                $member->member_unique_id = static::generateUniqueId();
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
            $uniqueId =  str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('member_unique_id', $uniqueId)->exists());
        
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

