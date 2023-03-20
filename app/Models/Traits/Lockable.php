<?php

namespace App\Models\Traits;

use App\Models\RecordLock;
use Carbon\Carbon;

trait Lockable
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function lock()
    {
        return $this->morphOne(RecordLock::class, 'lockable');
    }

    /**
     * @return bool
     */
    public function getIsLockedAttribute()
    {
        return $this->lock()->exists();
    }

    /**
     * @return integer|null
     */
    public function getLockedByIdAttribute()
    {
        if ($this->isLocked === true) {
            return $this->lock->user_id;
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getLockedByNameAttribute()
    {
        if ($this->isLocked === true) {
            return $this->lock->user->fullname;
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getUnlockedInAttribute()
    {
        if ($this->isLocked === true) {
            $delay = config('record-locks.delay');
            $unlockTime = $this->lock->created_at->addMinutes($delay);

            return $unlockTime->diffInMinutes(Carbon::now());
        }

        return null;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function enableLock($userId)
    {
        if ($this->isLocked === true) {
            if ($this->lock->user_id !== $userId) {
                return false;
            } else {
                $this->updateLock();
            }
        } else {
            $this->addLock($userId);
        }

        return true;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function disableLock($userId)
    {
        if ($this->isLocked === true && $this->lock->user_id !== $userId) {
            return false;
        }

        $this->lock()->delete();

        return true;
    }

    /**
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function addLock($userId)
    {
        $this->lock()->create(['user_id' => $userId]);
    }

    protected function updateLock()
    {
        $this->lock()->update(['created_at' => Carbon::now()]);
    }

}
