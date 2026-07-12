<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CronJob;
use App\Models\CronJobSchedule;
use Illuminate\Http\Request;

class CronJobSettingController extends Controller
{
    public function list()
    {
        goIfUserCan('view-cronjobs');

        $title = __('Cron Jobs');
        $cronJobs = CronJob::paginate();
        $cronJobSchedules = CronJobSchedule::active()->get();

        return view('admin.setting.cronjob.list', compact('title', 'cronJobs', 'cronJobSchedules'));
    }

    public function save(Request $request, $id) 
    {
        goIfUserCan('save-cronjobs');

        $request->validate([
            'name' => 'required',
            'cron_job_schedule_id' => 'required|exists:cron_job_schedules,id'
        ]);

        $cronJob                       = CronJob::findOrFail($id);
        $cronJob->name                 = $request->name;
        $cronJob->cron_job_schedule_id = $request->cron_job_schedule_id;
        $cronJob->save();

        return back()->withSuccess(__('Cron job saved successfully'));
    }

    public function pause($id)
    {
        goIfUserCan('manage-cronjobs');

        $cronJob = CronJob::running()->findOrFail($id);
        $cronJob->running = 0;
        $cronJob->save();

        return back()->withSuccess(__('Cron Job stoped running'));
    }
    
    public function running($id)
    {
        goIfUserCan('manage-cronjobs');

        $cronJob = CronJob::paused()->findOrFail($id);
        $cronJob->running = 1;
        $cronJob->save();

        return back()->withSuccess(__('Cron Job started running'));
    }
}
