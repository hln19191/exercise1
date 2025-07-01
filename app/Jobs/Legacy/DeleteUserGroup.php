<?php

namespace App\Jobs;

use App\Models\UserGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * @deprecated Replaced by UserGroupService::delete(). Kept for reference only.
 */

class DeleteUserGroup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userGroup;
    public $deletedBy;

    /**
     * Create a new job instance.
     */
    public function __construct(UserGroup $userGroup, $deletedBy = null)
    {
        $this->userGroup = $userGroup;
        $this->deletedBy = $deletedBy;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {     
        $this->userGroup->deleted_by = $this->deletedBy;
        $this->userGroup->save();
        $this->userGroup->delete(); // this will set deleted_at
  }
}
