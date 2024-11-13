<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalSales' => 1250.00,
            'totalOrders' => 25,
            'lowStockItems' => 5,
            'pendingPurchaseOrders' => 3
        ];

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats
        ]);
    }
}
