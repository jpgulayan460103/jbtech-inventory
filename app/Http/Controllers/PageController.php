<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;

class PageController extends Controller
{
    public function items(Request $request)
    {
        $warehouses = Warehouse::all();
        return view('items', compact('warehouses'));
    }
    public function requests(Request $request)
    {
        $warehouses = Warehouse::all();
        return view('requests', compact('warehouses'));
    }
    public function create_request(Request $request)
    {
        $warehouses = Warehouse::all();
        return view('create-request', compact('warehouses'));
    }
}
