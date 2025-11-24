<?php

namespace App\Observers;

use App\Models\Milestone;
use App\Models\ActivityLog;

class MilestoneObserver
{
    /**
     * Handle the Milestone "created" event.
     */
    public function created(Milestone $milestone): void
    {
        ActivityLog::create([
            'user_id' => $milestone->user_id,
            'action' => 'created_milestone',
            'description' => "Added a new milestone: {$milestone->text}",
            'subject_type' => Milestone::class,
            'subject_id' => $milestone->id,
            'url' => route('milestones.index'),
        ]);
    }

    /**
     * Handle the Milestone "updated" event.
     */
    public function updated(Milestone $milestone): void
    {
        //
    }

    /**
     * Handle the Milestone "deleted" event.
     */
    public function deleted(Milestone $milestone): void
    {
        ActivityLog::create([
            'user_id' => $milestone->user_id,
            'action' => 'deleted_milestone',
            'description' => "Deleted milestone: {$milestone->text}",
            'subject_type' => Milestone::class,
            'subject_id' => $milestone->id,
            'url' => route('milestones.index'),
        ]);
    }
}
