<?php

namespace App\Observers;

use App\Models\Province;
use App\Settings\AppSettings;

class ProvinceObserver
{
    public $settings;
    public function __construct(AppSettings $settings){
        $this->settings = $settings;
    }
    /**
     * Handle the Province "created" event.
     */
    public function created(Province $province): void
    {
        $this->settings->provinces_last_updated_at = now();
        
        $this->settings->save();
        
    }

    /**
     * Handle the Province "updated" event.
     */
    public function updated(Province $province): void
    {
        $this->settings->provinces_last_updated_at = now();
        
        $this->settings->save();
    }

    /**
     * Handle the Province "deleted" event.
     */
    public function deleted(Province $province): void
    {
        $this->settings->provinces_last_updated_at = now();
        
        $this->settings->save();
    }

    /**
     * Handle the Province "restored" event.
     */
    public function restored(Province $province): void
    {
        $this->settings->provinces_last_updated_at = now();
        
        $this->settings->save();
    }

    /**
     * Handle the Province "force deleted" event.
     */
    public function forceDeleted(Province $province): void
    {
       $this->settings->provinces_last_updated_at = now();
        
        $this->settings->save();
    }
}
