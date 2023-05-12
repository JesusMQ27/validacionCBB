@extends('layouts.app')

@section('content')
<!-- chinita-->
<div class="container">
    <div class="row row d-flex justify-content-md-center align-items-center vh-100">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Sistema de Validación CBB - Iniciar Sesión') }}</div>
                <div class="card-body">
                    <form class="user" method="POST" action="{{ route('login') }}">
                        @csrf
                        {{ csrf_field() }}
                        <div class="form-group row">
                            <!--<label for="per_usua" class="col-md-4 col-form-label text-md-right">{{ __('Usuario') }}</label>-->

                            <div class="col-md-12">
                                <input value="{{old('per_usua')}}" placeholder="Usuario" id="per_usua" type="text" class="form-control form-control-user @error('per_usua') is-invalid @enderror" name="per_usua" value="{{ old('per_usua') }}" required autocomplete="per_usua" autofocus>

                                @error('per_usua')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <!--<label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Contraseña') }}</label>-->
                            <div class="col-md-12">
                                <input value="{{old('password')}}" placeholder="Contraseña" id="password" type="password" class="form-control form-control-user @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <!--<div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                           <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>-->
                        <div class="form-group row mb-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    {{ __('Acceder') }}
                                </button>
                                <!--
                                                                @if (Route::has('password.request'))
                                                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                                                    {{ __('Forgot Your Password?') }}
                                                                </a>
                                                                @endif
                                -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
