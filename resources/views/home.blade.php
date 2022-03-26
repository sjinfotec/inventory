@extends('layouts.main')

@section('content')
<div id="home_cnt">
					<!--
					<div class="">
						<h1>ホーム</h1>
					</div>
					-->
					<!-- main contentns row -->
					<div id="maincontents">
							<home-component
							>
							</home-component>
							<!--
							<home-component
								v-bind:authusers="{{ $authusers }}"
							>
							</home-component>
							-->
					</div>
					<!-- /main contentns row -->
@endsection
