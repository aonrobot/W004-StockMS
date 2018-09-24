@extends('layouts.app')

@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<link rel="stylesheet" href="https://cdn.rawgit.com/aonrobot/W004-StockMS/master/public/css/dashboard.css"/>
<div class="container fixed-bottom main" >
	<!-- This is for Sub Menu -->
	<div class="row">
		<div class="col-md-12">
			<h1>สวัสดี {{Auth::user()->name}}</h1>
		</div>
	</div>
    <div class="row">
		<div class="col-md-4">
			<div class="dash-box dash-box-color-1">
				<div class="dash-box-icon">
					<i class="fa fa-shopping-cart"></i>
				</div>
				<div class="dash-box-body">
					<span class="dash-box-count" id="today_rev">8,252</span>
					<span class="dash-box-title">ยอดขายวันนี้</span> <br/>
                    <span class="badge badge-light" id="today">02/02/1020</span>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="dash-box dash-box-color-2">
				<div class="dash-box-icon">
					<i class="fa fa-calendar"></i>
				</div>
				<div class="dash-box-body">
					<span class="dash-box-count" id="month_rev">100</span>
					<span class="dash-box-title">ยอดขายของเดือนนี้</span> <br/>
                    <span class="badge badge-light" id="month" > กุมภาพันธ์</span>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="dash-box dash-box-color-3">
				<div class="dash-box-icon">
                    <i class="fa fa-inbox"></i>
				</div>
				<div class="dash-box-body">
					<span class="dash-box-count" id="year_rev">2502</span>
					<span class="dash-box-title">ยอดขายของปีนี้</span> </br>
                    <span class="badge badge-light" id="year">2018</span>
				</div>
			</div>
		</div>
	</div>
	<hr>

	<div class="row mt-5">
		<div class="col-sm-4">
			<h2><i class="fa fa-cart-arrow-down"></i> สินค้าขายดี 5 อันดับ </h2><br>
			<div class="bsChartDIV">
				<canvas id="bestSellerChart" width="300" height="250"></canvas>	
			</div>		
		</div>
		<div class="col-sm-8">
			<h2><i class="fa fa-chart-line"></i> ยอดขายปี <span class="labelYear"></span></h2><br>
			<div class="mrChartDIV">
				<canvas id="monthRevenueChart" width="400" height="150"></canvas>
			</div>
		</div>
		
	</div>

	<div class="row mt-5">
		<div class="col-sm-6">
			<h2><i class="fa fa-chart-bar"></i> ยอดขายสินค้า เดือน<span class="labelMonth"></span></h2><br>
			<div class="alert alert-info" role="alert">
				<h6><i class="fa fa-fire"></i> กราฟจะเปิดให้ใช้งานเร็วๆ นี้ครับ</h6>
			</div>
		</div>

		<div class="col-sm-6">
			<h2><i class="fa fa-chart-bar"></i> สินค้าทำกำไรสูงสุด 5 อันดับ</h2><br>
			<div class="alert alert-info" role="alert">
				<h6><i class="fa fa-fire"></i> กราฟจะเปิดให้ใช้งานเร็วๆ นี้ครับ</h6>
			</div>
		</div>
	</div>

</div>

@endsection

@section('page_script')

<script>

	var Authorization = 'Bearer ' + $('meta[name=api-token]').attr('content');
	var d = new Date();
	var months = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
	
	var get = {
		revenue : function(type) {
			return (
				$.ajax({
					type: 'GET',
					url: "api/document/service/revenue/" + type,
					headers: {
						"Accept": "application/json",
						"Authorization": Authorization
					}
				})
			);
		},
		revenueChart : function() {
			return (
				$.ajax({
					type: 'GET',
					url: "api/document/service/yearRevenueChart",
					headers: {
						"Accept": "application/json",
						"Authorization": Authorization
					},
					async: false
				})
			)
		},
		bestSellerChart : function() {
			return (
				$.ajax({
					type: 'GET',
					url: "api/document/service/bestSeller",
					headers: {
						"Accept": "application/json",
						"Authorization": Authorization
					},
					async: false
				})
			)
		}
	}	

	get.revenue('today').done(function (res) {
		$("#today_rev").html(res.toFixed(2));
	});
	
	get.revenue('thisMonth').done(function (res) {
		$("#month_rev").html(res.toFixed(2));
	});

	get.revenue('thisYear').done(function (res) {
		$("#year_rev").html(res.toFixed(2));
	});

	$("#today").html(
		d.getDate() + ' ' +
        months[d.getMonth()] + ' ' +
        d.getFullYear() 
	);
	$("#month, .labelMonth").html(
		months[d.getMonth()]
	);
	$("#year, .labelYear").html(
		d.getFullYear()
	);

	var ctx 		= document.getElementById("monthRevenueChart");
	var revenueData = [];

	get.revenueChart().done(function (res) {
		window.revenueData = res;
	});

	var monthRevenueChart = new Chart(ctx, {
		type: 'line',
		data: {
			labels: months,
			pointRadius: 10,
			pointHoverRadius: 10,
			datasets: revenueData
		},
		options: {
			// legend: {
			// 	display: false
			// },
		}
	});

	ctx 				= document.getElementById("bestSellerChart");
	var bestSellerData 	= [];

	get.bestSellerChart().done(function (res) {
		window.bestSellerData = res;
	});

	var bestSellerChart = new Chart(ctx, {
		type: 'pie',
		data: {
			datasets: [{
				data: bestSellerData.data,
				backgroundColor: [
					'rgb(255, 118, 117)',
					'rgba(85, 239, 196,1.0)',
					'rgb(116, 185, 255)',
					'rgb(162, 155, 254)',
					'rgb(255, 234, 167)'
				]
			}],
			labels: bestSellerData.label
		},
		options: {
			cutoutPercentage: 0
		}
	});

	
	
	$(document).ready(function(){

		$warningStr = 	`
							<div class="alert alert-warning" role="alert">
								<h5><i class="fa fa-exclamation-triangle"></i> ข้อมูลยังไม่เพียงพอสำหรับสร้างกราฟในขณะนี้<br></h5>
								<i>คุณอาจจะต้องทำการสร้างรายการขายอย่างน้อย 1 รายการก่อน</i>
							</div>
						`;

		if(bestSellerData.data.length == 1){
			if(bestSellerData.data[0] == 0){
				$('.bsChartDIV').html($warningStr);
			}
		} else if (bestSellerData.data.length == 0) {
			$('.bsChartDIV').html($warningStr);
		}

		if(revenueData.reduce(function(total, num){
			return total + num
		}) == 0){
			$('.mrChartDIV').html($warningStr);
		}
		
	})
</script>

@endsection
