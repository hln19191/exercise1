<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsModelActivity;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGroup extends Model
{
    use LogsModelActivity;
    use Blameable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
