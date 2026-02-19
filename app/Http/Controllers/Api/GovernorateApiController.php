<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use Illuminate\Http\JsonResponse;

class GovernorateApiController extends Controller
{
    /**
     * Display a listing of all governorates with shipping fees.
     */
    public function index(): JsonResponse
    {
        $governorates = Governorate::orderBy('name')->get();

        // Format governorates data as key-value pairs
        $formattedData = [];
        foreach ($governorates as $governorate) {
            $formattedData[$governorate->name] = (float) $governorate->shipping_fee;
        }

        return response()->json([
            'success' => true,
            'data' => $formattedData,
            'count' => count($formattedData)
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
