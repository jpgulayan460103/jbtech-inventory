<?php

namespace App\Http\Controllers;

use App\Models\ItemDetail;
use App\Models\Item;
use App\Models\ItemHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        DB::enableQueryLog();
        $items = ItemDetail::with('warehouse','item')->where('quantity','<>',0);
        if($id != 'all'){
            $items->where('item_id',$id);
        }
        if($request->warehouse_id){
            $items->where('warehouse_id', $request->warehouse_id);
        }
        if($request->stock_month){
            $items->where('stock_month', Carbon::parse($request->stock_month)->toDateString());
        }
        if($request->search){
            $search = $request->search;
            $items->join('items', 'items.id', '=', 'item_details.item_id')
            ->where(function ($query) use ($search) {
                $query->where('item_details.serial_number', 'like', "%".$search."%")
                      ->orWhere('items.name', 'like', "%".$search."%");
            });
        }
        // $items->orderBy('stock_month');
        $items->orderBy('id', "DESC");
        $items->paginate(10);
        // return DB::getQueryLog();
        return $items->paginate(10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ItemDetail  $itemDetail
     * @return \Illuminate\Http\Response
     */
    public function show(ItemDetail $itemDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ItemDetail  $itemDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(ItemDetail $itemDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ItemDetail  $itemDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemDetail $itemDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ItemDetail  $itemDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $item_id, $id)
    {
        try {
            DB::beginTransaction();
            $item_detail = ItemDetail::find($id);
            $item_detail_old = $item_detail;
            $item_detail_old_quantity = $item_detail->quantity;
            $item_detail->quantity = 0;
            $item_detail->save();
            $item = Item::find($item_id);
            $old_total_quantity_1 = $item->total_quantity_1;
            $old_total_quantity_2 = $item->total_quantity_2;
            ItemHistory::create([
                'history_type' => "deleted",
                'item_id' => $item->id,
                'item_detail_id' => $item_detail->id,
                'user_id' => $request->user_id,
                'warehouse_id' => $item_detail_old->warehouse_id,
                'quantity' =>  $item_detail_old_quantity,
                'stock' => $item_detail_old->warehouse_id == 1 ? $old_total_quantity_1 + $item_detail_old_quantity  : $old_total_quantity_2 + $item_detail_old_quantity ,
                'remain' => $item_detail_old->warehouse_id == 1 ? $item->total_quantity_1  : $item->total_quantity_2,
                'deleted_remarks' => $request->deleted_remarks
            ]);
            $item_detail->delete();
            DB::commit();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function stockMonths(Request $request, $id)
    {
        $items = ItemDetail::select('stock_month')->orderBy('stock_month')->where('quantity','<>',0)->distinct()->get('stock_month');
        return $items;
    }
}
