@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
    @include('layouts.sidemenu')
        <!-- main contents -->
        <div class="col-xl-10">
            <daily-working-information></daily-working-information>
		</div>
		<!-- /.container-fluid -->
	</body>
</html>
@endsection
