<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FileSaver;

class SettingController extends Controller
{
    use FileSaver;

    public function index()
    {
        $defaults = [
            'site_name'       => config('app.name'),
            'logo'            => null,
            'favicon'         => null,
            'contact_email'   => null,
            'footer_text'     => null,
            'default_locale'  => 'en',
            'rtl_enabled'     => '0',
        ];

        $settings = Setting::pluck('value', 'key')->toArray();
        $kv = array_merge($defaults, $settings);

        return view('panel.pages.setting', compact('kv'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'kv.site_name'       => 'nullable|string|max:150',
            'kv.contact_email'   => 'nullable|email',
            'kv.footer_text'     => 'nullable|string|max:500',

            'kv.default_locale'  => 'nullable|in:ar,en',
            'kv.rtl_enabled'     => 'nullable|boolean',

            'logo'               => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'favicon'            => 'nullable|file|mimes:ico,png,jpg,jpeg,webp|max:512',

            'remove_logo'        => 'nullable|boolean',
            'remove_favicon'     => 'nullable|boolean',
        ]);

        $kv = $request->input('kv', []);
        $kv['rtl_enabled'] = $request->boolean('kv.rtl_enabled');

        DB::transaction(function () use ($kv) {
            foreach ($kv as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => is_array($value) ? json_encode($value) : $value]
                );
            }
        });

        // File fields
        $logoSetting = Setting::firstOrCreate(['key' => 'logo'], ['type' => 'file', 'group' => 'ui']);
        $faviconSetting = Setting::firstOrCreate(['key' => 'favicon'], ['type' => 'file', 'group' => 'ui']);

        if ($request->boolean('remove_logo')) {
            $this->removePhysicalAndNullify($logoSetting);
        }
        if ($request->boolean('remove_favicon')) {
            $this->removePhysicalAndNullify($faviconSetting);
        }

        if ($request->hasFile('logo')) {
            $this->upload_file($request->file('logo'), $logoSetting, 'value', 'settings/logo');
        }
        if ($request->hasFile('favicon')) {
            $this->upload_file($request->file('favicon'), $faviconSetting, 'value', 'settings/favicon');
        }

        // 🔥 Clear cache so middleware reads the new settings
        cache()->forget('settings_kv');

        // 🔥 Automatically apply new locale + direction to current session
        $locale = $kv['default_locale'] ?? 'en';
        $dir = $kv['rtl_enabled'] ? 'rtl' : 'ltr';

        session([
            'locale' => $locale,
            'dir'    => $dir,
        ]);

        return back()->with('success', 'Settings updated and applied successfully.');
    }

    private function removePhysicalAndNullify(Setting $setting): void
    {
        $path = $setting->value;
        if (is_string($path)) {
            $abs = public_path($path);
            if (is_file($abs)) {
                @unlink($abs);
            }
        }
        $setting->update(['value' => null]);
    }
}
