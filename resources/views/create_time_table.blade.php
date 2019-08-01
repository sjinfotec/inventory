@extends('layouts.app')

@section('content')
				<!-- main contents -->
				<div class="col-xl-10">
					<!-- main contentns row -->
					<div class="row justify-content-between">
						<!-- .panel -->
						<div class="col-md">
							<div class="card bg-secondary text-white pt-2 border-0 shadow-pl">
								<!-- panel header -->
								<div class="card-header bg-transparent pt-2 border-0">
									<h1 class="float-left font-size-xl">
                                        <img class="icon-size-rg mr-3" src="{{ asset('images/round-restore-w.svg') }}" alt="">勤務時間設定
                                    </h1>
								</div>
								<!-- /.panel header -->
							</div>
						</div>
						<!-- /.panel -->
					</div>
					<!-- /main contentns row -->
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
					<create-time-table></create-time-table>
					<!-- /main contentns row -->
@endsection
