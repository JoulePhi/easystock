<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Cache settings
        if (!app()->runningInConsole()) {
            $settings = Cache::rememberForever('settings', function () {
                return Setting::all()
                    ->keyBy('key')
                    ->map(function ($setting) {
                        return $setting->value;
                    })
                    ->toArray();
            });

            config()->set('settings', $settings);
        }
    }
}
