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
						<img class="icon-size-rg mr-3" src="{{ asset('images/home-white.svg') }}" alt="">ホーム
					</h1>
				</div>
				<!-- /.panel header -->
			</div>
		</div>
		<!-- /.panel -->
	</div>
	<!-- /main contentns row -->
	<!-- main contentns row -->
	<div class="row justify-content-between">
		<!-- .panel -->
		<div class="col-md-12 pt-3">
			<div class="card shadow-pl">
				<!-- panel header -->
				<div class="card-header bg-transparent pb-0 border-0">
					<!-- <h1 class="float-sm-left font-size-rg">操作メニュー</h1> -->
					<span class="float-sm-right font-size-sm"></span>
				</div>
				<!-- /.panel header -->
				<div class="card-body pt-2">
					<!-- panel contents -->
					<!-- .row -->
					<div class="row justify-content-between">
						<home-component
							v-bind:authusers="{{ $authusers }}"
							v-bind:generaluser="{{ Config::get('const.C025.general_user') }}"
							v-bind:generalapproveruser="{{ Config::get('const.C025.general_approver__user') }}"
							v-bind:adminuser="{{ Config::get('const.C025.admin_user') }}"
							v-bind:distribution="{{ Config::get('const.DISTRIBUTION.DISTRIBUTION') }}"
							v-bind:distribution43z="{{ Config::get('const.DISTRIBUTION_VALUE.43z') }}"
							v-bind:distributionssjjoo="{{ Config::get('const.DISTRIBUTION_VALUE.SSJJOO') }}"
							v-bind:edition="{{ Config::get('const.EDITION.EDITION') }}"
							v-bind:editiondemo="{{ Config::get('const.EDITION_VALUE.DEMO') }}"
							v-bind:editiontrial="{{ Config::get('const.EDITION_VALUE.TRIAL') }}"
							v-bind:editioncroud="{{ Config::get('const.EDITION_VALUE.CROUD') }}"
							v-bind:editionssjjoo="{{ Config::get('const.EDITION_VALUE.SSJJOO') }}"
							v-bind:editionclient="{{ Config::get('const.EDITION_VALUE.CLIENT') }}"
						>
						</home-component>
					</div>
					<!-- /.row -->
					<!-- /.panel contents -->
				</div>
			</div>
		</div>
		<!-- /.panel -->
	</div>
	<!-- /main contentns row -->
@endsection
