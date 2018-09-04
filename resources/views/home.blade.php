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

	<div class="row">
		<h2><i class="fa fa-chart-line"></i> ยอดขายปี <span class="labelYear">2018</span></h2>
		<canvas id="monthRevenueChart" width="400" height="150"></canvas>
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
		}
	}	

	get.revenue('today').done(function (res) {
		$("#today_rev").html(res);
	});
	
	get.revenue('thisMonth').done(function (res) {
		$("#month_rev").html(res);
	});

	get.revenue('thisYear').done(function (res) {
		$("#year_rev").html(res);
	});

	$("#today").html(
		d.getDate() + ' ' +
        months[d.getMonth()] + ' ' +
        d.getFullYear() 
	);
	$("#month").html(
		months[d.getMonth()]
	);
	$("#year").html(
		d.getFullYear()
	);
	$(".labelYear").html(
		d.getFullYear()
	)

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
			datasets: [
				{
					label: null,
					data: revenueData,
					backgroundColor: [
						'rgba(54, 162, 235, 0.2)',
					],
					borderColor: [
						'rgba(54, 162, 235, 1)',
					],
					borderWidth: 1
				}
			]
		},
		options: {
			legend: {
				display: false,
			},
			showTooltips: false,
			onAnimationComplete: function () {

				var ctx = this.chart.ctx;
				ctx.font = this.scale.font;
				ctx.fillStyle = this.scale.textColor
				ctx.textAlign = "center";
				ctx.textBaseline = "bottom";

				this.datasets.forEach(function (dataset) {
					dataset.points.forEach(function (points) {
						ctx.fillText(points.value, points.x, points.y - 10);
					});
				})
			}
		}
	});
</script>

@endsection
