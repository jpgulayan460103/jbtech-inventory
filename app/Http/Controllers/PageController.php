<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function items(Request $request)
    {
        return view('items');
    }
}
