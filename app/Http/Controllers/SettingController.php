<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $this->authorize('manage_settings');

        $settings = Setting::all()->groupBy('group');

        return Inertia::render('Settings/Index', [
            'settings' => $settings,
            'groups' => [
                'general' => 'General Settings',
                'notification' => 'Notification Settings',
                'inventory' => 'Inventory Settings',
                'pos' => 'POS Settings',
                'invoice' => 'Invoice Settings'
            ]
        ]);
    }

    public function update(Request $request)
    {
        $this->authorize('manage_settings');

        $validated = $request->validate([
            'settings' => 'array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required'
        ]);

        foreach ($validated['settings'] as $setting) {
            Setting::set($setting['key'], $setting['value']);
        }

        return back()->with('success', 'Settings updated successfully');
    }

    public function defaultSettings()
    {
        return [
            // General Settings
            ['key' => 'company_name', 'value' => 'EasyStock Restaurant', 'type' => 'string', 'group' => 'general'],
            ['key' => 'company_address', 'value' => '', 'type' => 'text', 'group' => 'general'],
            ['key' => 'company_phone', 'value' => '', 'type' => 'string', 'group' => 'general'],
            ['key' => 'company_email', 'value' => '', 'type' => 'string', 'group' => 'general'],
            ['key' => 'tax_rate', 'value' => 10, 'type' => 'number', 'group' => 'general'],

            // Inventory Settings
            ['key' => 'low_stock_threshold', 'value' => 10, 'type' => 'number', 'group' => 'inventory'],
            ['key' => 'enable_stock_alerts', 'value' => true, 'type' => 'boolean', 'group' => 'inventory'],
            ['key' => 'auto_order_enabled', 'value' => false, 'type' => 'boolean', 'group' => 'inventory'],

            // POS Settings
            ['key' => 'default_payment_method', 'value' => 'cash', 'type' => 'string', 'group' => 'pos'],
            ['key' => 'receipt_header', 'value' => '', 'type' => 'text', 'group' => 'pos'],
            ['key' => 'receipt_footer', 'value' => 'Thank you for your business!', 'type' => 'text', 'group' => 'pos'],

            // Notification Settings
            ['key' => 'email_notifications', 'value' => true, 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'stock_alert_email', 'value' => '', 'type' => 'string', 'group' => 'notification'],
            ['key' => 'order_notification_email', 'value' => '', 'type' => 'string', 'group' => 'notification'],
        ];
    }
}
