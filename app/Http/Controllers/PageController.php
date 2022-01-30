<?php

namespace App\Http\Controllers;

use App\Models\RequestItem;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function items(Request $request)
    {
        $warehouses = Warehouse::all();
        $user = Auth::user();
        return view('items', compact('warehouses','user'));
    }
    public function requests(Request $request)
    {
        $warehouses = Warehouse::all();
        $user = Auth::user();
        return view('requests', compact('warehouses','user'));
    }
    public function create_request(Request $request)
    {
        $warehouses = Warehouse::all();
        $user = Auth::user();
        return view('create-request', compact('warehouses','user'));
    }
    public function created_request(Request $request, $id)
    {
        $created_request = RequestItem::with('warehouse','items.item','requester','processor')->find($id);
        $user = Auth::user();
        return view('created-request', compact('created_request','user'));
    }
    public function request_process(Request $request, $id)
    {
        $created_request = RequestItem::with('warehouse','items.item')->find($id);
        $user = Auth::user();
        return view('process-request', compact('created_request','user'));
    }
}
