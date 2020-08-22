@extends('layouts.app')

@section('content')
<div class="container justify-content-center d-flex align-items-center mt-reset">
        <div class="col-sm-12 col-md-8 col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="px-4 pb-4">
                        <img class="" src="{{ asset('images/onedawn-logo-full.svg') }}" alt="">
                    </div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        @if (count($errors) > 0)
                            <div>
                                <p style="color:red">ログインできませんでした</p>
                            </div>
                        @endif                  
                        <div class="col pb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">ログイン ID</span>
                                </div>
                                <input id="code" type="text" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" value="{{ old('code') }}" required autofocus>
                                @if ($errors->has('code'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col pb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">パスワード</span>
                                </div>
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- <div class="col pb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">アカウント ID</span>
                                </div>
                                <input id="account_id" type="text" class="form-control{{ $errors->has('account_id') ? ' is-invalid' : '' }}" name="account_id" value="{{ old('account_id') }}" required>
                                @if ($errors->has('account_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('account_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div> -->
                        <div class="col pb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="customCheck">次回から入力を省略する</label>
                            </div>
                        </div>
                        <div class="col pb-2">
                            <div class="btn-group d-flex">
                                <button type="submit" class="btn btn-primary btn-lg font-size-rg w-100">ログインする</button>
                            </div>
                        </div>
                        <!--
                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        -->
                    </form>
                </div>
            </div>
        </div>
</div>
@endsection
