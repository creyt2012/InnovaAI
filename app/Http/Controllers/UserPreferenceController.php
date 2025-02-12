<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'required|in:light,dark',
            'language' => 'required|in:vi,en',
            'font_size' => 'required|in:small,medium,large',
            'notification_enabled' => 'boolean',
            'keyboard_shortcuts' => 'array',
            'display_mode' => 'required|in:default,compact,comfortable'
        ]);

        $preference = UserPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            $validated
        );

        return response()->json([
            'message' => 'Preferences updated successfully',
            'preferences' => $preference
        ]);
    }

    public function show()
    {
        $preferences = UserPreference::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'theme' => 'light',
                'language' => 'vi',
                'font_size' => 'medium',
                'notification_enabled' => true,
                'keyboard_shortcuts' => null,
                'display_mode' => 'default'
            ]
        );

        return response()->json($preferences);
    }
} 