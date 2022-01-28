<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveItemRequest;
use App\Models\Item;
use App\Models\ItemHistory;
use Illuminate\Http\Request;
use App\Http\Requests\SerialRequest;

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
        return Item::all();
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
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        //
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
            'stock' => $request->warehouse_id == 1 ? $old_total_quantity_1  : $old_total_quantity_2 ,
            'remain' => $request->warehouse_id == 1 ? $item->total_quantity_1  : $item->total_quantity_2 ,
        ]);
    }
}
