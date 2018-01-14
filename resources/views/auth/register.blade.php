@extends('layouts.app') @section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col col-lg-5 col-xl-4 col-md-7 col-sm-9">
            <h2>Register</h2>
            <form method="POST" action="{{ route('register') }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}{{ $errors->has('password') ? ' is-valid' : '' }}" id="name" placeholder="Name" name="name" value="{{ old('name') }}" required autofocus> @if ($errors->has('name'))
                    <div class="invalid-feedback">{{ $errors->first('name') }}</div> @endif
                </div>
                <div class="form-group">
                    <label for="email">E-Mail Address</label>
                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}{{ $errors->has('password') ? ' is-valid' : '' }}" id="email" placeholder="E-Mail Address" name="email" value="{{ old('email') }}" required> @if ($errors->has('email'))
                    <div class="invalid-feedback">{{ $errors->first('email') }}</div> @endif
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" placeholder="Password" name="password" required> @if ($errors->has('password'))
                    <div class="invalid-feedback">{{ $errors->first('password') }}</div> @endif
                </div>
                <div class="form-group">
                    <label for="password-confirm">Confirm Password</label>
                    <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" id="password-confirm" placeholder="Password" name="password_confirmation" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary mb-2" dusk="register-submit">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection