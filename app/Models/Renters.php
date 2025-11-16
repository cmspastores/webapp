<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Renters
 *
 * This model represents a renter/tenant in the Dormitel Management System. 
 * It stores personal details (names, DOB, email, phone, address, emergency contact)
 * and handles auto-generation of unique renter IDs + full names.
 *
 * @package App\Models
 */
class Renters extends Model
{
    use HasFactory;
    use SoftDeletes; // Enable soft deletes for safe data recovery
    /**
     * Table name used by the model.
     * Explicitly defined in case the naming convention changes.
     *
     * @var string
     */
    protected $table = 'renters'; 

    /**
     * The primary key column for the renters table.
     *
     * @var string
     */
    protected $primaryKey = 'renter_id';

    /**
     * Mass assignable attributes.
     * These can be safely filled when creating/updating renters.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unique_id',          // Auto-generated unique renter code (e.g. A9F3B1C2)
        'first_name',         // Renter’s first name
        'middle_name',        // Renter’s middle name (optional)
        'last_name',          // Renter’s last name
        'full_name',          // Concatenated full name (auto-generated)
        'dob',                // Date of birth
        'email',              // Contact email
        'phone',              // Contact number
        'address',            // Current address
        'emergency_contact',  // Emergency contact information
        'emergency_contact_name',  // Emergency contact information
        'emergency_contact_email', // Emergency contact information
        'check_in_date',
        'check_out_date'
    ];

    /**
     * Booted lifecycle hook.
     *
     * Handles model events:
     *  - When creating: generate unique_id + build full_name automatically.
     *  - When updating: keep full_name in sync with first/middle/last names.
     */
    protected static function booted()
    {
        // Auto-generate fields before saving a new renter
        static::creating(function ($renter) {
            // Generate unique code only if it’s not manually provided
            if (empty($renter->unique_id)) {
                $renter->unique_id = strtoupper(bin2hex(random_bytes(4))); // e.g. "A9F3B1C2"
            }

            // Build the full name from first + middle + last
            $renter->full_name = trim(
                $renter->first_name . ' ' .
                ($renter->middle_name ? $renter->middle_name . ' ' : '') .
                $renter->last_name
            );
        });

        // Keep full_name updated automatically whenever names are changed
        static::updating(function ($renter) {
            $renter->full_name = trim(
                $renter->first_name . ' ' .
                ($renter->middle_name ? $renter->middle_name . ' ' : '') .
                $renter->last_name
            );
        });
    }

    /**
     * Accessor: Get formatted Date of Birth (e.g. Jan 15, 2000).
     *
     * @return string|null
     */
    public function getDobFormattedAttribute()
    {
        return $this->dob 
            ? \Carbon\Carbon::parse($this->dob)->format('M d, Y') 
            : null;
    }

    /**
     * Accessor: Get nicely formatted "created_at" timestamp.
     *
     * Example: Jan 15, 2025 3:45 PM
     *
     * @return string|null
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at 
            ? $this->created_at->timezone(config('app.timezone'))->format('M d, Y g:i A') 
            : null;
    }

    /**
     * Accessor: Get nicely formatted "updated_at" timestamp.
     *
     * Example: Feb 02, 2025 10:20 AM
     *
     * @return string|null
     */
    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at 
            ? $this->updated_at->timezone(config('app.timezone'))->format('M d, Y g:i A') 
            : null;
    }
}
