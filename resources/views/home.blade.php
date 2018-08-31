@extends('layouts.app')

@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<link rel="stylesheet" href="{{ asset('./css/dashboard.css') }} "/>
<div class="container fixed-bottom main" >
        <!-- This is for Sub Menu -->
    <div class="row">
		<div class="col-md-4">
			<div class="dash-box dash-box-color-1">
				<div class="dash-box-icon">
					<i class="fa fa-shopping-cart"></i>
				</div>
				<div class="dash-box-body">
					<span class="dash-box-count">8,252</span>
					<span class="dash-box-title">ยอดขายวันนี้</span> <br/>
                    <span class="badge badge-light">02/02/1020</span>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="dash-box dash-box-color-2">
				<div class="dash-box-icon">
					<i class="fa fa-calendar"></i>
				</div>
				<div class="dash-box-body">
					<span class="dash-box-count">100</span>
					<span class="dash-box-title">ยอดขายของเดือนนี้</span> <br/>
                    <span class="badge badge-light">กุมภาพันธ์</span>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="dash-box dash-box-color-3">
				<div class="dash-box-icon">
                    <i class="fa fa-inbox"></i>
				</div>
				<div class="dash-box-body">
					<span class="dash-box-count">2502</span>
					<span class="dash-box-title">ยอดขายของปีนี้</span> </br>
                    <span class="badge badge-light">2018</span>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
</script>

@endsection

@section('page_script')

@endsection
