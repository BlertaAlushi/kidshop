<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class HomeSettingsController extends Controller
{
    protected const HERO_BACKGROUND_KEY = 'home_hero_background_image';
    protected const HERO_TITLE_KEY = 'home_hero_title';
    protected const HERO_DESCRIPTION_KEY = 'home_hero_description';

    /**
     * Show the form for editing the home page settings.
     */
    public function edit()
    {
        $path = Setting::get(self::HERO_BACKGROUND_KEY);

        return Inertia::render('admin/settings/HomeSettings', [
            'hero_background_image' => $path ? Storage::url($path) : null,
            'hero_title' => Setting::get(self::HERO_TITLE_KEY, 'Style that keeps up with them'),
            'hero_description' => Setting::get(
                self::HERO_DESCRIPTION_KEY,
                'Fresh drops for every adventure — from playground mornings to pajama nights. Playful, comfy, made to move.'
            ),
        ]);
    }

    /**
     * Update the home page settings.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'hero_background_image' => 'nullable|image|max:5120',
            'hero_title' => 'nullable|string|max:255',
            'hero_description' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('hero_background_image')) {
            $oldPath = Setting::get(self::HERO_BACKGROUND_KEY);
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }

            $path = $data['hero_background_image']->store('settings', 'public');
            Setting::set(self::HERO_BACKGROUND_KEY, $path);
        }

        Setting::set(self::HERO_TITLE_KEY, $data['hero_title'] ?? null);
        Setting::set(self::HERO_DESCRIPTION_KEY, $data['hero_description'] ?? null);

        return redirect()->route('admin.settings.home.edit')->with('success', 'updated_success');
    }

    /**
     * Remove the home page background image.
     */
    public function destroy()
    {
        $oldPath = Setting::get(self::HERO_BACKGROUND_KEY);
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        Setting::set(self::HERO_BACKGROUND_KEY, null);

        return redirect()->route('admin.settings.home.edit')->with('success', 'removed_success');
    }
}
