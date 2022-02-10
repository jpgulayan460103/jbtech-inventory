<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveItemRequest;
use App\Models\Item;
use App\Models\ItemHistory;
use Illuminate\Http\Request;
use App\Http\Requests\SerialRequest;
use App\Models\ItemDetail;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Item::paginate(10);
    }

    public function all()
    {
        $items = Item::all();
        return $items;
    }

    public function detail_search(Request $request)
    {
        $item_details = ItemDetail::query();
        $item_details->join('items', 'items.id', '=', 'item_details.item_id', 'left');
        $item_details->groupBy('quantity');
        // $item_details->distinct();
        $item_details->where('warehouse_id', $request->warehouse_id);
        return $item_details->get();
    }

    public function forRequest(Request $request, $id)
    {
        $items = Item::find(1);
        $items->remaining = ItemDetail::where('quantity','<>',0)->where('item_id', $id)->where('warehouse_id', $request->warehouse_id)->groupBy('quantity')->select(DB::raw('quantity as per_pieces'), DB::raw('SUM(quantity) as total_quantity'), DB::raw('CAST(SUM(quantity/quantity) AS DECIMAL) as max_quantity'))->get();
        return $items;
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
    public function store(SaveItemRequest $request)
    {
        $item_exist = Item::where('name', $request->name)->where('category',$request->category)->first();
        if($item_exist){
            return response()->json([
                'message' => "The given data was invalid.",
                'errors' => [
                    'name' => ['The item is already added.']
                ]
            ], 422);
        }
        $item = Item::create($request->all());
        return $item;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item_exist = Item::where('name', $request->name)->where('category',$request->category)->where('id',"<>",$id)->first();
        if($item_exist){
            return response()->json([
                'message' => "The given data was invalid.",
                'errors' => [
                    'name' => ['The item is already added.']
                ]
            ], 422);
        }
        $item = Item::find($id)->update($request->all());
        return $item;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item, $id)
    {
        $item->find($id)->delete();
    }

    public function addSerial(SerialRequest $request, $id)
    {
        $item = Item::find($id);
        $old_total_quantity_1 = $item->total_quantity_1;
        $old_total_quantity_2 = $item->total_quantity_2;
        $item_detail = $item->details()->create($request->all());
        ItemHistory::create([
            'history_type' => 'in',
            'item_id' => $item->id,
            'item_detail_id' => $item_detail->id,
            'user_id' => $request->user_id,
            'warehouse_id' => $request->warehouse_id,
            'quantity' =>  $request->quantity,
            'stock' => $request->warehouse_id == 1 ? $old_total_quantity_1  : $old_total_quantity_2 ,
            'remain' => $request->warehouse_id == 1 ? $item->total_quantity_1  : $item->total_quantity_2 ,
        ]);
        $item->allow_delete = 0;
        $item->save();
    }

    public function categories(Request $request)
    {
        return Item::select('category')->distinct()->get();
    }
}
