<?php

namespace App\Http\Controllers;

use App\Models\ItemHistory;
use App\Models\ItemDetail;
use Illuminate\Http\Request;

class ItemHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $items = ItemHistory::with('item','item_detail.warehouse','request_item.warehouse', 'user', 'warehouse')->orderBy('id', 'desc');
        if($id != 'all'){
            $items->where('item_id',$id);
        }
        if($request->warehouse_id){
            $items->where('warehouse_id', $request->warehouse_id);
        }
        if($request->history_type){
            $items->where('history_type', $request->history_type);
        }
        if($request->search){
            $search = $request->search;
            $item_details_ids = ItemDetail::join('items', 'items.id', '=', 'item_details.item_id')
            ->where(function ($query) use ($search) {
                $query->where('item_details.serial_number', 'like', "%".$search."%")
                      ->orWhere('items.name', 'like', "%".$search."%");
            })
            ->select('item_details.id', 'item_details.serial_number', 'items.name')->pluck('id');
            $items->whereIn('item_detail_id', $item_details_ids);
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
     * @param  \App\Models\ItemHistory  $itemHistory
     * @return \Illuminate\Http\Response
     */
    public function show(ItemHistory $itemHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ItemHistory  $itemHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(ItemHistory $itemHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ItemHistory  $itemHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemHistory $itemHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ItemHistory  $itemHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemHistory $itemHistory)
    {
        //
    }
}
