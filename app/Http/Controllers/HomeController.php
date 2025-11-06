<?php

namespace App\Http\Controllers;

//use Module\Dokani\Services\HelperService;

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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
       return view('home');
    }



    public function subscriptionCheck()
    {
        return 12345;
    }


    public function permissionCheck()
    {
        return '1234';
    }
}
