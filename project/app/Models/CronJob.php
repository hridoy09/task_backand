<?php

namespace App\Models;

use App\Traits\Modeling;
use Illuminate\Database\Eloquent\Model;

class CronJob extends Model
{
    use Modeling;

    protected $casts = [
        'last_run' => 'datetime'
    ];

    public function schedule()
    {
        return $this->belongsTo(CronJobSchedule::class, 'cron_job_schedule_id');
    }

    public function scopePaused($query)
    {
        return $query->where('running', 0);
    }

    public function scopeRunning($query)
    {
        return $query->where('running', 1);
    }

    public function getRunningBadgeAttribute()
    {
        if ($this->running == 1) {
            return getBadge(__('Running'), 'success');
        } else {
            return getBadge(__('Not Running'), 'danger');
        }
    }
}
