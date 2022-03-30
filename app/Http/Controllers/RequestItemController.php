<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRequestItem;
use App\Models\Item;
use App\Models\ItemDetail;
use App\Models\ItemHistory;
use App\Models\RequestItem;
use App\Models\RequestItemDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request_item = RequestItem::with(
            'warehouse',
            'requester',
            'processor',
        );
        $user = User::find($request->user_id);
        if($user && $user->account_type == 'user'){
            $request_item->where('requester_id', $user->id);
        }
        if($user && $user->account_type == 'warehouse_admin'){
            if($request->show && $request->show == 'requested'){
                $request_item->where('warehouse_id', $user->warehouse_id);
                $request_item->where('requester_id', '<>', $user->id );
            }else{
                $request_item->where('requester_id', $user->id);
            }
        }
        if($request->search && trim($request->search) != ""){
            // $request_item->where('request_number', 'like', "%".$request->search."%");
            $request_item->where(function ($query) use ($request) {
                $query->where('request_number', 'like', "%".$request->search."%")
                      ->orWhere('customer_name', 'like', "%".$request->search."%");
            });
        }
        if($request->status && trim($request->status) != ""){
            $request_item->where('status', $request->status);
        }
        $request_item->orderBy('id', 'desc');
        return $request_item->paginate(20);
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
    public function store(CreateRequestItem $request)
    {
        DB::beginTransaction();
        try {
            $request_item = RequestItem::create($request->all());
            $items = $request->items;
            $request_item->items()->createMany($items);
            $request_item->request_number = Carbon::parse(Carbon::now())->format("Y-").str_pad($request_item->id,4,"0",STR_PAD_LEFT);;
            $request_item->status = "pending";
            $request_item->save();
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
    public function update(Request $request, $id)
    {
        RequestItem::find($id)->update($request->all());
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
                $req_item->is_rejected = $item['is_rejected'];
                $req_item->save();
            }
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function receive(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request_item = RequestItem::with('warehouse','items.item','serials.item', 'serials.item_detail','requester','processor')->find($id);
            $request_item->status = "received";
            $request_item->save();
            // return $request_item;
            $serials = $request_item->serials;
            foreach ($serials as $history_item) {
                $data = [
                    'quantity' => $history_item->quantity,
                    'warehouse_id' => $request_item->requester->warehouse_id,
                ];
                $item = Item::find($history_item->item->id);
                $old_total_quantity_1 = $item->total_quantity_1;
                $old_total_quantity_2 = $item->total_quantity_2;
                $receiver = ItemHistory::create([
                    'history_type' => 'stock_transfer',
                    'item_id' => $history_item->item->id,
                    'item_detail_id' => $history_item->item_detail->id,
                    'user_id' => $request->user_id,
                    'warehouse_id' => $request->warehouse_id,
                    'stock' => $request->warehouse_id == 1 ? $old_total_quantity_1 : $old_total_quantity_2,
                    'remain' => $request->warehouse_id == 1 ? $item->total_quantity_1 + $history_item->quantity  : $item->total_quantity_2 + $history_item->quantity ,
                    'request_item_id' => $id,
                    'quantity' =>  $history_item->quantity,
                ]);

                // $sender = ItemHistory::create([
                //     'history_type' => 'stock_transfer_send',
                //     'item_id' => $history_item->item->id,
                //     'item_detail_id' => $history_item->item_detail->id,
                //     'user_id' => $request_item->processor_id,
                //     'warehouse_id' => $request_item->warehouse_id,
                //     'stock' => $request_item->warehouse_id == 1 ? $old_total_quantity_1 + $history_item->quantity : $old_total_quantity_2 + $history_item->quantity,
                //     'remain' => $request_item->warehouse_id == 1 ? $item->total_quantity_1 : $item->total_quantity_2,
                //     'request_item_id' => $id,
                //     'quantity' =>  $history_item->quantity,
                // ]);
                // return [
                //     $receiver,
                //     $sender,
                // ];
                $history_item->item_detail()->update($data);
            }
            DB::commit();
            return $request_item;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
