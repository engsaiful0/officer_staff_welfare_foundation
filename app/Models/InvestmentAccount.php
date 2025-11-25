<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InvestmentAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_id',
        'account_number',
        'account_opening_date',
        'account_closing_date',
        'opening_balance',
        'current_balance',
        'total_principal_paid',
        'total_interest_received',
        'total_rent_received',
        'total_payments_made',
        'total_installments_paid',
        'installments_paid_count',
        'installments_pending_count',
        'installments_overdue_count',
        'account_status',
        'account_notes',
        'created_by',
        'updated_by',
        'closed_by'
    ];

    protected $casts = [
        'account_opening_date' => 'date',
        'account_closing_date' => 'date',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'total_principal_paid' => 'decimal:2',
        'total_interest_received' => 'decimal:2',
        'total_rent_received' => 'decimal:2',
        'total_payments_made' => 'decimal:2',
        'total_installments_paid' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function accountNumberRecord()
    {
        return $this->hasOne(InvestmentAccountNumber::class, 'investment_account_id');
    }

    // Accessors & Mutators
    public function getAccountBalanceAttribute()
    {
        return $this->current_balance;
    }

    public function getTotalEarningsAttribute()
    {
        return $this->total_interest_received + $this->total_rent_received;
    }

    public function getOutstandingBalanceAttribute()
    {
        if ($this->investment) {
            return $this->investment->principal_amount - $this->total_principal_paid;
        }
        return 0;
    }

    public function getCompletionPercentageAttribute()
    {
        if ($this->investment && $this->investment->principal_amount > 0) {
            return round(($this->total_principal_paid / $this->investment->principal_amount) * 100, 2);
        }
        return 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('account_status', 'active');
    }

    public function scopeClosed($query)
    {
        return $query->where('account_status', 'closed');
    }

    public function scopeMatured($query)
    {
        return $query->where('account_status', 'matured');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('account_status', $status);
    }

    public function scopeByInvestment($query, $investmentId)
    {
        return $query->where('investment_id', $investmentId);
    }

    // Methods
    public function isActive()
    {
        return $this->account_status === 'active';
    }

    public function isClosed()
    {
        return $this->account_status === 'closed';
    }

    public function isMatured()
    {
        return $this->account_status === 'matured';
    }

    public function closeAccount($userId, $notes = null)
    {
        $this->update([
            'account_status' => 'closed',
            'account_closing_date' => Carbon::now()->toDateString(),
            'closed_by' => $userId,
            'account_notes' => $notes ? ($this->account_notes . "\n" . $notes) : $this->account_notes,
        ]);
    }

    public function updateBalance()
    {
        if ($this->investment) {
            // Calculate current balance based on installments
            $paidInstallments = $this->investment->installments()
                ->where('status', 'paid')
                ->get();

            $this->total_principal_paid = $paidInstallments->sum('principal_amount');
            $this->total_rent_received = $paidInstallments->sum('rent');
            $this->total_installments_paid = $paidInstallments->sum('total_amount');
            $this->installments_paid_count = $paidInstallments->count();
            $this->installments_pending_count = $this->investment->installments()
                ->where('status', 'pending')
                ->count();
            $this->installments_overdue_count = $this->investment->installments()
                ->where('status', 'overdue')
                ->count();

            // Current balance = principal - principal paid
            $this->current_balance = $this->investment->principal_amount - $this->total_principal_paid;

            $this->save();
        }
    }

    public function generateAccountNumber()
    {
        if (!$this->account_number) {
            $userId = $this->created_by ?? auth()->id();
            
            // Generate account number using InvestmentAccountNumber model
            // Pass null for investment_account_id initially, then update after saving
            $accountNumberRecord = InvestmentAccountNumber::generateAccountNumber(
                $userId,
                null, // Will be updated after account is saved
                Carbon::now()->year
            );

            $this->account_number = $accountNumberRecord->account_number;
            $this->save();
            
            // Update the account_number_record with the investment_account_id
            if ($this->id) {
                $accountNumberRecord->update([
                    'investment_account_id' => $this->id
                ]);
            }
        }

        return $this->account_number;
    }
}
