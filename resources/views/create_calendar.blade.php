@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
    @include('layouts.sidemenu')
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">カレンダー登録</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <create-calendar></create-calendar>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection