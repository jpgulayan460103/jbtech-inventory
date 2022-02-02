<?php

namespace App\Http\Controllers;

use App\Models\ItemHistory;
use Illuminate\Http\Request;

class ItemHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        return ItemHistory::with('item','item_detail.warehouse','request_item', 'user')->where('item_id',$id)->orderBy('warehouse_id')->orderBy('id', 'desc')->paginate(10);
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
