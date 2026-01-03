<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * CancellationRequest Model
 * Represents a customer's request to cancel their booking
 * Includes refund bank details and cancellation reason
 */
class CancellationRequest extends Model
{
    protected $primaryKey = 'request_id';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'booking_id',
        'customer_id',
        'reason_type',
        'reason_details',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'proof_document_url',
        'status',
        'processed_by_staff_id',
        'processed_at',
        'staff_notes',
        'refund_amount',
        'refund_reference',
        'refunded_at',
    ];

    /**
     * The attributes that should be cast to native types
     */
    protected $casts = [
        'processed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'refund_amount' => 'decimal:2',
    ];

    /**
     * Get human-readable reason types
     */
    public static function getReasonTypes(): array
    {
        return [
            'change_of_plans' => 'Change of Plans',
            'found_alternative' => 'Found Alternative Transportation',
            'financial_reasons' => 'Financial Reasons',
            'emergency' => 'Emergency / Medical',
            'vehicle_issue' => 'Issue with Vehicle',
            'service_issue' => 'Service Quality Issue',
            'other' => 'Other',
        ];
    }

    /**
     * Get the human-readable reason type
     */
    public function getReasonLabelAttribute(): string
    {
        return self::getReasonTypes()[$this->reason_type] ?? $this->reason_type;
    }

    /**
     * Accessor: Get the full URL for the proof document
     */
    public function getProofDocumentUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return Storage::url($value);
    }

    /**
     * Get the booking associated with this cancellation request
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the customer who made this cancellation request
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the staff member who processed this request
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'processed_by_staff_id', 'staff_id');
    }
}
