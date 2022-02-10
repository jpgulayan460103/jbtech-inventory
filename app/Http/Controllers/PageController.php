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
        $created_request = RequestItem::with('warehouse','items.item','requester','processor')->find($id);
        $user = Auth::user();
        return view('process-request', compact('created_request','user'));
    }
    public function users(Request $request)
    {
        $warehouses = Warehouse::all();
        $user = Auth::user();
        return view('users', compact('warehouses','user'));
    }
    public function generateBarcode(Request $request)
    {
        $prefix = $request->prefix ? $request->prefix : "";
        $from = $request->from ? $request->from : 0;
        $to = $request->to ? $request->to : 0;
        $padding = $request->padding ? $request->padding : 0;
        for ($i=$from; $i <= $to; $i++) { 
            $padded = str_pad($i,$padding,"0",STR_PAD_LEFT);
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            echo '<div style="float:left;margin:2px;padding:10px 10px 2px 10px;width:max-content;border: 1px solid black;text-align:center"><img src="data:image/png;base64,' . base64_encode($generator->getBarcode($prefix.$padded, $generator::TYPE_CODE_128)) . '"><br><span>'.$prefix.$padded.'</span></div>';
        }
        
    }
}
