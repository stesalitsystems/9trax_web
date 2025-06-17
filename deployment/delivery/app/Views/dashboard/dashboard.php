<div class="container-fluid">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/jquery.scrollbar.css" />
    <!-- Smart Carousel CSS settings -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/smart-carousel.css" />
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/smart-carousel-skins.css" />        
    <!-- Smart Carousel JS -->
    <script src="<?php echo base_url() ?>assets/js/jquery.smart-carousel.js"></script>
    <style>
		@import url('https://fonts.googleapis.com/css?family=Raleway:300,400,600,700,800,900');
        /* .metor-grid .grid .text {
            position: absolute;
            left: 0px;
            bottom: 0px;
            padding: 5px 10px;
            text-align: left;
            color: #FFF;
        }
        .metor-grid .p-r15 {padding-right: 15px !important;}
        .metor-grid .col-xs-3, .metor-grid .col-xs-4, .metor-grid .col-xs-6, .metor-grid .col-xs-8, .metor-grid .col-xs-12 {
            padding-left: 5px;padding-right: 5px;}
        .p-r15 {padding-right: 15px;}
        .metor-grid .m-lr0 {margin-left: 0;margin-right: 0;}
        .metor-grid .m-r0 {margin-right: 0;}
        .m-r0 {margin-right: 0!important;}
        .m-b5 {margin-bottom: 5px;}
        .m-lr0 {margin-left: 0;margin-right: 0;}
        .metor-grid .grid {
            position: relative;
            display: block;
            margin-bottom: 10px;
            height: 13em;
            padding-top: 55px;
            padding-bottom: 65px;
            text-align: center;
            transition: transform .3s;
			box-shadow: 10px 10px 2px rgba(0,0,0,.2);
        }
        .icon-home {
            background-position: 0 0;
        }
        .icon {
            display: inline-block;
            width: 48px;
            height: 48px;
            margin: 0 auto;
            vertical-align: middle;
            background-repeat: no-repeat;
            background-image: url(<?php // echo base_url() ?>assets/images/grid-icons.png);
        }
        .metor-grid .grid .text {
			position: relative;
            left: 0px;
            bottom: 0px;
            padding: 5px 20px;
            text-align: left;
            color: #fff;
			display: block;
			font-size: 25px;
			background: rgba(0,0,0,.15);
        }
		.metor-grid .grid .text strong, .metor-grid .grid .text b {
			font-weight: normal;
		}
        .bc-7556D6 {
            background-color: #7556D6;
        }
        .metor-grid .grid:hover {
            transform: scale(1.2,1.2);
            z-index: 1;
        }
        .bc-7F3979 {
            background-color: #7F3979;
        }
        .bc-3FA0EC {
            background-color: #3FA0EC;
        }
        .bc-1B9CB2 {
            background-color: #1B9CB2;
        }
        .bc-31aa2d {
            background-color: #31aa2d;
        }
        .bc-2F5998 {
            background-color: #2F5998;
        }
        .bc-3b9f3d {
            background-color: #3b9f3d;
        }
        .bc-2f61ad {
            background-color: #2f61ad;
        }
        .bc-e96343 {
            background-color: #e96343;
        }
        .cp {
            cursor: pointer;
        }
        .bc-2f9bf2 {
            background-color: #2f9bf2;
        }
        @media screen and (max-width: 1366px)
        .metor-grid .grid {
            height: 101px!important;
            padding-top: 15px!important;
        }
        grid.css:1
        .bc-7556D6 {
            background-color: #7556D6;
        }
        .fa{
            color:#fff;
        }
        .p-dashboard{
			padding: 15px 30px 15px 30px !important;
		}
		.metor-grid .grid {
			padding: 0;
			text-decoration: none;
		}
		.num-count {
			font-family: 'Raleway', sans-serif;
			display: block;
			text-align: center;
			color: #fff;
			font-size: 70px;
			font-weight: 300;
		}
		.metor-grid .grid .text i {
			float: right;
			position: relative;
			top: 6px;
		}
		.gdr-green {
			background: rgb(185,216,58);
			background: -moz-linear-gradient(top, rgba(185,216,58,1) 0%, rgba(49,170,45,1) 99%);
			background: -webkit-linear-gradient(top, rgba(185,216,58,1) 0%,rgba(49,170,45,1) 99%);
			background: linear-gradient(to bottom, rgba(185,216,58,1) 0%,rgba(49,170,45,1) 99%);
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b9d83a', endColorstr='#31aa2d',GradientType=0 );
		}
		.gdr-red {
			background: rgb(209,97,0);
			background: -moz-linear-gradient(top, rgba(209,97,0,1) 0%, rgba(255,0,0,1) 100%);
			background: -webkit-linear-gradient(top, rgba(209,97,0,1) 0%,rgba(255,0,0,1) 100%);
			background: linear-gradient(to bottom, rgba(209,97,0,1) 0%,rgba(255,0,0,1) 100%);
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d16100', endColorstr='#ff0000',GradientType=0 );
		}
		.gdr-blue {
			background: rgb(100,160,244);
			background: -moz-linear-gradient(top, rgba(100,160,244,1) 0%, rgba(117,86,214,1) 100%); 
			background: -webkit-linear-gradient(top, rgba(100,160,244,1) 0%,rgba(117,86,214,1) 100%);
			background: linear-gradient(to bottom, rgba(100,160,244,1) 0%,rgba(117,86,214,1) 100%);
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#64a0f4', endColorstr='#7556d6',GradientType=0 );
		}
		.gdr-pink {
			background: rgb(254,144,144);
			background: -moz-linear-gradient(top, rgba(254,144,144,1) 0%, rgba(255,92,92,1) 100%);
			background: -webkit-linear-gradient(top, rgba(254,144,144,1) 0%,rgba(255,92,92,1) 100%);
			background: linear-gradient(to bottom, rgba(254,144,144,1) 0%,rgba(255,92,92,1) 100%);
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fe9090', endColorstr='#ff5c5c',GradientType=0 );
		}
        */
        .shadow {
            box-shadow: 0 .3rem .75rem 0 rgba(33,36,40,.25) !important;
        }
        .bg-success {
            background-color: #1cc88a !important;
        }
        .bg-danger {
            background-color: #e74a3b !important;
        }
        .bg-secondary {
            background-color: #858796 !important;
        }
        .card-header {
            padding: .6rem 1rem;
            position: relative;
        }
        .scroll-wrapper {
            max-height: 299px;
        }
        .scroll-element.scroll-x {
            display: none !important;
        }
        .magikk-dashboard-wrapper {
        }
        .magikk-dashboard-wrapper h1 {
            font-size: 20px;
            color: #054099;
            border-bottom: 1px solid #e9dedd;
            padding: 0 0 1rem;
            margin: 0 0 1rem;
        }
        .section-title h2 {
            font-weight: bold;
            font-size: 18px;
            line-height: 28px;
            /* color: #464950; */
            margin: 0;
        }
        .section-title h2 span {
            font-size: 14px;
            line-height: 28px;
            border-radius: 4px;
            width: 39px;
            height: 28px;
            text-align: center;
            color: #fff;
        }
        .active-users h2 span {
            background-color: #a3f57b;
            margin-left: 10px;
        }
        .inactive-users h2 span {
            background-color: #f57b7b;
            margin-left: 10px;
        }
        .sos-details h2 span {
            background-color: #e832cc;
        }
        .call-details h2 span {
            background-color: #7b38f4;
        }
        .active-users .active-users-list {
            background-color: #effeda;
            padding: 1rem;
            border-radius: 5px;
            min-height: 331px;
        }
        .inactive-users .inactive-users-list {
            background-color: #feecda;
            padding: 1rem;
            border-radius: 5px;
            min-height: 331px;
        }
        .users-list ul {
            padding: 0;
            margin: 0;
            list-style: none;
        }
        .users-list ul li {
            border-bottom-width: 1px;
            border-bottom-style: solid;
            padding-bottom: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .users-list ul li:last-child {
            padding-bottom: 0;
            margin-bottom: 0;
            border-bottom: 0;
        }
        .users-list .user-img {
            border-radius: 50%;
            /*width: 51px;*/
            height: 51px;
        }
        .users-list h3 {
             font-weight: bold;
            font-size: 15px;
            color: #2a2b2d;
            margin-bottom: 0.25rem;
            text-transform: capitalize;
        }
        .users-list p {
            font-weight: normal;
            font-size: 15px;
            color: #2a2b2d;
            margin-bottom: 0.25rem;
        }
        .users-list .text-small {
            font-size: 12px;
            margin-bottom: 0;
            font-style: italic;
        }
        .status {
            font-size: 15px;
        }
        .active-users-list .status {
            color: #48c809;
        }
        .inactive-users-list .status {
            color: #f14e22;
        }
        .user-info-wrapper {
            padding-left: 1rem;
        }
        .active-users-list li {
            border-bottom-color: #d1f3b0;
        }
        .inactive-users-list li {
            border-bottom-color: #f3d8b0;
        }
        .list-header > div {
            background-color: #fbb460;
        }
        .list-header > div span {
            font-weight: bold;
            padding: 0.5rem 1rem;
            display: block;
        }
        .list-body > div span {
            font-size: 14px;
            padding: 0.75rem 1rem;
            display: block;
        }
        .list-body.odd-list {
            background-color: #e2eced;
            margin-bottom: 0.1rem;
        }
        .list-body.even-list {
            background-color: #e0f8f9;
            margin-bottom: 0.1rem;
        }
        .list-header {
            margin-bottom: 0.1rem;
        }

        .user-profile {
            border-radius: .5rem;
            border-left: .25rem solid #fea !important;
            padding: 1.5rem 1rem;
            background: rgb(52,152,219);
            background: linear-gradient(180deg, rgba(52,152,219,1) 0%, rgba(22,108,166,1) 100%);
            color: #fff;
        }
        .user-profile .userImg {
            position: relative;
        }
        .user-profile .userImg img {
            border-radius: 50%;
            border: 4px solid #e4e4e4;
            max-width: 100%;
            height: auto;
        }
        .user-profile .userImg .online-status {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #249d5b;
            position: absolute;
            right: 8px;
            top: 8px;
            z-index: 1;
        }
        .user-profile .userInfo h3 {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: .1rem;
        }
        .user-profile .userInfo .usr-deviceno {
        }
        .user-profile .userInfo .usr-location {
            /* color: #a0a2ab;
            font-weight: bold; */
        }
        .user-profile .userInfo hr {
            margin-top: 0.6rem;
            margin-bottom: 0.6rem;
        }
        .user-profile .userInfo .usr-contact {
        }
        .user-profile .userInfo .usr-contact .text-small {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            color: #fff;
        }

        .smart-carousel {
            height: 242px;
            padding: 3rem 0;
            /* background: rgb(70,115,159);
            background: linear-gradient(180deg, rgba(70,115,159,1) 0%, rgba(52,73,94,1) 100%);
            border: 1px solid rgb(98, 137, 175); */
            background: url(../assets/images/google-map.jpg) no-repeat center;
            background-size: cover;
            border-radius: .5rem;
        }
        .smart-carousel.round-image .smart-carousel-container li {
            border-color: #ddd;
            background-color: #fff;
        }
        .smart-carousel.round-image .smart-carousel-container li.userActive.sc-selected {
            border-color: #1cc88a;
        }
        .smart-carousel.round-image .smart-carousel-container li.userInactive.sc-selected {
            border-color: #e74a3b;
        }
        .smart-carousel.round-image .smart-carousel-container li img {
            width: 150px;
            height: 150px;
            filter: grayscale(100%);
        }
        .smart-carousel.round-image .smart-carousel-container li.sc-selected img {
            filter: grayscale(0);
        }
        .smart-carousel.round-image .sc-content-wrapper {
            bottom: 0;
            top: auto;
            left: 0;
            right: 0;
            z-index: 999;
        }
        .smart-carousel .sc-content-container {
            left: 0;
        }
        .smart-carousel .sc-content-wrapper h2 {
            font-size: 16px;
            text-transform: capitalize;
            color: #fff;
        }
        .smart-carousel .sc-content-wrapper p {
            font-size: 14px;
            color: #fff;
        }
        .smart-carousel .sc-nav-button {
            margin-top: -27.5px;
        }
        .smart-carousel .sc-nav-button.sc-prev, .smart-carousel .sc-nav-button.sc-next {
            background-color: #ececec;
        }
        .smart-carousel .sc-overlay {
            opacity: 0;
        }
        .smart-carousel .sc-overlay, .smart-carousel .sc-nav-button {
            z-index: 100;
        }
		.followdiv {
			position: absolute;
			z-index: 10000;
			background-color: #f1f1f1;
			text-align: center;
			left: 25em;
			top: 10em;
			border-style: ridge;
			border-color: #3879bb;
			border-width: 1px;
		}
		.followdivheader {
			background: rgb(218, 33, 38) none repeat scroll 0% 0%;
			color: #fff;
			padding: 5px 10px;		
			text-align:left;
			cursor: move;
		}
		.followmap{
			width: 50em;
		}

        .active-users-profile {
            background: #7cb200;
            background: -moz-linear-gradient(left,  #7cb200 0%, #8fc800 100%);
            background: -webkit-linear-gradient(left,  #7cb200 0%,#8fc800 100%);
            background: linear-gradient(to right,  #7cb200 0%,#8fc800 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#7cb200', endColorstr='#8fc800',GradientType=1 );
            color: #fff;
            border-radius: .5rem;
            text-align: center;
        }
        .inactive-users-profile {
            background: #d12904;
            background: -moz-linear-gradient(left,  #d12904 0%, #ff3019 100%);
            background: -webkit-linear-gradient(left,  #d12904 0%,#ff3019 100%);
            background: linear-gradient(to right,  #d12904 0%,#ff3019 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d12904', endColorstr='#ff3019',GradientType=1 );
            color: #fff;
            border-radius: .5rem;
            text-align: center;
        }
        .active-users-profile a, .inactive-users-profile a {
            display: block;
            width: 100%;
            height: 100%;
            padding: 3em;
        }
        .active-users-profile span, .inactive-users-profile span {
            font-size: 50px;
        }

        #active-users-modal button.close, #inactive-users-modal button.close {
            position: absolute;
            right: 10px;
            top: 8px;
        }

        .scroll-wrapper > .scroll-content {
            overflow-x: hidden !important;
        }

        .sos-details .card-header {
            background-color: #3879BB !important;
        }
    </style>
    <script src="<?php echo base_url() ?>assets/js/jquery.scrollbar.js"></script>
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script type="text/javascript">
        $(document).ready(function($){
            $('.scrollbar-inner').scrollbar();
			
			//chart start
			<?php if(count($health_device)>0){ ?> 
				temperaturegraph();
			<?php } ?>
			<?php if($activehealthdevicestr != ''){ ?>
				temperaturedailygraph();
			<?php } ?>			
        });
		
		var highchartcolor = ['#Cc0000', '#2b908f', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee', '#90ee7e'];
		Highcharts.setOptions({
			colors: highchartcolor
		});
		
		function temperaturegraph(){
			var series2 = [];
			var seriesdata = {};
			var mainseries = [];
			var start_day;
			var start_month;
			var start_year;
			var deviceid = $.trim($("#health_device").val());
			var health_day = $.trim($("#health_day").val());
			$.ajax({
				url:BASEURL + "dashboard/health_device_stats",
				type:"POST",
				data:{deviceid:deviceid,health_day:health_day},
				dataType:"json"
			}).done(function (resp) {
				var dataJson = resp.device;
				start_day = parseInt(dataJson[0].start_day);
				start_month = parseInt(dataJson[0].start_month) - 1;
				start_year = parseInt(dataJson[0].start_year);
				
				for (var i = 0;i <dataJson.length; i++)
				{
					seriesdata[dataJson[i].name] = [];
					for (var j = 0;j <dataJson[i].data.length; j++)
					{
						seriesdata[dataJson[i].name].push(parseFloat(dataJson[i].data[j].temperature));
						if(i == 0){
							series2.push(parseFloat(dataJson[i].data[j].normal_temperature));
						}
					}
				}
				
				mainseries.push({
					name: 'Normal Temperature Borderline',
					data: series2,
					color: '#fecb0b'
				});
				
				for (var i = 0;i <dataJson.length; i++)
				{
					mainseries.push({
						name: dataJson[i].name,
						data: seriesdata[dataJson[i].name]
					});
				}
				
				console.log(mainseries);
				
				var highchart = new Highcharts.chart({
					chart: {
						type: 'spline',
						renderTo: 'container',
						scrollablePlotArea: {
							minWidth: 600,
							scrollPositionX: 1
						},
						backgroundColor: 'transparent'
					},
					legend: {
						itemStyle: {
							color: "#c4c4c4"
						}
					},
					title: {
						text: ''
					},
					credits: {
						enabled: false
					},
					xAxis: {
						type: 'datetime',
						labels: {
							overflow: 'justify',
							style: {
								color: '#c4c4c4'
							}
						}
					},
					yAxis: {
						title: {
							text: 'Temperature °F',
							style: {
								color: '#c4c4c4'
							}
						},
						labels: {
							style: {
								color: '#c4c4c4'
							}
						}
					},
					tooltip: {
						xDateFormat: '%H %M %S',
						valueSuffix: ' °F'
					},
					plotOptions: {
						spline: {
							lineWidth: 4,
							states: {
								hover: {
									lineWidth: 5
								}
							},
							marker: {
								enabled: false
							},
							pointInterval: 900000, // 15 min
							pointStart: Date.UTC(start_year, start_month, start_day, 0, 0, 0)
						}
					},
					series: mainseries,
					navigation: {
						menuItemStyle: {
							fontSize: '10px'
						}
					}
				});
			}).fail(function () {
				console.log("Fetch error");
			});
			
		}
		
		function temperaturedailygraph(){
			var chartSeriesData = [];
			var start_day = '<?php echo $daily_health_status['device'][0]['start_day'];?>';
			var start_month = '<?php echo $daily_health_status['device'][0]['start_month'];?>';
			var start_year = '<?php echo $daily_health_status['device'][0]['start_year'];?>';
				
			var highchart = new Highcharts.chart({
				chart: {
					type: 'spline',
					renderTo: 'container_daily',
					scrollablePlotArea: {
						minWidth: 600,
						scrollPositionX: 1
					},
					backgroundColor: 'transparent'
				},
				legend: {
					itemStyle: {
						color: "#c4c4c4"
					}
				},
				title: {
					text: ''
				},
				credits: {
					enabled: false
				},
				xAxis: {
					type: 'datetime',
					labels: {
						overflow: 'justify',
						style: {
							color: '#c4c4c4'
						}
					}
				},
				yAxis: {
					title: {
						text: 'Temperature °F',
						style: {
							color: '#c4c4c4'
						}
					},
					labels: {
						style: {
							color: '#c4c4c4'
						}
					}
				},
				tooltip: {
					valueSuffix: ' °F'
				},
				plotOptions: {
					spline: {
						lineWidth: 4,
						states: {
							hover: {
								lineWidth: 5
							}
						},
						marker: {
							enabled: false
						},
						pointInterval: 900000, // 15 min
						pointStart: Date.UTC(start_year, start_month, start_day, 0, 0, 0)
					}
				},
				series: [
				<?php for($i=0;$i<count($daily_health_status['device']);$i++) {
						$seriesdata = '';
						for($j=0;$j<count($daily_health_status['device'][$i]['data']);$j++) { 
							if($seriesdata == ''){
								$seriesdata = $daily_health_status['device'][$i]['data'][$j]['temperature'];
							}
							else {
								$seriesdata .= ','.$daily_health_status['device'][$i]['data'][$j]['temperature'];
							}
						}
				?>
					{
						name: '<?php echo $daily_health_status['device'][$i]['devicename']; ?>',
						data: [<?php echo $seriesdata; ?>],
						<?php if($daily_health_status['device'][$i]['devicename'] == 'Normal Temperature Borderline'){ ?>
						color: '#fecb0b'
						<?php } ?>
					},
				<?php } ?>
				],
				navigation: {
					menuItemStyle: {
						fontSize: '10px'
					}
				}
			});
			window.dispatchEvent(new Event('resize'));
		}

        jQuery( window ).load(function() {
            //Transparent carousel
            jQuery("#transparent-carousel").smartCarousel({
                itemWidth:150,
                itemHeight:200,
                distance:-25,
                selectedItemDistance:0,
                selectedItemZoomFactor:0.9,
                unselectedItemZoomFactor:0.65,
                unselectedItemAlpha:0.25,
                motionStartDistance:140,
                topMargin:0,
                gradientStartPoint:0.36,
                gradientOverlayColor:"#fff",
                gradientOverlaySize:190,
                selectByClick:false
            });                
        });
    </script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.12.1/ol.css" type="text/css">
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/animate.css">
	<link href="<?php echo base_url() ?>assets/css/chosen.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo base_url() ?>assets/css/ol3gm.css" rel="stylesheet" type="text/css"/>
	<script src="<?php echo base_url() ?>assets/js/BootSideMenu.js"></script>
	<script src="<?php echo base_url() ?>assets/js/chosen.jquery.min.js" type="text/javascript"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.12.1/ol.js" type="text/javascript"></script>
	<script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap-notify.min.js"></script>
    
    <div class="magikk-dashboard-wrapper">
        <div class="row">
            <div class="col-xs-12 col-md-4">
                <div class="user-profile shadow">
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="userImg text-center">
                                <?php if($sessdata->profile_image != ""){ ?>
								<img src="<?php echo base_url().'uploads/users/'.$sessdata->user_id.'/'.$sessdata->profile_image;?>" alt="" height="100" />
								<?php }else{ ?>
								<img src="<?php echo base_url().'assets/images/no_image.jpg';?>" alt="" height="100" />
								<?php } ?>
                                <span class="online-status"></span>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-8">
                            <div class="userInfo text-left">
                                <h3><?php echo $sessdata->firstname." ".$sessdata->lastname; ?></h3>
                                <hr />
                                <div class="usr-contact">
                                    <?php /* <div class="row">
                                        <div class="col-xs-12 col-md-4">
                                            <div class="text-small">Phone</div>
                                            <div><?php echo $sessdata->mobile; ?></div>
                                        </div>
                                        <div class="col-xs-12 col-md-8">
                                            <div class="text-small">Email</div>
                                            <div><?php echo $sessdata->email; ?></div>
                                        </div>
                                    </div> */ ?>
                                    <div class="usr-phone">
                                        <div class="text-small">Phone</div>
                                        <?php echo $sessdata->mobile; ?>
                                    </div>
                                    <hr />
                                    <div class="usr-email">
                                        <div class="text-small">Email</div>
                                        <?php echo $sessdata->email; ?>
                                    </div>
                                    <hr />
                                    <div class="usr-location">
                                        <div class="text-small">Address</div>
                                        <?php echo $sessdata->address; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-8">
                <!--
                ########################################
                    - Transparent Carousel / Start -
                ########################################
                -->
                <div id="transparent-carousel" class="smart-carousel round-image transparent shadow">
                    <div class="smart-carousel-wrapper">
                        <ul class="smart-carousel-container">
                            <?php 
							if(count($active_devicedetails)>0){ 
							foreach($active_devicedetails as $active_each){
							?>
							<li class="userActive">
                                <img src="<?php echo $active_each['icon_details']; ?>" alt="User" />
                                <div class="sc-content">
                                    <h2><?php echo $active_each['device_name']; ?></h2>
                                    <p>Serial No. <?php echo $active_each['serial_no']; ?></p>
                                </div>
                            </li>
							<?php } } ?>
							<?php 
							if(count($inactive_devicedetails)>0){ 
							foreach($inactive_devicedetails as $inactive_each){
							?>
                            <li class="userInactive">
                                <img src="<?php echo $inactive_each['icon_details']; ?>" alt="User" />
                                <div class="sc-content">
                                    <h2><?php echo $inactive_each['device_name']; ?></h2>
                                    <p>Serial No. <?php echo $inactive_each['serial_no']; ?></p>
                                </div>
                            </li> 
							<?php } } ?>
                        </ul>
                    </div>
                </div>
                <!--
                ######################################
                    - Transparent Carousel / End -
                ######################################
                -->
            </div>
        </div>
        <div style="height:30px;"></div>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="active-users-profile shadow">
                    <a data-toggle="modal" data-target="#active-users-modal" style="cursor: pointer;">
                        <h2>Users On Duty</h2>
                        <span><?php echo count($active_devicedetails);?></span>
                    </a>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="inactive-users-profile shadow">
                    <a data-toggle="modal" data-target="#inactive-users-modal" style="cursor: pointer;">
                        <h2>Users Off Duty</h2>
                        <span><?php echo count($inactive_devicedetails);?></span>
                    </a>
                </div>
            </div>
        </div>
		<div style="height:30px;"></div>
		<?php if($activehealthdevicestr != ''){ ?>
		<div class="row">
			<div class="col-xs-12 col-md-12">
                <div class="sos-details section-title card shadow">
                    <div class="card-header bg-secondary text-white">
                        <h2 class="text-center">Today's Health Status</h2>
                    </div>
				</div>
			</div>
		</div>
		<div style="height:10px;"></div>
        <div class="row">
            <div class="col-xs-12 col-md-12">
				<div id="container_daily" style="background-color:#323232;"></div>
			</div>
		</div>
        <div style="height:30px;"></div>
		<?php } ?>
		<?php if(count($health_device)>0){ ?>
		<div class="row">
			<div class="col-xs-12 col-md-12">
                <div class="sos-details section-title card shadow">
                    <div class="card-header bg-secondary text-white">
                        <h2 class="text-center">User Wise Health Status</h2>
                    </div>
				</div>
			</div>
		</div>
		<div style="height:10px;"></div>
		<div class="row">
			<div class="col-xs-12 col-md-7"></div>
			<div class="col-xs-12 col-md-3">
				<select class="form-control" name="health_device" id="health_device" onchange="temperaturegraph()">
				<?php foreach($health_device as $health_device_each){ ?>
					<option value="<?php echo $health_device_each->did; ?>"><?php echo $health_device_each->device_name.' - '.$health_device_each->serial_no; ?></option>
				<?php } ?>
				</select>
			</div>
			<div class="col-xs-12 col-md-2">
				<select class="form-control" name="health_day" id="health_day" onchange="temperaturegraph()">
					<option value="3">Last 3 days</option>
					<option value="5">Last 5 days</option>
					<option value="7">Last 7 days</option>
				</select>
			</div>
		</div>
		<div style="height:10px;"></div>
        <div class="row">
            <div class="col-xs-12 col-md-12">
				<div id="container" style="background-color:#323232;"></div>
			</div>
		</div>
        <div style="height:30px;"></div>
		<?php } ?>
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="sos-details section-title card shadow">
                    <div class="card-header bg-secondary text-white">
                        <h2 class="text-center">Summary Report</h2>
                    </div>
                    <div class="row no-gutters list-header">
                        <div class="col-xs-12 col-md-3"><span>Device Name</span></div>
						<div class="col-xs-12 col-md-3"><span>Serial No.</span></div>
                        <div class="col-xs-12 col-md-2"><span>Last Date & Time</span></div>
                        <!--<div class="col-xs-12 col-md-2 text-center"><span>Travelled Distance</span></div>
                        <div class="col-xs-12 col-md-2 text-center"><span>Total Alert</span></div>-->
                        <div class="col-xs-12 col-md-2 text-center"><span>Total SOS</span></div>
                        <div class="col-xs-12 col-md-2 text-center"><span>Total Call</span></div>
                    </div>
					<?php 
					if(count($summary_data)>0){
					$i = 1;
					foreach($summary_data as $summary_data_each){
					?>
                    <div class="row no-gutters list-body <?php if($i%2 == 0){ ?>even-list<?php } else { ?>odd-list<?php } ?>">
                        <div class="col-xs-12 col-md-3"><span><strong style="text-transform:capitalize;"><?php echo $summary_data_each->dname ?></strong></span></div>
						<div class="col-xs-12 col-md-3"><span><?php echo $summary_data_each->serial ?></span></div>
                        <div class="col-xs-12 col-md-2"><span><?php echo date('d-m-Y h:i:s A', strtotime($summary_data_each->result_date.' '.$summary_data_each->lastpdatatime)); ?></span></div>
                        <!--<div class="col-xs-12 col-md-2 text-center"><span><?php echo ceil($summary_data_each->distance_cover/1000) ?> KM</span></div>
                        <div class="col-xs-12 col-md-2 text-center"><span><?php if(empty($summary_data_each->alert_no)){ echo 0;} else { echo $summary_data_each->alert_no;} ?></span></div>-->
                        <div class="col-xs-12 col-md-2 text-center"><span><?php if(empty($summary_data_each->sos_no)){ echo 0;} else { echo $summary_data_each->sos_no;} ?></span></div>
                        <div class="col-xs-12 col-md-2 text-center"><span><?php if(empty($summary_data_each->call_no)){ echo 0;} else { echo $summary_data_each->call_no;} ?></span></div>
                    </div>
					<?php $i++; } } else { ?>
					<div class="row no-gutters list-body odd-list">
						<div class="col-xs-12 col-md-12 text-center"><span>No Active Device</span></div>
					</div>
					<?php } ?>
                </div>
            </div>            
        </div>
        <div style="height:30px;"></div>
    </div>
</div>

<div id="active-users-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content active-users section-title card shadow" style="width:100%;">
            <div class="modal-header card-header bg-success text-white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2>Currently Active Users<span class="pull-right"><?php echo count($active_devicedetails);?></span></h2>
            </div>
            <div class="modal-body p-0">
                <div class="active-users-list users-list">
                    <div class="scrollbar-inner">
                        <ul>
                            <?php 
                            if(count($active_devicedetails)>0){ 
                            foreach($active_devicedetails as $active_each){
                            ?>
                            <li>
                                <div class="row">
                                    <div class="col-xs-12 col-md-1 pr-0">
                                        <img class="user-img" src="<?php echo $active_each['icon_details']; ?>" alt="User"/>
                                    </div>
                                    <div class="col-xs-12 col-md-5 pl-0 pr-0">
                                        <div class="user-info-wrapper">
                                            <h3><?php echo $active_each['device_name']; ?></h3>
                                            <p>Serial No. <?php echo $active_each['serial_no']; ?></p>
                                            <p class="text-small">Last seen: <?php echo date('d-m-Y h:i:s A', strtotime($active_each['datetime'])); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-1 pl-0 pr-0 text-center">
                                        <?php if($active_each['sos_flag'] == 1){ ?> 
                                        <a href="javascript:void(0);" title="SOS Alert"><img src="<?php echo base_url() ?>assets/images/sos-active.png" alt="SOS" width="24" height="24" /></a>
                                        <?php } else { ?>
                                        <a href="javascript:void(0);" title="SOS Alert"><img src="<?php echo base_url() ?>assets/images/sos-inactive.png" alt="SOS" width="24" height="24" /></a>
                                        <?php } ?>
                                    </div>
                                    <div class="col-xs-12 col-md-1 pl-0 pr-0 text-center">
                                        <?php if($active_each['call_flag'] == 1){ ?>
                                        <a href="javascript:void(0);" title="Call"><img src="<?php echo base_url() ?>assets/images/call-active.png" alt="Call" width="24" height="24" /></a>
                                        <?php } else { ?>
                                        <a href="javascript:void(0);" title="Call"><img src="<?php echo base_url() ?>assets/images/call-inactive.png" alt="Call" width="24" height="24" /></a>
                                        <?php } ?>
                                    </div>
                                    <div class="ccol-xs-12 col-md-1 pl-0 pr-0 text-center">
                                        <?php if($active_each['alert_flag'] == 1){ ?>
                                        <a href="javascript:void(0);" title="Alert"><img src="<?php echo base_url() ?>assets/images/alert-active.png" alt="Alert" width="24" height="24" /></a>
                                        <?php } else { ?>
                                        <a href="javascript:void(0);" title="Alert"><img src="<?php echo base_url() ?>assets/images/alert-inactive.png" alt="Alert" width="24" height="24" /></a>
                                        <?php } ?>
                                    </div>
                                    <div class="col-xs-12 col-md-1 pl-0 pr-0 text-center">
                                        <a href="javascript:void(0);" title="View on Map" onclick="followdevice(<?php echo $active_each['device_id']; ?>)"><img src="<?php echo base_url() ?>assets/images/map-view.png" alt="View on Map" width="24" height="24" /></a>
                                    </div>
                                </div>
                            </li>
                            <?php } } else {
                            ?>
                            <li>
                                <div class="row">
                                    <div class="col-xs-12 col-md-12 pl-0 pr-0">
                                        <div class="user-info-wrapper">
                                            <h3>No Active Device</h3>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="inactive-users-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content inactive-users section-title card shadow" style="width:100%;">
            <div class="modal-header card-header bg-danger text-white">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2>Currently Inactive Users<span class="pull-right"><?php echo count($inactive_devicedetails);?></span></h2>
            </div>
            <div class="modal-body p-0">
                <div class="inactive-users-list users-list">
                    <div class="scrollbar-inner">
                        <ul>
                            <?php 
                            if(count($inactive_devicedetails)>0){ 
                            foreach($inactive_devicedetails as $inactive_each){
                            ?>
                            <li>
                                <div class="row">
                                    <div class="col-xs-12 col-md-1 pr-0">
                                        <img class="user-img" src="<?php echo $inactive_each['icon_details']; ?>" alt="User" />
                                    </div>
                                    <div class="col-xs-12 col-md-8 pl-0 pr-0">
                                        <div class="user-info-wrapper">
                                            <h3><?php echo $inactive_each['device_name']; ?></h3>
                                            <p>Serial No. <?php echo $inactive_each['serial_no']; ?></p>
                                            <p class="text-small">Last seen: <?php echo date('d-m-Y h:i:s A', strtotime($inactive_each['datetime'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php } } else {
                            ?>
                            <li>
                                <div class="row">
                                    <div class="col-xs-12 col-md-12 pl-0 pr-0">
                                        <div class="user-info-wrapper">
                                            <h3>No Inactive Device</h3>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var styles = [
	'Road',
	'RoadOnDemand',
	'Aerial',
	'AerialWithLabels'
];
var bingLayers = [];
var i,ii;
for (i = 0,ii = styles.length;i < ii;++i) {
	bingLayers.push(new ol.layer.Tile({
		visible:false,
		preload:Infinity,
		source:new ol.source.BingMaps({
			key:'AiRWdWuzR_KVYwYjKRF96X09xA3DEhO_bPDdol4UC7nmE9D6PTGFrXWMpYG2RFnd',
			imagerySet:styles[i]
					// use maxZoom 19 to see stretched tiles instead of the BingMaps
					// "no photos at this zoom level" tiles
					// maxZoom: 19
		})
	}));
}

// google osm layer push
bingLayers.push(new ol.layer.Tile({
type: 'base',
title: 'Google Streetmaps',
visible: true,
source: new ol.source.OSM({
url: 'http://mt{0-3}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
attributions: [
new ol.Attribution({ html: '© Google' }),
new ol.Attribution({ html: '<a href="https://developers.google.com/maps/terms">Terms of Use.</a>' })
]
})
}));

var lat = 23.2599;
var lon = 77.4126;

// follow device start
	
var mapvars = {};
var trackSourcevars = {};
var trackLayervars = {};
var vectorSourcevars = {};
var vectoLayervars = {};
var locateSourcevars = {};
var locateLayervars = {};
var followajaxvars = {};
var fetchDataFollowTimeoutCallvars = {};
var followdevid = 1;
var followfeaturevars = {};
var followlatlonvars = {};
var followtrackonSourcevars = {};
var followtrackonLayervars = {};
var followfeaturevarspole = {};
var followlatlonvarspole = {};
var followtrackonSourcevarspole = {};
var followtrackonLayervarspole = {};

var GEOSERVER_URL = '<?php echo GEOSERVER_URL; ?>';

function followdevice(deviceid) {
	$("#mydiv"+followdevid).remove();		
	if(mapvars['followmap' + followdevid]){
		delete mapvars['followmap' + followdevid];
		trackLayervars['followmap' + followdevid].getSource().clear();
		locateLayervars['followmap' + followdevid].getSource().clear();
		followajaxvars['followmap' + followdevid].abort();
		clearTimeout(fetchDataFollowTimeoutCallvars['followmap' + followdevid]);
	}
	$(".magikk-dashboard-wrapper").append('<div id="mydiv'+followdevid+'" class="followdiv"><div id="mydiv'+followdevid+'header" class="followdivheader">Follow Device <a href="javascript:void(0);" style="float: right;color: #fff;" onclick="deletefollow('+followdevid+')"><i class="fa fa-times" aria-hidden="true" style="padding: 0px; line-height: 11px; margin: 0px 6px 0px 0px; overflow: hidden;"></i></a></div><div id="followmap'+followdevid+'" class="followmap"></div></div>');
	
	vectorSourcevars['followmap' + followdevid] = new ol.source.Vector({});
	vectoLayervars['followmap' + followdevid] = new ol.layer.Vector({
		source:vectorSourcevars['followmap' + followdevid]
	});
	locateSourcevars['followmap' + followdevid] = new ol.source.Vector({});		
	locateLayervars['followmap' + followdevid] = new ol.layer.Vector({
		source:locateSourcevars['followmap' + followdevid]
	});
	trackSourcevars['followmap' + followdevid] = new ol.source.Vector({});
	trackLayervars['followmap' + followdevid] = new ol.layer.Vector({
		source:trackSourcevars['followmap' + followdevid]
	});
	trackLayervars['followmap' + followdevid].setZIndex(10);
	mapvars['followmap' + followdevid] = new ol.Map({
		target:'followmap'+followdevid,// The DOM element that will contains the map
		layers:bingLayers.concat(vectoLayervars['followmap' + followdevid],trackLayervars['followmap' + followdevid],locateLayervars['followmap' + followdevid]),
		// Create a view centered on the specified location and zoom level
		view:new ol.View({
			center:ol.proj.transform([lon,lat],'EPSG:4326','EPSG:3857'),
			zoom:16,
			minZoom: 6,
			maxZoom: 20
		}),
		crossOrigin:'anonymous',
		controls: []
	});
	dragElement(document.getElementById(("mydiv"+followdevid)));
	
	//trail object start
	var followstartMarker = {};
	if(followtrackonLayervars['trackon' + followdevid]){
		followtrackonLayervars['trackon' + followdevid].getSource().clear();
		//map.removeLayer(followtrackonLayervars['trackon' + followdevid]);
		delete followfeaturevars['trackon' + followdevid];
		delete followlatlonvars['trackon' + followdevid];
		delete followtrackonLayervars['trackon' + followdevid];
		delete followtrackonSourcevars['trackon' + followdevid];
		followtrackonLayervarspole['trackon' + followdevid].getSource().clear();
		//map.removeLayer(followtrackonLayervarspole['trackon' + followdevid]);
		delete followfeaturevarspole['trackon' + followdevid];
		delete followlatlonvarspole['trackon' + followdevid];
		delete followtrackonLayervarspole['trackon' + followdevid];
		delete followtrackonSourcevarspole['trackon' + followdevid];
	}
	followtrackonSourcevars['trackon' + followdevid] = new ol.source.Vector({});
	followtrackonLayervars['trackon' + followdevid] = new ol.layer.Vector({
		source:followtrackonSourcevars['trackon' + followdevid]
	});
	mapvars['followmap' + followdevid].addLayer(followtrackonLayervars['trackon' + followdevid]);
	followtrackonSourcevarspole['trackon' + followdevid] = new ol.source.Vector({});
	followtrackonLayervarspole['trackon' + followdevid] = new ol.layer.Vector({
		source:followtrackonSourcevarspole['trackon' + followdevid]
	});
	mapvars['followmap' + followdevid].addLayer(followtrackonLayervarspole['trackon' + followdevid]);
	
	
	followlatlonvars['trackon' + followdevid] = [];
	followlatlonvarspole['trackon' + followdevid] = [];
	$.ajax({
		url:BASEURL + "controlcentre/getdevicetodaycoordinates",
		data:{deviceid:deviceid},
		dataType:'json',
		type:"POST",
		global:false
	}).done(function (resp) {
		var respObj = resp.getcoordinates;
		var dataLength = Object.keys(respObj).length;
		var respObjpole = resp.getpoledata;
		var dataLengthpole = Object.keys(respObjpole).length;
		var respObjpoleline = resp.getpolelinedata;
		var dataLengthpoleline = Object.keys(respObjpoleline).length;
		if (dataLength > 0) {
			for (var i = 0;i < dataLength;i++) {
				if(i == 0){
					startMarker = {deviceid:respObj[i].deviceid,longitude:respObj[i].longitude,latitude:respObj[i].latitude,faetureid:respObj[i].positionalid};
				}
				followlatlonvars['trackon' + followdevid].push([parseFloat(
									respObj[i].longitude),parseFloat(
									parseFloat(respObj[i].latitude))]);
			}
			var geom = new ol.geom.LineString(followlatlonvars['trackon' + followdevid]);
			geom.transform('EPSG:4326', 'EPSG:3857');
			followfeaturevars['trackon' + followdevid] = new ol.Feature({
				geometry: geom
			});
			// line style start
			var styleFunction = function(feature) {
				var geometry = geom;
				var styles = [
				  // linestring
				  new ol.style.Style({
					stroke: new ol.style.Stroke({
					  color: '#0069d9',
					  width: 1.5
					})
				  })
				];

				return styles;
			};
			// line style end
			
			followfeaturevars['trackon' + followdevid].setStyle(styleFunction);
			followfeaturevars['trackon' + followdevid].setId("followtrackonroute_" + followdevid);
			followtrackonSourcevars['trackon' + followdevid].addFeature(followfeaturevars['trackon' + followdevid]);                
		}
		// pole data
		 if (dataLengthpoleline > 0) {
			 var HPdevid = 1;
			 var coordinates = respObjpoleline.lonlat.split(",");
			 var j = 0;
			 var coordinateArr = [];
			 while (j != (coordinates.length - 0)) {
				coordinateArr.push([coordinates[j],coordinates[j + 1]]);
				j += 2;
			 }
			for (var k = 0;k < coordinateArr.length;k++) {	
				followlatlonvarspole['trackon' + followdevid].push([parseFloat(
										coordinateArr[k][0]),parseFloat(
										coordinateArr[k][1])]);
				if(k == 0){
					startMarkerpole = {longitude:parseFloat(coordinateArr[k][0]),latitude:parseFloat(coordinateArr[k][1]),faetureid:'start'};
				}
				if(k == (coordinateArr.length-1)){
					endMarkerpole = {longitude:parseFloat(coordinateArr[k][0]),latitude:parseFloat(coordinateArr[k][1]),faetureid:'end'};
				}				
			}
			var geompole = new ol.geom.LineString(followlatlonvarspole['trackon' + followdevid]);
			geompole.transform('EPSG:4326', 'EPSG:3857');
			followfeaturevarspole['trackon' + followdevid] = new ol.Feature({
				geometry: geompole
			});
			// line style start
			var styleFunctionpole = function(feature) {
				var geometry = geompole;
				var styles = [
				  // linestring
				  new ol.style.Style({
					stroke: new ol.style.Stroke({
					  color: 'yellow',
					  width: 5
					})
				  })
				];

				return styles;
			};
			followfeaturevarspole['trackon' + followdevid].setStyle(styleFunctionpole);
			followfeaturevarspole['trackon' + followdevid].setId("followtrackonpole_" + followdevid);
			followtrackonSourcevarspole['trackon' + followdevid].addFeature(followfeaturevarspole['trackon' + followdevid]);
			
			var startgeompole = new ol.geom.Point(ol.proj.transform([parseFloat(
				startMarkerpole.longitude),parseFloat(startMarkerpole.latitude)],
			'EPSG:4326','EPSG:3857'));
		
			var startfeaturepole = new ol.Feature({
				geometry:startgeompole,
				id:"followstartpole" + startMarkerpole.faetureid
			});
				
			var starticonURL = BASEURLIMG + 'assets/images/greenpole.png'
			
			startfeaturepole.setStyle([
				new ol.style.Style({
					image:new ol.style.Icon(({
						anchor:[0.5,1],
						size:[32,32],
						scale:0.7,
						anchorXUnits:'fraction',
						anchorYUnits:'fraction',
						opacity:1,
						src:starticonURL
					}))
				})
			]);
			locateSourcevars['followmap' + followdevid].addFeature(startfeaturepole);
			
			var endgeompole = new ol.geom.Point(ol.proj.transform([parseFloat(
					endMarkerpole.longitude),parseFloat(endMarkerpole.latitude)],
				'EPSG:4326','EPSG:3857'));
			
			var endfeaturepole = new ol.Feature({
				geometry:endgeompole,
				id:"followendpole" + endMarkerpole.faetureid
			});
				
			var endiconURL = BASEURLIMG + 'assets/images/redpole.png'
			
			endfeaturepole.setStyle([
				new ol.style.Style({
					image:new ol.style.Icon(({
						anchor:[0.5,1],
						size:[32,32],
						scale:0.7,
						anchorXUnits:'fraction',
						anchorYUnits:'fraction',
						opacity:1,
						src:endiconURL
					}))
				})
			]);
			locateSourcevars['followmap' + followdevid].addFeature(endfeaturepole);
		 }
	}).fail(function () {
		console.log("Fetch error");
	});
	//trail object end
	
	fetchFollowTracking(deviceid);		
}

function fetchFollowTracking(deviceid){
	followajaxvars['followmap' + followdevid] = $.ajax({
		url:BASEURL + "controlcentre/getfollowlocation",
		data:{deviceid:deviceid},
		dataType:'json',
		type:"POST",
		global:false
	}).done(function (resp) {			
		var respObj = resp.result;
		var deviceObj = resp.dev_data;
		//console.log(deviceObj);
		var dataLength = Object.keys(respObj).length;
		if (dataLength > 0) {
			for (var i = 0;i < dataLength;i++) {
				trackLayervars['followmap' + followdevid].getSource().clear();					
				var geom = new ol.geom.Point(ol.proj.transform([parseFloat(
					respObj[i].longitude),parseFloat(respObj[i].latitude)],
				'EPSG:4326','EPSG:3857'));
				var sizeArr = [32,30];
				var scale = 0.8;
				var feature = new ol.Feature({
					geometry:geom,
					id:"track_" + respObj[i].deviceid,
					devid:respObj[i].deviceid
				});
				var iconURL = BASEURLIMG + 'assets/iconset/device.png';
				feature.setStyle([
					new ol.style.Style({
						image:new ol.style.Icon(({
							anchor:[0.5,1],
							size:sizeArr,
							scale:scale,
							anchorXUnits:'fraction',
							anchorYUnits:'fraction',
							opacity:1,
							src:iconURL
						}))
					})
				]);
				trackSourcevars['followmap' + followdevid].addFeature(feature);
				mapvars['followmap' + followdevid].getView().setCenter(ol.proj.transform([parseFloat(
					respObj[i].longitude),parseFloat(respObj[i].latitude)],
				'EPSG:4326','EPSG:3857'));
				
				var dname = '';
				if(deviceObj.device_name){
					dname = ' ('+deviceObj.device_name+')';
				}
				
				$("#mydiv"+followdevid+"header").html(deviceObj.serial_no + dname +'<a href="javascript:void(0);" style="float: right;color: #fff;" onclick="deletefollow('+followdevid+')"><i class="fa fa-times" aria-hidden="true" style="padding: 0px; line-height: 11px; margin: 0px 6px 0px 0px; overflow: hidden;"></i></a>');
				
				followtrackonLayervars['trackon' + followdevid].getSource().clear();					
				if(followlatlonvars['trackon' + followdevid].length>0){
					followlatlonvars['trackon' + followdevid].push([parseFloat(
									respObj[i].longitude),parseFloat(
									parseFloat(respObj[i].latitude))]);
					var linegeom = new ol.geom.LineString(followlatlonvars['trackon' + followdevid]);
					linegeom.transform('EPSG:4326', 'EPSG:3857');
					followfeaturevars['trackon' + followdevid] = new ol.Feature({
						geometry: linegeom
					});
					// line style start
					var styleFunction = function(feature) {
						var geometry = linegeom;
						var styles = [
						  // linestring
						  new ol.style.Style({
							stroke: new ol.style.Stroke({
							  color: '#0069d9',
							  width: 1.5
							})
						  })
						];
						
						return styles;
					};
					
					followfeaturevars['trackon' + followdevid].setStyle(styleFunction);
					followfeaturevars['trackon' + followdevid].setId("followtrackonroute_" + followdevid);
					followtrackonSourcevars['trackon' + followdevid].addFeature(followfeaturevars['trackon' + followdevid]); 
				}
			}
		}
	}).fail(function () {
		console.log("Fetch error");
	}).complete(function () {
		fetchDataFollowTimeoutCallvars['followmap' + followdevid] = setTimeout(function () {
			fetchFollowTracking(deviceid);
		},20000);
	});
}

function deletefollow(deviceid){
	trackLayervars['followmap' + followdevid].getSource().clear();
	$("#mydiv"+followdevid).remove();
	delete mapvars['followmap' + followdevid];
	followajaxvars['followmap' + followdevid].abort();
	clearTimeout(fetchDataFollowTimeoutCallvars['followmap' + followdevid]);
	console.log(mapvars);
}

function dragElement(elmnt) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  if (document.getElementById(elmnt.id + "header")) {
	/* if present, the header is where you move the DIV from:*/
	document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
  } else {
	/* otherwise, move the DIV from anywhere inside the DIV:*/
	elmnt.onmousedown = dragMouseDown;
  }

  function dragMouseDown(e) {
	e = e || window.event;
	// get the mouse cursor position at startup:
	pos3 = e.clientX;
	pos4 = e.clientY;
	document.onmouseup = closeDragElement;
	// call a function whenever the cursor moves:
	document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
	e = e || window.event;
	// calculate the new cursor position:
	pos1 = pos3 - e.clientX;
	pos2 = pos4 - e.clientY;
	pos3 = e.clientX;
	pos4 = e.clientY;
	// set the element's new position:
	elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
	elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
  }

  function closeDragElement() {
	/* stop moving when mouse button is released:*/
	document.onmouseup = null;
	document.onmousemove = null;
  }
}

// follow device end
</script>