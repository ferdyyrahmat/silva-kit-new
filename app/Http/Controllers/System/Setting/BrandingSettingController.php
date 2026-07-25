<?php

namespace App\Http\Controllers\System\Setting;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class BrandingSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name'         => SystemSetting::getByKey('app_name', config('app.name', 'Silva Kit')),
            'meta_description' => SystemSetting::getByKey('meta_description', 'Silva Kit Enterprise Admin Management Platform'),
            'meta_keywords'    => SystemSetting::getByKey('meta_keywords', 'silva kit, admin dashboard, laravel, enterprise'),
            'meta_author'      => SystemSetting::getByKey('meta_author', 'Silva Team'),
            'app_logo_light'   => SystemSetting::getByKey('app_logo_light', '/images/logo-light.png'),
            'app_logo_dark'    => SystemSetting::getByKey('app_logo_dark', '/images/logo-dark.png'),
            'app_logo_sm'      => SystemSetting::getByKey('app_logo_sm', '/images/logo-sm.png'),
            'app_favicon'      => SystemSetting::getByKey('app_favicon', '/images/favicon.ico'),
        ];

        return view('admin.settings.branding', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name'         => 'required|string|max:100',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'meta_author'      => 'nullable|string|max:100',
            'app_logo_light'   => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'app_logo_dark'    => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'app_logo_sm'      => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'app_favicon'      => 'nullable|file|mimes:ico,png,jpg,jpeg,svg|max:1024',
        ]);

        SystemSetting::setByKey('app_name', $request->app_name);
        SystemSetting::setByKey('meta_description', $request->meta_description ?? '');
        SystemSetting::setByKey('meta_keywords', $request->meta_keywords ?? '');
        SystemSetting::setByKey('meta_author', $request->meta_author ?? '');

        // Handle File Uploads
        $uploadMap = [
            'app_logo_light' => 'logo-light',
            'app_logo_dark'  => 'logo-dark',
            'app_logo_sm'    => 'logo-sm',
            'app_favicon'    => 'favicon',
        ];

        foreach ($uploadMap as $inputName => $filePrefix) {
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $ext = $file->getClientOriginalExtension();
                $fileName = "{$filePrefix}-" . time() . ".{$ext}";
                
                // Save to public storage
                $path = $file->storeAs('branding', $fileName, 'public');
                $publicUrl = "/storage/{$path}";

                SystemSetting::setByKey($inputName, $publicUrl);
            }
        }

        audit_log("Updated Application Branding, Logo & Meta settings", 'update', 'settings');

        return response()->json([
            'success'  => true,
            'message'  => 'Application Branding and Meta settings updated successfully!',
            'redirect' => route('admin.settings.branding.index')
        ]);
    }
}
