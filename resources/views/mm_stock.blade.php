@extends('layouts.mm_main')

@section('content')
<div id="maincontents">
	@include('layouts.stockmenu')

<?php 
$vbind_product_id2 = !empty($_GET["product_id2"]) ? "v-bind:product_id2=\"".$product_id2."\"" : "";
$vbind_mdate = !empty($_GET["mdate"]) ? "v-bind:mdate=\"".$mdate."\"" : "";
$vbind_orderfr = !empty($_GET["orderfr"]) ? "v-bind:orderfr=\"".$orderfr."\"" : "";

//echo "vbind orderfr = ".$vbind_orderfr."<br>\n";

$html_view = <<<EOF
	<mm-stock
	{$vbind_product_id2}
	{$vbind_mdate}
	{$vbind_orderfr}
>
	</mm-stock>
	
EOF;

echo $html_view;

?>

@endsection
