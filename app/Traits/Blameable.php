<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth; // Import the Auth facade
use Illuminate\Database\Eloquent\SoftDeletes; // To check for SoftDeletes trait in the model

trait Blameable
{
    /**
     * Boot the trait.
     * Sets up model event listeners to automatically populate _by fields.
     */
    protected static function bootBlameable()
    {
        // Event listener for when a model is being created
        static::creating(function ($model) {
            // Check if 'created_by' attribute exists and an authenticated user is present
            // This will be null for the very first user or unauthenticated actions.
            if ($model->isFillable('created_by') && Auth::check()) {
                $model->created_by = Auth::id();
            }
            // Often, the creator is also the first updater.
            if ($model->isFillable('updated_by') && Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        // Event listener for when a model is being updated
        static::updating(function ($model) {
            // Check if 'updated_by' attribute exists and an authenticated user is present
            if ($model->isFillable('updated_by') && Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        // Event listener for when a model is being deleted (soft or forced)
        static::deleting(function ($model) {
            // Only populate 'deleted_by' if the model uses SoftDeletes and a user is authenticated.
            // This ensures the value is set before the soft-delete actually happens.
            if (in_array(SoftDeletes::class, class_uses_recursive(static::class)) && $model->isFillable('deleted_by') && Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly(); // Save without triggering another 'updating' event
            }
        });

        // Event listener for when a model is being restored (only if SoftDeletes is used)
        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::restoring(function ($model) {
                // Clear the deleted_by when restoring
                if ($model->isFillable('deleted_by')) {
                    $model->deleted_by = null;
                }
            });
        }

    }

    /**
     * Get the user who created this model record.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        // 'users' is the table name, 'created_by' is the foreign key on this model's table
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this model record.
     *
     * @return BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class,'updated_by');
    }

    /**
     * Get the user who soft deleted this model record.
     *
     * @return BelongsTo
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}