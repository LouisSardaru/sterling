<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\RestaurantTable;

class DashboardController extends Controller
{
    public function __invoke()
    {  
        $profit = 0;
        foreach(Order::wherePlacedToday()->whereDelivered()->get() as $order) {
          $profit += $order->total_unformatted;
        }

        return Inertia::render('Dashboard/Index', [
            'orders_placed' => Order::wherePlacedToday()->count(),
            'orders_takeout' => Order::whereTakeoutToday()->count(),
            'orders_fullfilled' => Order::wherePlacedToday()->count() > 0 ? (Order::whereDeliveredToday()->count() / Order::wherePlacedToday()->count()) * 100 : 0,
            'tables_unassigned' => RestaurantTable::whereUnassigned()->count(),
            'profit' => number_format($profit, 2),
        ]);
    }
}
