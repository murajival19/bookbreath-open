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
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * termsを開きます。
     *
     * @return \Illuminate\Http\Response
     */
    public function terms()
    {
        return view('terms');
    }

    /**
     * ポリシーを開きます。
     *
     * @return \Illuminate\Http\Response
     */
    public function policy()
    {
        return view('policy');
    }

    /**
     * 使い方を開きます。
     *
     * @return \Illuminate\Http\Response
     */
    public function howToUse()
    {
        return view('howToUse');
    }
}
