@extends('layouts.mm_main')

@section('content')
<!-- main contents -->
<div id="maincontents">

<?php 
$vbind_product_name = !empty($_GET["product_name"]) ? "v-bind:product_name=\"".$product_name."\"" : "";
$vbind_mdate = !empty($_GET["mdate"]) ? "v-bind:mdate=\"".$mdate."\"" : "";
$vbind_charge = !empty($_GET["charge"]) ? "v-bind:charge=\"".$charge."\"" : "";
$vbind_orderfr = !empty($_GET["orderfr"]) ? "v-bind:orderfr=\"".$orderfr."\"" : "";

//echo "vbind orderfr = ".$vbind_orderfr."<br>\n";

$html_view = <<<EOF
	<mat-manage
		{$vbind_product_name}
		{$vbind_mdate}
		{$vbind_charge}
		{$vbind_orderfr}
	>
	</mat-manage>
	
EOF;

echo $html_view;

?>

@endsection
