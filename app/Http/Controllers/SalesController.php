<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Http\Requests\SaleRequest;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

        $sales = Sale::whereBetween('date', [$startDate, $endDate])->orderBy('date')->get();

        return view('sales.index', compact('sales', 'startDate', 'endDate'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaleRequest $request)
    {
        $data = $request->validated();

        if(Sale::whereDate('date', $data['date'])->exists())
        {
            return back()->with('fail_created', true);
        }

        Sale::create($data);
        return back()->with('created', true);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaleRequest $request)
    {
        $data = $request->validated();

        Sale::whereDate('date', $data['date'])
        ->firstOrFail()
        ->update([
            'net_value' => $data['net_value']
        ]);

        return back()->with('created', true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $data = $request->validate(['date' => 'required|date']);
        Sale::where('date', $data['date'])->firstOrFail()->delete();
        return redirect()->route('sales.index')->with('deleted', true);
    }
}
