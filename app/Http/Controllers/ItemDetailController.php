<?php

namespace App\Http\Controllers;

use App\Models\ItemDetail;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $items = ItemDetail::with('warehouse','item')->where('quantity','<>',0);
        if($id != 'all'){
            $items->where('item_id',$id);
        }
        if($request->warehouse_id){
            $items->where('warehouse_id', $request->warehouse_id);
        }
        if($request->search){
            $search = $request->search;
            $items->join('items', 'items.id', '=', 'item_details.item_id')
            ->where(function ($query) use ($search) {
                $query->where('item_details.serial_number', 'like', "%".$search."%")
                      ->orWhere('items.name', 'like', "%".$search."%");
            });
        }
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
    public function destroy(ItemDetail $itemDetail)
    {
        //
    }
}
