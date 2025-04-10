<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use Inertia\Inertia;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('date_range') && is_array($request->date_range)) {
            $request->merge([
                'date_range' => array_values($request->date_range),
            ]);
        }
        // Validación de parámetros
        $validated = $request->validate([
            'date_range' => 'nullable|sometimes|array',
            'date_range.0' => 'nullable|date',
            'date_range.1' => 'nullable|date|after_or_equal:date_range.0',
            'regions' => 'sometimes',
            'categories' => 'sometimes',
            'customer_types' => 'sometimes',
            'payment_methods' => 'sometimes',
            'sales_persons' => 'sometimes'
        ]);

        /* dd($validated); */

        // Normalizar filtros
        $filters = [
            'date_range' => $validated['date_range'] ?? null,
            'regions' => $this->normalizeFilter($validated['regions'] ?? null),
            'categories' => $this->normalizeFilter($validated['categories'] ?? null),
            'customer_types' => $this->normalizeFilter($validated['customer_types'] ?? null),
            'payment_methods' => $this->normalizeFilter($validated['payment_methods'] ?? null),
            'sales_persons' => $this->normalizeFilter($validated['sales_persons'] ?? null)
        ];

        $query = Sale::query();

        // Aplicar filtro de fechas
        if (!empty($filters['date_range'])) {
            $start = Carbon::parse($filters['date_range'][0])->startOfDay();
            $end = Carbon::parse($filters['date_range'][1] ?? $start)->endOfDay();
            $query->whereBetween('sale_date', [$start, $end]);
        }

        // Mapeo y aplicación de filtros
        $filterMap = [
            'regions' => 'region',
            'categories' => 'product_category',
            'customer_types' => 'customer_type',
            'payment_methods' => 'payment_method',
            'sales_persons' => 'sales_person'
        ];

        foreach ($filterMap as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->whereIn($dbColumn, $filters[$filterKey]);
            }
        }

        // Obtener opciones para filtros
        $filterOptions = [
            'regions' => Sale::distinct('region')->pluck('region'),
            'categories' => Sale::distinct('product_category')->pluck('product_category'),
            'customer_types' => Sale::distinct('customer_type')->pluck('customer_type'),
            'payment_methods' => Sale::distinct('payment_method')->pluck('payment_method'),
            'sales_persons' => Sale::distinct('sales_person')->pluck('sales_person')
        ];

        // Calcular indicadores
        $indicators = $this->calculateIndicators($query);

        return Inertia::render('Sales/Index', [
            'indicators' => $indicators,
            'filterOptions' => $filterOptions,
            'filters' => $filters
        ]);
    }

    protected function normalizeFilter($input): array
    {
        if (is_array($input)) {
            return array_filter($input);
        }

        if (is_string($input) && $input !== '') {
            return [$input];
        }

        return [];
    }

    private function calculateIndicators($query): array
    {
        return [
            'total_sales' => $query->clone()->sum('total_sale'),
            'average_sale' => $query->clone()->avg('total_sale'),

            'monthly_trend' => $query->clone()
                ->select(
                    DB::raw('YEAR(sale_date) as year'),
                    DB::raw('MONTH(sale_date) as month'),
                    DB::raw('SUM(total_sale) as total')
                )
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get()
                ->map(function ($item) {
                    return [
                        'period' => Carbon::createFromDate($item->year, $item->month)
                            ->locale('es')
                            ->isoFormat('MMMM YYYY'),
                        'total' => $item->total
                    ];
                }),

            'product_performance' => $query->clone()
                ->select('product_name')
                ->selectRaw('SUM(total_sale) as total')
                ->groupBy('product_name')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),

            'regional_sales' => $query->clone()
                ->select('region')
                ->selectRaw('SUM(total_sale) as total')
                ->groupBy('region')
                ->get()
                ->map(function ($item) {
                    $region = Region::where('name', $item->region)->first();

                    return [
                        'region' => $item->region,
                        'total' => (float) $item->total,
                        'lat' => optional($region)->lat,
                        'lng' => optional($region)->lng,
                    ];
                }),

            'daily_trend' => $query->clone()
                ->select(
                    DB::raw('DATE(sale_date) as date'),
                    DB::raw('SUM(total_sale) as total')
                )
                ->groupBy('date')
                ->get(),

            'payment_methods' => $query->clone()
                ->select('payment_method')
                ->selectRaw('SUM(total_sale) as total')
                ->groupBy('payment_method')
                ->get(),

            'customer_types' => $query->clone()
                ->select('customer_type')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('customer_type')
                ->get(),

            'sales_performance' => $query->clone()
                ->select('sales_person')
                ->selectRaw('SUM(total_sale) as total')
                ->groupBy('sales_person')
                ->orderByDesc('total')
                ->limit(8)
                ->get(),

            'category_distribution' => $query->clone()
                ->select('product_category')
                ->selectRaw('SUM(total_sale) as total')
                ->groupBy('product_category')
                ->get(),

            'monthly_comparison' => $query->clone()
                ->select(
                    DB::raw('YEAR(sale_date) as year'),
                    DB::raw('MONTH(sale_date) as month'),
                    DB::raw('SUM(total_sale) as total')
                )
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get()
        ];
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
