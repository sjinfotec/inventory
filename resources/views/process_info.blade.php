@extends('layouts.mobile')

@section('content')
				<!-- main contents -->
				<div class="col-xl-10">
					@if (session('status'))
					<!-- main contentns row -->
					<div class="row justify-content-between">
						<!-- .panel -->
						<div class="col-md pt-3">
							<div class="card shadow-pl">
								<div class="card-body pt-2">
									<div class="alert alert-success" role="alert">
										{{ session('status') }}
									</div>
								</div>
							</div>
						</div>
						<!-- /.panel -->
					</div>
					<!-- /main contentns row -->
					@endif
					<process-info
						v-bind:order_no="{{ $order_no }}"
						v-bind:seq="{{ $seq }}"
						v-bind:device="{{ $device }}"
						v-bind:user_code="{{ $user_code }}"
					></process-info>
					<!-- /main contentns row -->
@endsection
