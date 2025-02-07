<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\User;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function updateModel(Request $request)
    {
        $validated = $request->validate([
            'model_id' => 'required|exists:ai_models,id'
        ]);

        $user = $request->user();
        $user->update([
            'preferred_model_id' => $validated['model_id']
        ]);

        return response()->json([
            'message' => 'Model preference updated successfully'
        ]);
    }

    public function updateParameters(Request $request)
    {
        $validated = $request->validate([
            'temperature' => 'required|numeric|min:0|max:2',
            'max_length' => 'required|integer|min:100|max:4000',
            'top_p' => 'required|numeric|min:0|max:1'
        ]);

        $user = $request->user();
        $user->update([
            'model_parameters' => $validated
        ]);

        return response()->json([
            'message' => 'Model parameters updated successfully'
        ]);
    }
} 