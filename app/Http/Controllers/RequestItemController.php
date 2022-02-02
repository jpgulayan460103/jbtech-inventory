<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemDetail;
use App\Models\ItemHistory;
use App\Models\RequestItem;
use App\Models\RequestItemDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request_item = RequestItem::with(
            'warehouse',
            'requester',
            'processor',
        )->paginate(20);
        return $request_item;
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
        DB::beginTransaction();
        try {
            $request_item = RequestItem::create($request->all());
            $items = $request->items;
            $request_item->items()->createMany($items);
            DB::commit();
            return $request_item;
        } catch (\Throwable $th) {
            //throw $th;
        }
        return $request_item;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RequestItem  $requestItem
     * @return \Illuminate\Http\Response
     */
    public function show(RequestItem $requestItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RequestItem  $requestItem
     * @return \Illuminate\Http\Response
     */
    public function edit(RequestItem $requestItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RequestItem  $requestItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RequestItem $requestItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RequestItem  $requestItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequestItem $requestItem)
    {
        //
    }

    public function scan(Request $request)
    {
        $item_detail = ItemDetail::with('item')
        ->where('serial_number',$request->scanned)
        ->where('warehouse_id',$request->warehouse_id)
        ->first();
        return $item_detail;
    }

    public function process(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request_item = RequestItem::find($id);
            $request_item->status = "processed";
            $request_item->processor_id = $request->user_id;
            $request_item->save();
            foreach ($request->items as $itemArray) {
                $item_detail = ItemDetail::find($itemArray['id']);
                if($item_detail->quantity != 0){
                    $old_quantity = $item_detail->quantity;
                    $item = Item::find($item_detail->item_id);
                    $old_total_quantity_1 = $item->total_quantity_1;
                    $old_total_quantity_2 = $item->total_quantity_2;
                    $item_detail->quantity = 0;
                    $item_detail->save();
                    $item = Item::find($item_detail->item_id);
                    

                    ItemHistory::create([
                        'history_type' => 'out',
                        'item_id' => $item->id,
                        'item_detail_id' => $item_detail->id,
                        'user_id' => $request->user_id,
                        'warehouse_id' => $request->warehouse_id,
                        'stock' => $request->warehouse_id == 1 ? $old_total_quantity_1  : $old_total_quantity_2 ,
                        'remain' => $request->warehouse_id == 1 ? $item->total_quantity_1  : $item->total_quantity_2 ,
                        'request_item_id' => $id,
                        'quantity' =>  $old_quantity,
                    ]);
                }
            }

            foreach ($request->requestDataItems as $item) {
                $req_item = RequestItemDetail::find($item['id']);
                $req_item->fulfilled_quantity = $item['fulfilled_quantity'];
                $req_item->save();
            }
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
