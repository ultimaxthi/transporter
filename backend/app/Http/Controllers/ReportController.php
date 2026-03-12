<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Relatório de consumo de combustível
     */
    public function fuelConsumption(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\FuelSupply::class);

        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->startOfMonth();

        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : Carbon::now();

        $report = $this->reportService->getFuelConsumptionReport($startDate, $endDate);

        return response()->json($report);
    }

    /**
     * Relatório de desempenho de motoristas
     */
    public function driverPerformance(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Trip::class);

        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->startOfMonth();

        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : Carbon::now();

        $report = $this->reportService->getDriverPerformanceReport($startDate, $endDate);

        return response()->json($report);
    }

    /**
     * Relatório de viagens
     */
    public function trips(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Trip::class);

        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->startOfMonth();

        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : Carbon::now();

        $report = $this->reportService->getTripReport($startDate, $endDate);

        return response()->json($report);
    }
}