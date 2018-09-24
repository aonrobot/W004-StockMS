<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Warehouse;
use DB;

class NewBranchController extends Controller
{
    public function create ()
    {
        return view('auth.register');
    }

    public function store (Request $request)
    {
        $data = $request->all();

        $validateData = $this->validator($data);

        $validateData->validate();

        $userId = User::create([
            'name' => $data['name'],
            'branchName' => $data['branchName'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ])->id;

        DB::table('product_category')->insert(
            array(
                'user_id' => $userId,
                'name' => 'Uncategory',
                'description' => 'Product ที่ยังไม่ได้จัดหมวดหมู่',
                "created_at" =>  \Carbon\Carbon::now(),
                "updated_at" => \Carbon\Carbon::now()
            )
        );

        // Insert main warehouse   
        Warehouse::create([
            'user_id' => $userId,
            'name' => 'สาขาหลัก (Main warehouse)',
            'address' => 'ที่อยู่ สาขาหลัก',
            "created_at" =>  \Carbon\Carbon::now(),
            "updated_at" => \Carbon\Carbon::now()
        ]);

        return redirect('home');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'branchName' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }
}
