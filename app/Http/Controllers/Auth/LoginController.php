<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\tb_personal;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

//use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller {

    public function __construct() {
        $this->middleware('guest', ['only' => 'showLoginForm']);
    }

    /* public function login1() {
      $credentials = $this->validate(request(), [
      $this->username() => 'string',
      'password' => 'string'
      ]);
      //return $credentials;
      if (Auth::attempt($credentials)) {
      return redirect()->route('principal');
      } else {
      //            return "pt";
      return back()
      ->withErrors([
      $this->username() => 'correo no encontrado',
      'password' => 'contraseña erronea'
      ])
      ->withInput(request([$this->username(), 'password']))
      ;
      }
      }

      public function login2() {
      $credenciales = $this->validate(request(), [
      $this->username() => 'required|string',
      'password' => 'required|string'
      ]);

      $personal = DB::table('bib_personal as a')
      ->join('bib_personal_local as b', 'a.id', '=', 'b.id_user')
      ->join('bib_acceso_biblioteca as c', 'b.plo_id', '=', 'c.plo_id')
      ->join('bib_personal_perfil as d', 'a.id', '=', 'd.id_user')
      ->select('a.*')
      ->select(DB::raw('count(*) as cantidad'))
      ->where(['a.per_dni' => request()->per_dni, 'a.password' => MD5(request()->password), 'a.per_esta' => '1'])
      ->get();

      $personal_dat = Personal::where(['per_dni' => request()->per_dni
      , 'password' => MD5(request()->password)
      , 'per_esta' => '1'])->first();

      if ($personal[0]->cantidad > 0) {
      Auth::login($personal_dat);
      return redirect()->route('inicio');
      } else {
      //return "Hubo un error";
      return back()
      ->withErrors([$this->username() => trans('auth.failed')]) //Retorna error en los campos de login
      ->withInput(request([$this->username()]));
      }
      }

      public function login3() {

      $credentials = $this->validate(request(), [
      $this->username() => 'string',
      'password' => 'string'
      ]);
      $personal = DB::table('usuarios as u')
      ->select('u.*')
      ->select(DB::raw('count(*) as cantidad'))
      ->where(['u.per_dni' => request()->per_dni,
      'u.per_esta' => '1',
      'u.password' => MD5(request()->password)])
      ->get();
      $personal_data = usuarios::where(['per_dni' => request()->per_dni,
      'password' => MD5(request()->password),
      ])->first();
      if ($personal[0]->cantidad > 0) {
      //dd($personal);

      Auth::login($personal_data);
      return redirect()->route('principal');
      } else {
      return back()
      ->withErrors([
      $this->username() => 'usuario no encontrado',
      'password' => ''
      ])
      ->withInput(request([$this->username(), 'password']));
      }
      } */

    public function login() {

        $credentials = $this->validate(request(), [
            $this->username() => 'string|required',
            'password' => 'string|required'
        ]);
        $personal = DB::table('tb_personal')
                ->where(['per_codigo' => request()->per_usua,
                    'per_estado' => '1',
                    'password' => MD5(request()->password)])
                ->get();
        //dd($personal);
        $personal_data = tb_personal::where(['per_codigo' => request()->per_usua,
                    'password' => MD5(request()->password),
                ])->first();
        //dd($personal_data);
        if (count($personal) > 0) {
            //dd($personal);

            Auth::login($personal_data);
            //Auth::login($personal[0]);
            return redirect()->route('principal');
        } else {
            return back()
                            ->withErrors([
                                $this->username() => 'Credenciales incorrectas',
                                'password' => ''
                            ])
                            ->withInput(request([$this->username(), 'password']));
        }
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }

    public function showLoginForm() {
        return view('auth.login');
    }

    public function username() {
        return 'per_usua';
    }

}
