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
							<img class="icon-size-rg mr-3" src="{{ asset('images/round-restore-w.svg') }}" alt="">ユーザー情報設定
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
		@can('admin-higher')
			<edit-user
				v-bind:authusers="{{ $authusers }}"
				v-bind:settingcompanies="{{ $settingtable['companies'] }}"
				v-bind:settingdepartments="{{ $settingtable['departments'] }}"
				v-bind:settingsettings="{{ $settingtable['settings'] }}"
				v-bind:settingworkingtimetables="{{ $settingtable['working_timetables'] }}"
				v-bind:settingcalendarsettinginformations="{{ $settingtable['calendar_setting_informations'] }}"
				v-bind:settingusers="{{ $settingtable['users'] }}"
				v-bind:const_generaldatas="{{ $const_general_datas }}"
			>
			</edit-user>
		@else
		<edit-user
				v-bind:authusers="{{ $authusers }}"
				v-bind:settingcompanies="{{ $settingtable['companies'] }}"
				v-bind:settingdepartments="{{ $settingtable['departments'] }}"
				v-bind:settingsettings="{{ $settingtable['settings'] }}"
				v-bind:settingworkingtimetables="{{ $settingtable['working_timetables'] }}"
				v-bind:settingcalendarsettinginformations="{{ $settingtable['calendar_setting_informations'] }}"
				v-bind:settingusers="{{ $settingtable['users'] }}"
				v-bind:const_generaldatas="{{ $const_general_datas }}"
			>
			</edit-user>
		@endcan
		<!-- /main contentns row -->
@endsection
