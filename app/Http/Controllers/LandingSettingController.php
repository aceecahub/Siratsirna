<?php

namespace App\Http\Controllers;

use App\Models\LandingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class LandingSettingController extends Controller
{
    /**
     * Display the landing page editor form in Admin CMS.
     */
    public function edit(): Response
    {
        $allSettings = LandingSetting::all()->pluck('value', 'key')->map(function ($val) {
            $decoded = json_decode($val, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : $val;
        })->toArray();

        return Inertia::render('Admin/LandingSetting/Edit', [
            'settings' => $allSettings,
        ]);
    }

    /**
     * Update landing page settings and handle file uploads.
     */
    public function update(Request $request)
    {
        $data = $request->except(['_token']);

        // Handle single image uploads
        if ($request->hasFile('hero_image_file')) {
            $path = $request->file('hero_image_file')->store('landing', 'public');
            LandingSetting::setValue('hero_image', '/storage/' . $path);
        }

        if ($request->hasFile('kades_photo_file')) {
            $path = $request->file('kades_photo_file')->store('landing', 'public');
            LandingSetting::setValue('kades_photo', '/storage/' . $path);
        }

        if ($request->hasFile('balai_desa_photo_file')) {
            $path = $request->file('balai_desa_photo_file')->store('landing', 'public');
            LandingSetting::setValue('balai_desa_photo', '/storage/' . $path);
        }

        // Process other key-value inputs
        foreach ($data as $key => $val) {
            // Skip file upload temp fields
            if (in_array($key, ['hero_image_file', 'kades_photo_file', 'balai_desa_photo_file'])) {
                continue;
            }

            // Handle nested items or direct string/json
            if ($val !== null) {
                // If it's a JSON string sent from Inertia form
                if (is_string($val) && (str_starts_with($val, '[') || str_starts_with($val, '{'))) {
                    $jsonDecoded = json_decode($val, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        LandingSetting::setValue($key, $jsonDecoded);
                        continue;
                    }
                }
                LandingSetting::setValue($key, $val);
            }
        }

        return back()->with('success', 'Pengaturan & konten Landing Page berhasil diperbarui!');
    }
}
