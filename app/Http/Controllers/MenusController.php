<?php

namespace App\Http\Controllers;

use App\Menus;
use Illuminate\Http\Request;

class MenusController extends Controller {

    public function __construct() {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function valida(Request $request) {
        //return view('principal');
        $id = $request->input('id');
        $href = $request->input('href');
        $data = Menus::valida_menu($href, $id, auth()->user()->id);
        //dd($data);
        return $data;
        //return view('principal', compact('data'));
        //return view('principal')->with(compact('data'));
    }

    public function loadMenuDeve($href, Request $request) {
        //$href2[] = $href;
        $per_id = auth()->user()->id;
        $menu = $request->input("menu");
        $data = Menus::carga_opciones($menu, $per_id);
        return view("devengado", compact('href', 'menu', 'data'));
    }

    public function loadMenuConta($href, Request $request) {
        $per_id = auth()->user()->id;
        $menu = $request->input("menu");
        $data = Menus::carga_opciones($menu, $per_id);
        return view("contabilidad", compact('href', 'menu', 'data'));
    }

    public function loadSubMenuValida($href) {
        $ruta = $href;
        $url = explode("_", $ruta);
        if (count($url) > 1) {
            $url = $url[0] . "/" . $url[1];
        } else {
            $url = $ruta;
        }
    }

    public function loadSubMenu($href) {
        $ruta = $href;
        $url = explode("_", $ruta);
        if (count($url) > 1) {
            $url = $url[0] . "/" . $url[1];
        } else {
            $url = $ruta;
        }

        return view("submenu/$url");
    }

}
