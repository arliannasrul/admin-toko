<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_phone',
        'customer_name',
        'subject',
        'description',
        'status', // open, in_progress, resolved
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the order associated with the complaint.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
