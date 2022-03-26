@extends('layouts.main')

@section('content')
<div id="maincontents">
	@include('layouts.stockmenu')

<?php 
$vbind_order_info = !empty($_GET["order_info"]) ? "v-bind:order_info=\"".$order_info."\"" : "";
$vbind_order_no = !empty($_GET["order_no"]) ? "v-bind:order_no=\"".$order_no."\"" : "";
$vbind_company_id = !empty($_GET["company_id"]) ? "v-bind:company_id=\"".$company_id."\"" : "";
$vbind_product_id2 = !empty($_GET["product_id2"]) ? "v-bind:product_id2=\"".$product_id2."\"" : "";
$vbind_receipt_day = !empty($_GET["receipt_day"]) ? "v-bind:receipt_day=\"".$receipt_day."\"" : "";
$vbind_delivery_day = !empty($_GET["delivery_day"]) ? "v-bind:delivery_day=\"".$delivery_day."\"" : "";
$vbind_orderfr = !empty($_GET["orderfr"]) ? "v-bind:orderfr=\"".$orderfr."\"" : "";

//echo "vbind orderfr = ".$vbind_orderfr."<br>\n";

$html_view = <<<EOF
	<stock-work-a
		{$vbind_order_info}
		{$vbind_order_no}
		{$vbind_company_id}
		{$vbind_product_id2}
		{$vbind_receipt_day}
		{$vbind_delivery_day}
		{$vbind_orderfr}
	>
	</stock-work-a>
	
EOF;

echo $html_view;

?>

@endsection
