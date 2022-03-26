@extends('layouts.main')

@section('content')
<!-- main contents -->
<div id="maincontents">
	<!--
	<div class="">
		<h1 class="">
			<img class="iconsize_rg" src="{{ asset('images/round-restore-w.svg') }}" alt="">在庫・預かり管理
		</h1>
	</div>
	-->
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

<?php 
$vbind_order_info = !empty($_GET["order_info"]) ? "v-bind:order_info=\"".$order_info."\"" : "";
$vbind_order_no = !empty($_GET["order_no"]) ? "v-bind:order_no=\"".$order_no."\"" : "";
$vbind_company_id = !empty($_GET["company_id"]) ? "v-bind:company_id=\"".$company_id."\"" : "";
$vbind_product_id2 = !empty($_GET["product_id2"]) ? "v-bind:product_id2=\"".$product_id2."\"" : "";
$vbind_supply_day = !empty($_GET["supply_day"]) ? "v-bind:supply_day=\"".$supply_day."\"" : "";
$vbind_order_day = !empty($_GET["order_day"]) ? "v-bind:order_day=\"".$order_day."\"" : "";
$vbind_orderfr = !empty($_GET["orderfr"]) ? "v-bind:orderfr=\"".$orderfr."\"" : "";

//echo "vbind orderfr = ".$vbind_orderfr."<br>\n";

$html_view = <<<EOF
	<view-inventory-z
		{$vbind_order_info}
		{$vbind_order_no}
		{$vbind_company_id}
		{$vbind_product_id2}
		{$vbind_supply_day}
		{$vbind_order_day}
		{$vbind_orderfr}
	>
	</view-inventory-z>
	
EOF;

echo $html_view;

?>

@endsection
