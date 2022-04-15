<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemDetail;
use App\Models\ItemHistory;
use App\Models\RequestItem;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $user = Auth::user();
        $warehouses = Warehouse::all();
        $items = Item::orderBy('category')->orderBy('name')->get();
        return view('reports', compact('warehouses','items','user'));
    }

    public function generateReports(Request $request)
    {
        $warehouse_id = "";
        if(Auth::user()->account_type == 'warehouse_admin'){
            $warehouse_id = Auth::user()->warehouse_id;
        }else{
            if($request->warehouse_id){
                $warehouse_id = $request->warehouse_id;
            }
        }
        if($request->date){
            $date = Carbon::parse($request->date);
        }else{
            $date = Carbon::now()->toDateString();
        }
        $query_start_day = Carbon::parse($date)->copy()->firstOfMonth();
        $query_end_day = Carbon::parse($date)->copy()->lastOfMonth()->addDay()->subSecond();
        $last_query_start_day = Carbon::parse($query_start_day)->copy()->subSecond()->firstOfMonth();
        $last_query_end_day = Carbon::parse($last_query_start_day)->copy()->lastOfMonth()->addDay()->subSecond();
        $items = Item::orderBy('category')->orderBy('name')->get();
        foreach ($items as $item) {
            if($item->total_remain){
                $item->total_remain = $item->total_remain->remain;
            }else{
                $item->total_remain = 0;
            }
            $previous_in = ItemHistory::where('item_id',$item->id)->where('created_at',"<=", $last_query_end_day)->where(function ($query) {
                $query->where('history_type','in')
                      ->orWhere('history_type','stock_transfer');
            });
            $previous_in = $previous_in->sum('quantity');
            // return $previous_in;


            //prev
            $previous_out = ItemHistory::where('item_id',$item->id)->where('created_at',"<=", $last_query_end_day)->where(function ($query) {
                $query->where('history_type','out')
                      ->orWhere('history_type','deleted');
            });
            $previous_out = $previous_out->sum('quantity');

            $previous_total_in = $previous_in - $previous_out;



            $total_in = ItemHistory::where('item_id',$item->id)->whereBetween('created_at',[$query_start_day, $query_end_day])->where('history_type','in');
            $total_stock_transfer = ItemHistory::where('item_id',$item->id)->whereBetween('created_at',[$query_start_day, $query_end_day])->where('history_type','stock_transfer');
            if($warehouse_id != ""){
                $total_in->where('warehouse_id', $warehouse_id);
                $total_stock_transfer->where('warehouse_id', $warehouse_id);
            }
            $item->previous_remaining = $previous_total_in;
            $item->total_in = $total_in->sum('quantity');
            $item->total_stock_transfer = $total_stock_transfer->sum('quantity');
            $total_out = ItemHistory::where('item_id',$item->id)->whereBetween('created_at',[$query_start_day, $query_end_day])->where('history_type','out');
            $total_deleted = ItemHistory::where('item_id',$item->id)->whereBetween('created_at',[$query_start_day, $query_end_day])->where('history_type','deleted');
            if($warehouse_id != ""){
                $total_out->where('warehouse_id', $warehouse_id);
                $total_deleted->where('warehouse_id', $warehouse_id);
            }
            $item->total_out = $total_out->sum('quantity');
            $item->total_deleted = $total_deleted->sum('quantity');
            $item->total_remaining = ($item->total_in + $item->total_stock_transfer) + $previous_total_in - ($item->total_out + $item->total_deleted);
        }
        // return $items;
        $date = Carbon::parse($date)->format("F Y");
        return [
            'items' => $items->toArray(),
            'date' => $date,
            'warehouse_id' => $warehouse_id,
            'uuid' => Str::uuid()
        ];
    }

    public function generateExcelReport(Request $request)
    {
        $report = $this->generateReports($request);
        $warehouse_name = "";
        if($report['warehouse_id']){
            $warehouse_name = Warehouse::find($report['warehouse_id'])->name;
        }
        $list = [
            [
                "Month:",
                $report['date'],
                "Warehouse:",
                $warehouse_name,
            ],
            [
                'Item ID',
                'Category',
                'Item Name',
                'Starting',
                'In',
                'Transferred',
                'Out',
                'Deleted',
                'Remaining',
            ]
        ];
          
        $path = "generated/jbtech-inventory-report-".$report['date'].".csv";
        $file = fopen($path,"w");
        
        foreach ($list as $line) {
            fputcsv($file, $line);
        }
        
        foreach ($report['items'] as $item) {
            $line = [
                $item['id'],
                $item['category'],
                $item['name'],
                $item['previous_remaining'],
                $item['total_in'],
                $item['total_stock_transfer'],
                $item['total_out'],
                $item['total_deleted'],
                $item['total_remaining'],
            ];
            fputcsv($file, $line);
        }
        
        fclose($file);
        return [
            "path" => $path
        ];
    }
}
