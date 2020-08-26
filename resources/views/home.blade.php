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
					<span class="float-sm-right font-size-sm">
					</span>
				</div>
				<!-- /.panel header -->
				<div class="card-body pt-2">
					<!-- panel contents -->
					<!-- .row -->
					<div class="row justify-content-between">
						<home-component
							v-bind:authusers="{{ $authusers }}"
							v-bind:accountid='{{ $accountid }}'
							v-bind:edition="{{ $edition }}"
							v-bind:isexistdownload="{{ $isexistdownload }}"
							v-bind:settingcompanies="{{ $settingtable['companies'] }}"
							v-bind:settingdepartments="{{ $settingtable['departments'] }}"
							v-bind:settingsettings="{{ $settingtable['settings'] }}"
							v-bind:settingworkingtimetables="{{ $settingtable['working_timetables'] }}"
							v-bind:settingcalendarsettinginformations="{{ $settingtable['calendar_setting_informations'] }}"
							v-bind:settingusers="{{ $settingtable['users'] }}"
							v-bind:menudatas="{{ $menu_selections }}"
							v-bind:const_generaldatas="{{ $const_general_datas }}"
							v-bind:feature_item_selections="{{ $feature_item_selections }}"
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
