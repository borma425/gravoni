<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGovernorateRequest;
use App\Models\Governorate;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $governorates = Governorate::orderBy('name')->paginate(20);
        $transferConfigured = !empty(config('plugins.cashup_cash.transfer_base_url')) && !empty(config('plugins.cashup_cash.transfer_api_key'));
        $phoneNumbers = [];
        $accountBalance = '0.00';
        $balanceMessage = '';
        if ($transferConfigured) {
            $transferController = app(TransferMoneyController::class);
            $phoneNumbers = $transferController->getPhoneNumbers();
            $balanceResult = $transferController->getAllBalance();
            $accountBalance = $balanceResult['balance'] ?? '0.00';
            $balanceMessage = $balanceResult['message'] ?? '';
        }
        return view('governorates.index', compact(
            'governorates',
            'phoneNumbers',
            'accountBalance',
            'balanceMessage',
            'transferConfigured'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('governorates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGovernorateRequest $request)
    {
        Governorate::create($request->validated());

        return redirect()->route('governorates.index')
            ->with('success', 'تم إضافة المحافظة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Governorate $governorate)
    {
        return view('governorates.show', compact('governorate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Governorate $governorate)
    {
        return view('governorates.edit', compact('governorate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreGovernorateRequest $request, Governorate $governorate)
    {
        $governorate->update($request->validated());

        return redirect()->route('governorates.index')
            ->with('success', 'تم تحديث المحافظة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Governorate $governorate)
    {
        $governorate->delete();

        return redirect()->route('governorates.index')
            ->with('success', 'تم حذف المحافظة بنجاح');
    }
}
