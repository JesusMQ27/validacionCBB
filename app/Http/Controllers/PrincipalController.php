<?php

namespace App\Http\Controllers;

use App\Principal;
use Illuminate\Http\Request;

class PrincipalController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function principal() {
        //return view('principal');
        $data = Principal::load_modulos();
        return view('principal', compact('data'));
        //return view('principal')->with(compact('data'));
    }

}
