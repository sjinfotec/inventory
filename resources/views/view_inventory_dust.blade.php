@extends('layouts.main')

@section('content')
<!-- main contents -->
<div id="maincontents">
	<!-- /main contentns row -->
	@if (session('status'))
	<!-- main contentns row -->
	<div>
		<div class="">
			<div class="alert alert-success" role="alert">
				{{ session('status') }}
			</div>
		</div>
	</div>
	<!-- /main contentns row -->
	@endif
		<view-inventory-dust
		>
		</view-inventory-dust>
@endsection
