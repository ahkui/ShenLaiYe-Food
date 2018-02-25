@extends('layouts.app') @section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col col-lg-5 col-xl-4 col-md-7 col-sm-9">
            <h2>Login</h2>
            <form method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="email">E-Mail Address</label>
                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" placeholder="E-Mail Address" name="email" value="{{ old('email') }}" required autofocus> @if ($errors->has('email'))
                    <div class="invalid-feedback">{{ $errors->first('email') }}</div> @endif
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" placeholder="Password" name="password" required> @if ($errors->has('password'))
                    <div class="invalid-feedback">{{ $errors->first('password') }}</div> @endif
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="remember" class="custom-control-input" id="remember" {{ old( 'remember') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="remember">Remember Me</label>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary mb-2">Login</button>
                    {{-- <a class="btn btn-link mb-2" href="{{ route('password.request') }}">Forgot Your Password?</a> --}}
                </div>
            </form>
        </div>
    </div>
</div>
@endsection