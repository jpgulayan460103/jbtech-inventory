<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return User::with('warehouse')->where('account_type','<>','admin')->get();
    }

    public function store(CreateUserRequest $request){
        return User::create($request->all());
    }

    public function update(Request $request, $id){
        return User::find($id)->update($request->all());
    }
    public function destroy($id){
        return User::find($id)->delete();
    }
}
