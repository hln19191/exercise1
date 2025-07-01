<?php
namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateUser implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $validated;
    protected $user;
    protected $file;
    protected $removePhoto;

    public function __construct(array $validated, ?User $user = null, $file = null, bool $removePhoto = false)
    {
        $this->validated = $validated;
        $this->user = $user;
        $this->file = $file;
        $this->removePhoto = $removePhoto;
    }

    public function handle()
    {
        // Handle password
        if (!empty($this->validated['password'])) {
            $this->validated['password'] = Hash::make($this->validated['password']);
        } else {
            unset($this->validated['password']);
        }

        //update user
        if ($this->user) {
            $user = $this->user;
            $user->update($this->validated);
            $user->syncRoles([$this->validated['role']]);

             // Handle photo removal
            if ($this->removePhoto) {
                try{
                    $user->clearMediaCollection('photos');
                } catch (\Exception $e) {
                    throw new \Exception("Failed to remove photo: " . $e->getMessage());
                }
            }
        } else {
            // create new user
            $user = User::create($this->validated);
            $user->assignRole($this->validated['role']);
        }

         // Handle file upload with Spatie Media Library
        if ($this->file) {
            try{
                $user->clearMediaCollection('photos'); // Clear existing photos if any
                $user->addMedia($this->file)->toMediaCollection('photos');               
            } catch (\Exception $e) {
                return redirect()->route('users.index')->with('error', $e->getMessage());
            }
        }
    }
}