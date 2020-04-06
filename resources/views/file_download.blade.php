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
							<img class="icon-size-rg mr-3" src="{{ asset('images/round-get-app-w.svg') }}" alt="">ダウンロード
						</h1>
					</div>
					<!-- /.panel header -->
				</div>
			</div>
			<!-- /.panel -->
		</div>
		<!-- /main contentns row -->
		<!-- main contentns row -->
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
		<file-download
			v-bind:authusers="{{ $authusers }}"
			v-bind:generaluser="{{ $generaluser }}"
			v-bind:generalapproveruser="{{ $generalapproveruser }}"
			v-bind:adminuser="{{ $adminuser }}"
			v-bind:distribution="{{ $distribution }}"
			v-bind:distribution43z="{{ $distribution43z }}"
			v-bind:distributionssjjoo="{{ $distributionssjjoo }}"
			v-bind:distributionmarutaka="{{ $distributionmarutaka }}"
			v-bind:edition="{{ $edition }}"
			v-bind:editiondemo="{{ $editiondemo }}"
			v-bind:editiontrial="{{ $editiontrial }}"
			v-bind:editioncroud="{{ $editioncroud }}"
			v-bind:editionssjjoo="{{ $editionssjjoo }}"
			v-bind:editionclient="{{ $editionclient }}"
		></file-download>
		<!-- /main contentns row -->
@endsection
