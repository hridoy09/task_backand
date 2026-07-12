<?php

namespace App\Http\Controllers;

use App\Models\CronJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CronJobController extends Controller
{
    public function index()
    {
        $cronJobs = CronJob::running()->get();

        foreach ($cronJobs as $cronJob) {
            if (is_null($cronJob->last_run)) {
                $shouldRun = true;
            } else {
                $lastRun = \Carbon\Carbon::parse($cronJob->last_run);
                $secondsPassed = $lastRun->diffInSeconds(now(), true); // false => keep signed value
                $shouldRun = $secondsPassed >= $cronJob->schedule?->seconds;
            }

            if ($shouldRun) {
                $cronJob->last_run = now();
                $cronJob->save();

                if (method_exists($this, $cronJob->method_name)) {
                    $this->{$cronJob->method_name}();
                } else {
                    Log::warning("Method {$cronJob->method_name} not found in CronJobController");
                }
            }
        }
    }

    public function defaultCron() {}
}
