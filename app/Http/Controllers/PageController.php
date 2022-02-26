<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemHistory;
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
        $created_request = RequestItem::with('warehouse','items.item','serials.item', 'serials.item_detail','requester','processor')->findOrFail($id);
        // return $created_request;
        $user = Auth::user();
        return view('created-request', compact('created_request','user'));
    }
    public function request_process(Request $request, $id)
    {
        $created_request = RequestItem::with('warehouse','items.item','requester','processor')->findOrFail($id);
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
            echo '<div style="float:left;margin:2px;padding:8px 5px 2px 5px;width:max-content;border: 1px solid rgb(0,0,0,0.3);text-align:center"><img style="width:150px" src="data:image/png;base64,' . base64_encode($generator->getBarcode($prefix.$padded, $generator::TYPE_CODE_128)) . '"><br><span>'.$prefix.$padded.'</span></div>';
        }   
    }

    public function reports(Request $request)
    {
        $warehouse_id = "";
        if(Auth::user()->account_type == 'warehouse_admin'){
            $warehouse_id = Auth::user()->warehouse_id;
        }else{
            if($request->warehouse){
                $warehouse_id = $request->warehouse;
            }
        }
        $items = Item::all();
        foreach ($items as $item) {
            $total_in = ItemHistory::where('item_id',$item->id)->where(function ($query) {
                $query->where('history_type','in')
                      ->orWhere('history_type','stock_transfer');
            });
            if($warehouse_id != ""){
                $total_in->where('warehouse_id', $warehouse_id);
            }
            $item->total_in = $total_in->sum('quantity');
            $total_out = ItemHistory::where('item_id',$item->id)->where('history_type','out');
            if($warehouse_id != ""){
                $total_out->where('warehouse_id', $warehouse_id);
            }
            $item->total_out = $total_out->sum('quantity');
            $item->total_remaining = $item->total_in - $item->total_out;
        }
        $user = Auth::user();
        $warehouses = Warehouse::all();
        return view('reports', compact('items','user','warehouses', 'warehouse_id'));
    }
}
