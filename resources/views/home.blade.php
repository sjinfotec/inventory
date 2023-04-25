@extends('layouts.main')

@section('content')
<div id="home_cnt">
	<!-- main contentns row -->
	<div id="maincontents">

<?php 
$vbind_product_code = !empty($_GET["product_code"]) ? "v-bind:product_code=\"".$product_code."\"" : "";
$vbind_mdate = !empty($_GET["mdate"]) ? "v-bind:mdate=\"".$mdate."\"" : "";
$vbind_orderfr = !empty($_GET["orderfr"]) ? "v-bind:orderfr=\"".$orderfr."\"" : "";
$vbind_dataarr = !empty($dataarr) ? "v-bind:dataarr=".json_encode($dataarr)."" : "";

//echo "vbind json_encode ".$vbind_selecthtml."<br>\n";
//print_r($vbind_dataarr);

$html_view = <<<EOF
		<home-component
		{$vbind_product_code}
		{$vbind_mdate}
		{$vbind_orderfr}
		{$vbind_dataarr}
		>
		</home-component>
	
EOF;

echo $html_view;
?>
	</div>
	<!-- /main contentns row -->
@endsection
