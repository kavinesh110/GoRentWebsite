<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SupportTicket Model
 * Represents customer service requests and support tickets
 * Links to bookings, cars, and maintenance records for issue tracking
 */
class SupportTicket extends Model
{
    protected $primaryKey = 'ticket_id';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'customer_id',
        'booking_id',
        'car_id',
        'maintenance_record_id',
        'flagged_for_maintenance',
        'name',
        'email',
        'phone',
        'category',
        'subject',
        'description',
        'status',
        'staff_response',
        'assigned_to',
        'resolved_at',
        'closed_at',
    ];

    /**
     * The attributes that should be cast to native types
     */
    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'flagged_for_maintenance' => 'boolean',
    ];

    /**
     * Get the customer who submitted this ticket
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the booking associated with this ticket (if any)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the car associated with this ticket (if any)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id', 'id');
    }

    /**
     * Get the maintenance record linked to this ticket (if any)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function maintenanceRecord(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRecord::class, 'maintenance_record_id', 'maintenance_id');
    }

    /**
     * Get the staff member assigned to this ticket
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_to', 'staff_id');
    }

    /**
     * Get available categories for support tickets (car issues only)
     * @return array
     */
    public static function getCategories(): array
    {
        return [
            'cleanliness' => 'Cleanliness',
            'lacking_facility' => 'Lacking Facility',
            'bluetooth' => 'Bluetooth',
            'engine' => 'Engine',
            'others' => 'Others',
        ];
    }

    /**
     * Get available statuses for support tickets
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ];
    }
}
