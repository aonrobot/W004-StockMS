<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getJSON(Request $request){
        $data = $request->input('data');
        print_r($data);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \App\User::find(1);

        // Creating a token without scopes...
        $token = $user->createToken('TokenAccessAPI')->accessToken;

        return view('home', ['token' => $token]);
    }
}
