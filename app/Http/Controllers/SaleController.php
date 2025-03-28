<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $indicators = [
            'total_sales' => Sale::sum('total_sale'),
            'average_sale' => Sale::avg('total_sale'),
            'monthly_trend' => Sale::select(
                DB::raw('MONTH(sale_date) as month'),
                DB::raw('SUM(total_sale) as total')
            )
                ->groupBy('month')
                ->get()
                ->map(function ($item) {
                    return [
                        'month' => date('F', mktime(0, 0, 0, $item->month, 1)),
                        'total' => $item->total
                    ];
                }),

            'product_performance' => Sale::select('product_name')
                ->selectRaw('SUM(total_sale) as total')
                ->groupBy('product_name')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),

            'regional_sales' => Sale::select('region')
                ->selectRaw('SUM(total_sale) as total')
                ->groupBy('region')
                ->get(),

            'daily_trend' => Sale::select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('SUM(total_sale) as total')
            )
                ->whereBetween('sale_date', [now()->subDays(30), now()])
                ->groupBy('date')
                ->get(),

            'payment_methods' => Sale::select('payment_method')
                ->selectRaw('SUM(total_sale) as total')
                ->groupBy('payment_method')
                ->get(),

            'customer_types' => Sale::select('customer_type')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('customer_type')
                ->get(),

            'sales_performance' => Sale::select('sales_person')
                ->selectRaw('SUM(total_sale) as total')
                ->groupBy('sales_person')
                ->orderByDesc('total')
                ->limit(8)
                ->get(),

            'category_distribution' => Sale::select('product_category')
                ->selectRaw('SUM(total_sale) as total')
                ->groupBy('product_category')
                ->get(),

            'monthly_comparison' => Sale::select(
                DB::raw('YEAR(sale_date) as year'),
                DB::raw('MONTH(sale_date) as month'),
                DB::raw('SUM(total_sale) as total')
            )
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get()
        ];

        return Inertia::render('Sales/Index', [
            'indicators' => $indicators
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        //
    }
}
