@extends('layouts.mm_main')

@section('content')
<div id="maincontents">

<?php 
$vbind_product_code = !empty($_GET["product_code"]) ? "v-bind:product_code=\"".$product_code."\"" : "";
$vbind_mdate = !empty($_GET["mdate"]) ? "v-bind:mdate=\"".$mdate."\"" : "";
$vbind_orderfr = !empty($_GET["orderfr"]) ? "v-bind:orderfr=\"".$orderfr."\"" : "";

//echo "vbind orderfr = ".$vbind_orderfr."<br>\n";

$html_view = <<<EOF
	<mm-stock
	{$vbind_product_code}
	{$vbind_mdate}
	{$vbind_orderfr}
	>
	</mm-stock>
	
EOF;

echo $html_view;

?>

@endsection
