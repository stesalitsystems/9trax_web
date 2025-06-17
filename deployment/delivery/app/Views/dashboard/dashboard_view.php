<?php

	$sessdata = session();
	
?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<div class="container-fluid">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="javascript:void(0)">Dashboard</a>
        </li>
        <!--        <li class="breadcrumb-item active">My Dashboard</li>-->
    </ol>
    <!-- Icon Cards-->
    <style>
		@import url('https://fonts.googleapis.com/css?family=Raleway:300,400,600,700,800,900');
		
        .metor-grid .grid .text {
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
            /*height: 11em;*/
            padding-top: 55px;
            padding-bottom: 65px;
            text-align: center;
            transition: transform .3s;
            /*box-shadow: 0 0 10px rgba(0,0,0,.2);*/
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
            background-image: url(<?php echo base_url() ?>assets/images/grid-icons.png);
        }
        .metor-grid .grid .text {
			position: relative;
            left: 0px;
            bottom: 0px;
            padding: 5px 20px;
            text-align: left;
            color: #fff;
			display: block;
			font-size: 12px;
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
			font-size: 55px;
			font-weight: 300;
		}
		.num-count-multiple {
			font-family: 'Raleway', sans-serif;
			display: block;
			text-align: center;
			color: #fff;
			font-size: 17px;
			font-weight: 300;
			padding-top: 0.3em;
		}
		.metor-grid .grid .text i {
			float: right;
			position: relative;
			top: 6px;
		}
		.gdr-green {
			background: rgb(185,216,58); /* Old browsers */
			background: -moz-linear-gradient(top, rgba(185,216,58,1) 0%, rgba(49,170,45,1) 99%); /* FF3.6-15 */
			background: -webkit-linear-gradient(top, rgba(185,216,58,1) 0%,rgba(49,170,45,1) 99%); /* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgba(185,216,58,1) 0%,rgba(49,170,45,1) 99%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b9d83a', endColorstr='#31aa2d',GradientType=0 ); /* IE6-9 */
		}
		.gdr-green2 {
			background: rgb(121,199,140); /* Old browsers */
			background: -moz-linear-gradient(top, rgba(121,199,140,1) 0%, rgba(77,153,95,1) 99%); /* FF3.6-15 */
			background: -webkit-linear-gradient(top, rgba(121,199,140,1) 0%,rgba(77,153,95,1) 99%); /* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgba(121,199,140,1) 0%,rgba(77,153,95,1) 99%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#79c78c', endColorstr='#4d995f',GradientType=0 ); /* IE6-9 */
		}
		.gdr-red {
			background: rgb(209,97,0); /* Old browsers */
			background: -moz-linear-gradient(top, rgba(209,97,0,1) 0%, rgba(255,0,0,1) 100%); /* FF3.6-15 */
			background: -webkit-linear-gradient(top, rgba(209,97,0,1) 0%,rgba(255,0,0,1) 100%); /* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgba(209,97,0,1) 0%,rgba(255,0,0,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d16100', endColorstr='#ff0000',GradientType=0 ); /* IE6-9 */
		}
		.gdr-blue {
			background: rgb(100,160,244); /* Old browsers */
			background: -moz-linear-gradient(top, rgba(100,160,244,1) 0%, rgba(117,86,214,1) 100%); /* FF3.6-15 */
			background: -webkit-linear-gradient(top, rgba(100,160,244,1) 0%,rgba(117,86,214,1) 100%); /* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgba(100,160,244,1) 0%,rgba(117,86,214,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#64a0f4', endColorstr='#7556d6',GradientType=0 ); /* IE6-9 */
		}
		.gdr-pink {
			background: rgb(254,144,144); /* Old browsers */
			background: -moz-linear-gradient(top, rgba(254,144,144,1) 0%, rgba(255,92,92,1) 100%); /* FF3.6-15 */
			background: -webkit-linear-gradient(top, rgba(254,144,144,1) 0%,rgba(255,92,92,1) 100%); /* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgba(254,144,144,1) 0%,rgba(255,92,92,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fe9090', endColorstr='#ff5c5c',GradientType=0 ); /* IE6-9 */
		}
		.gdr-yellow {
			background: rgb(186,191,7); /* Old browsers */
			background: -moz-linear-gradient(to bottom, rgb(186, 191, 7) 0%,rgb(202, 206, 56) 100%); /* FF3.6-15 */
			background: -webkit-linear-gradient(to bottom, rgb(186, 191, 7) 0%,rgb(202, 206, 56) 100%); /* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgb(186, 191, 7) 0%,rgb(202, 206, 56) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fe9090', endColorstr='#ff5c5c',GradientType=0 ); /* IE6-9 */
		}
		.gdr-sub {
			background: rgb(91, 91, 91); /* Old browsers */
			background: -moz-linear-gradient(to bottom, rgb(91, 91, 91) 0%,rgb(83, 83, 83) 100%); /* FF3.6-15 */
			background: -webkit-linear-gradient(to bottom, rgb(91, 91, 91) 0%,rgb(83, 83, 83) 100%); /* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgb(91, 91, 91) 0%,rgb(83, 83, 83) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fe9090', endColorstr='#ff5c5c',GradientType=0 ); /* IE6-9 */
		}
		.gdr-sub2 {
			background: rgb(191, 107, 98); /* Old browsers */
			background: -moz-linear-gradient(to bottom, rgb(191, 107, 98) 0%,rgb(83, 83, 83) 100%); /* FF3.6-15 */
			background: -webkit-linear-gradient(to bottom, rgb(191, 107, 98) 0%,rgb(83, 83, 83) 100%); /* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgb(191, 107, 98) 0%,rgb(83, 83, 83) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fe9090', endColorstr='#ff5c5c',GradientType=0 ); /* IE6-9 */
		}
		.gdr-sub3 {
			background: rgb(49, 100, 179); /* Old browsers */
			background: -moz-linear-gradient(to bottom, rgb(49, 100, 179) 0%,rgb(83, 83, 83) 100%); /* FF3.6-15 */
			background: -webkit-linear-gradient(to bottom, rgb(49, 100, 179) 0%,rgb(83, 83, 83) 100%); /* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgb(49, 100, 179) 0%,rgb(83, 83, 83) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#82aff5', endColorstr='#775cff',GradientType=0 ); /* IE6-9 */
		}
    </style>
    <div class="row metor-grid">
        <div class="col-xs-12 col-md-12 p-r15 col-md-offset-4">
            <div class="row m-r0 m-b5 m-lr0">
				<div class="col-xs-2 col-md-2 p-dashboard">
                    <a class="grid gdr-green2" href="<?php echo base_url() ?>controlcentre/onlivedutydevice" target="_blank">
						<!--<span class="text clearfix">On Duty LIVE (<?php echo ($online_keyman_g + $online_keyman_y + $online_patrolman_g + $online_patrolman_y + $online_mate_g + $online_mate_y+$online_others_g+$online_others_y);?>) <i class="fa fa-arrow-up"></i></span>-->
						<span class="text clearfix">On Duty LIVE (<?php echo ($online_keyman_g + $online_patrolman_g + $online_mate_g + $online_others_g);?>) <i class="fa fa-arrow-up"></i></span>
						<span class="num-count-multiple" style="font-size: 11px;">Keyman (G) : <?php echo $online_keyman_g;?></span>
						<!--<span class="num-count-multiple" style="font-size: 11px;">Keyman (Y) : <?php echo $online_keyman_y;?></span>-->
						<span class="num-count-multiple" style="font-size: 11px;">Patrolman (G) : <?php echo $online_patrolman_g;?></span>
						<!--<span class="num-count-multiple" style="font-size: 11px;">Patrolman (Y) : <?php echo $online_patrolman_y;?></span>-->
						<span class="num-count-multiple" style="font-size: 11px;">Mate (G) : <?php echo $online_mate_g;?></span>
						<!--<span class="num-count-multiple" style="font-size: 11px;">Mate (Y) : <?php echo $online_mate_y;?></span>-->
						<span class="num-count-multiple" style="font-size: 11px;">Others (G) : <?php echo $online_others_g;?></span>
						<!--<span class="num-count-multiple" style="font-size: 11px;">Others (Y) : <?php echo $online_others_y;?></span>-->
                    </a>
                </div>
                <div class="col-xs-2 col-md-2 p-dashboard">
                    <a class="grid gdr-green" href="<?= base_url() ?>controlcentre/ondutydevice" target="_blank">
						<span class="text clearfix">Today’s ON (<?php echo $online;?>) <i class="fa fa-arrow-up"></i></span>
						<span class="num-count-multiple">Keyman : <?php echo $online_keyman;?>(<?php echo ($online_keyman+$offline_keyman);?>)</span>
						<span class="num-count-multiple">Patrolman : <?php echo $online_patrolman;?>(<?php echo ($online_patrolman+$offline_patrolman);?>)</span>
						<span class="num-count-multiple">Mate : <?php echo $online_mate;?></span>
						<span class="num-count-multiple">USFD : <?php echo $online_usfd;?></span>
						<span class="num-count-multiple">Others : <?php echo $online_others;?></span>
                    </a>
                </div>
                <div class="col-xs-2 col-md-2 p-dashboard">
                    <a class="grid gdr-red" href="<?= base_url() ?>controlcentre/offdutydevice" target="_blank">
						<span class="text clearfix">Today’s OFF (<?php echo $offline;?>) <i class="fa fa-arrow-down"></i></span>
						<span class="num-count-multiple">Keyman : <?php echo $offline_keyman;?>(<?php echo ($online_keyman+$offline_keyman);?>)</span>
						<span class="num-count-multiple">Patrolman : <?php echo $offline_patrolman;?>(<?php echo ($online_patrolman+$offline_patrolman);?>)</span>
						<span class="num-count-multiple">Mate : <?php echo $offline_mate;?></span>
						<span class="num-count-multiple">USFD : <?php echo $offline_usfd;?></span>
						<span class="num-count-multiple">Others : <?php echo $offline_others;?></span>
                    </a>
                </div>
				<div class="col-xs-2 col-md-2 p-dashboard">
                    <a class="grid gdr-blue cp" href="javascript:void(0)">
						<span class="text clearfix">Call <i class="fa fa-phone"></i></span>
						<span class="num-count"><?php echo $call;?></span>
                    </a>
                </div>
				<div class="col-xs-2 col-md-2 p-dashboard">
                    <a class="grid gdr-pink cp" href="javascript:void(0)">
						<span class="text clearfix">SOS <i class="fa fa-bolt"></i></span>
						<span class="num-count"><?php echo $sos;?></span>
                    </a>
                </div>
                <div class="col-xs-2 col-md-2 p-dashboard">
                    <a class="grid gdr-yellow cp" href="javascript:void(0)">
						<span class="text clearfix">Stock (<?php echo ($online_stock + $offline_stock);?>)<i class="fa fa-cubes"></i></span>
						<span class="num-count-multiple">Today's On : <?php echo $online_stock;?></span>
						<span class="num-count-multiple">Today's Off : <?php echo $offline_stock;?></span>
                    </a>
                </div>
            </div>
        </div>
		<?php if(!empty($subleveldetails) && count($subleveldetails) > 0){ ?>
		<div class="container">
			<h6>Sub Level Wise On Duty LIVE</h6>
			<div class="row">
				<?php 
				foreach($subleveldetails as $subleveldetails_each){
				?>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 p-dashboard">
                    <a class="grid gdr-sub cp" href="<?= base_url() ?>dashboard/devicelists/<?php echo $subleveldetails_each['user_id']; ?>" target="_blank" style="height: 14em;">
						<span class="text clearfix"><?php echo $subleveldetails_each['organisation'].' ('.($subleveldetails_each['sub_keyman_g']+$subleveldetails_each['sub_keyman_y']+$subleveldetails_each['sub_patrolman_g']+$subleveldetails_each['sub_patrolman_y']+$subleveldetails_each['sub_mate_g']+$subleveldetails_each['sub_mate_y']+$subleveldetails_each['sub_others_g']+$subleveldetails_each['sub_others_y']).')';?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Keyman (G) : <?php echo $subleveldetails_each['sub_keyman_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Keyman (Y) : <?php echo $subleveldetails_each['sub_keyman_y'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Patrolman (G) : <?php echo $subleveldetails_each['sub_patrolman_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Patrolman (Y) : <?php echo $subleveldetails_each['sub_patrolman_y'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Mate (G) : <?php echo $subleveldetails_each['sub_mate_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Mate (Y) : <?php echo $subleveldetails_each['sub_mate_y'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Others (G) : <?php echo $subleveldetails_each['sub_others_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Others (Y) : <?php echo $subleveldetails_each['sub_others_y'];?></span>
                    </a>
                </div>
				<?php } ?>
			</div>
			<?php if($sessdata->get('login_sess_data')['group_id'] == 3){ ?>
			<div class="row">
				<?php 
				foreach($subleveldetails2 as $subleveldetails2_each){
				?>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 p-dashboard">
                    <a class="grid gdr-sub2 cp" href="<?= base_url() ?>dashboard/devicelists/<?php echo $subleveldetails2_each['user_id']; ?>" target="_blank" style="height: 14em;">
						<span class="text clearfix"><?php echo $subleveldetails2_each['organisation'].' ('.($subleveldetails2_each['sub_keyman_g']+$subleveldetails2_each['sub_keyman_y']+$subleveldetails2_each['sub_patrolman_g']+$subleveldetails2_each['sub_patrolman_y']+$subleveldetails2_each['sub_mate_g']+$subleveldetails2_each['sub_mate_y']+$subleveldetails2_each['sub_others_g']+$subleveldetails2_each['sub_others_y']).')';?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Keyman (G) : <?php echo $subleveldetails2_each['sub_keyman_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Keyman (Y) : <?php echo $subleveldetails2_each['sub_keyman_y'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Patrolman (G) : <?php echo $subleveldetails2_each['sub_patrolman_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Patrolman (Y) : <?php echo $subleveldetails2_each['sub_patrolman_y'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Mate (G) : <?php echo $subleveldetails2_each['sub_mate_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Mate (Y) : <?php echo $subleveldetails2_each['sub_mate_y'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Others (G) : <?php echo $subleveldetails2_each['sub_others_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Others (Y) : <?php echo $subleveldetails2_each['sub_others_y'];?></span>
                    </a>
                </div>
				<?php } ?>
			</div>
			<div class="row">
				<?php 
				foreach($subleveldetails3 as $subleveldetails3_each){
				?>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 p-dashboard">
                    <a class="grid gdr-sub2 gdr-sub3 cp" href="<?= base_url() ?>dashboard/devicelists/<?php echo $subleveldetails2_each['user_id']; ?>" target="_blank" style="height: 14em;">
						<span class="text clearfix"><?php echo $subleveldetails3_each['organisation'].' ('.($subleveldetails3_each['sub_keyman_g']+$subleveldetails3_each['sub_keyman_y']+$subleveldetails3_each['sub_patrolman_g']+$subleveldetails3_each['sub_patrolman_y']+$subleveldetails3_each['sub_mate_g']+$subleveldetails3_each['sub_mate_y']+$subleveldetails3_each['sub_others_g']+$subleveldetails3_each['sub_others_y']).')';?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Keyman (G) : <?php echo $subleveldetails3_each['sub_keyman_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Keyman (Y) : <?php echo $subleveldetails3_each['sub_keyman_y'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Patrolman (G) : <?php echo $subleveldetails3_each['sub_patrolman_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Patrolman (Y) : <?php echo $subleveldetails3_each['sub_patrolman_y'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Mate (G) : <?php echo $subleveldetails3_each['sub_mate_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Mate (Y) : <?php echo $subleveldetails3_each['sub_mate_y'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Others (G) : <?php echo $subleveldetails3_each['sub_others_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Others (Y) : <?php echo $subleveldetails3_each['sub_others_y'];?></span>
                    </a>
                </div>
				<?php } ?>
			</div>
			<?php } ?>
			<?php if($sessdata->get('login_sess_data')['group_id'] == 4){ ?>
			<div class="row">
				<?php 
				foreach($subleveldetails2 as $subleveldetails2_each){
				?>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 p-dashboard">
                    <a class="grid gdr-sub2 cp" href="javascript:void(0)" style="height: 14em;">
						<span class="text clearfix"><?php echo $subleveldetails2_each['organisation'].' ('.($subleveldetails2_each['sub_keyman_g']+$subleveldetails2_each['sub_keyman_y']+$subleveldetails2_each['sub_patrolman_g']+$subleveldetails2_each['sub_patrolman_y']+$subleveldetails2_each['sub_mate_g']+$subleveldetails2_each['sub_mate_y']+$subleveldetails2_each['sub_others_g']+$subleveldetails2_each['sub_others_y']).')';?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Keyman (G) : <?php echo $subleveldetails2_each['sub_keyman_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Keyman (Y) : <?php echo $subleveldetails2_each['sub_keyman_y'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Patrolman (G) : <?php echo $subleveldetails2_each['sub_patrolman_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Patrolman (Y) : <?php echo $subleveldetails2_each['sub_patrolman_y'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Mate (G) : <?php echo $subleveldetails2_each['sub_mate_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Mate (Y) : <?php echo $subleveldetails2_each['sub_mate_y'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #0ae10a;">Others (G) : <?php echo $subleveldetails2_each['sub_others_g'];?></span>
						<span class="num-count-multiple" style="font-size: 11px;color: #dfdf2e;">Others (Y) : <?php echo $subleveldetails2_each['sub_others_y'];?></span>
                    </a>
                </div>
				<?php } ?>
			</div>
			<?php } ?>
			
			
		</div>
		<?php } ?>
		<div class="col-xs-12 col-md-12 p-r15 col-md-offset-4">
			<div id="container"></div>
		</div>
    </div>
</div>
<script>
<?php  
$categories = '';
$onduty = '';
$offduty = '';
$ondutykeyman_g='';
$ondutykeyman_y='';
$offdutykeyman='';
$ondutypatrolman_g='';
$ondutypatrolman_y='';
$offdutypatrolman='';
$ondutystock='';
$offdutystock='';
$ondutymate='';
$offdutymate='';
$ondutyothers='';
$offdutyothers='';
$ondutyothers_g = '';
$ondutyothers_y = '';

foreach($sectiondetails as $row){
	if($categories == ''){
		$categories = "'".$row['organisation']."'";
	}
	else{
		$categories .= ",'".$row['organisation']."'";
	}
	if($onduty == ''){
		if($row['onduty'] == 0){
			$onduty = '0';
		}
		else {
			$onduty = $row['onduty'];
		}
	}
	else{
		if($row['onduty'] == 0){
			$onduty = $onduty.',0';
		}
		else {
			$onduty .= ','.$row['onduty'];
		}
	}
	if($offduty == ''){
		if($row['offduty'] == 0){
			$offduty = '0';
		}
		else {
			$offduty = $row['offduty'];
		}
	}
	else{
		if($row['offduty'] == 0){
			$offduty = $offduty.',0';
		}
		else {
			$offduty .= ','.$row['offduty'];
		}
	}
	if($ondutykeyman_g == ''){
		if($row['ondutykeyman_g'] == 0){
			$ondutykeyman_g = '0';
		}
		else {
			$ondutykeyman_g = $row['ondutykeyman_g'];
		}
	}
	else{
		if($row['ondutykeyman_g'] == 0){
			$ondutykeyman_g = $ondutykeyman_g.',0';
		}
		else {
			$ondutykeyman_g .= ','.$row['ondutykeyman_g'];
		}
	}
	if($ondutykeyman_y == ''){
		if($row['ondutykeyman_y'] == 0){
			$ondutykeyman_y = '0';
		}
		else {
			$ondutykeyman_y = $row['ondutykeyman_y'];
		}
	}
	else{
		if($row['ondutykeyman_y'] == 0){
			$ondutykeyman_y = $ondutykeyman_y.',0';
		}
		else {
			$ondutykeyman_y .= ','.$row['ondutykeyman_y'];
		}
	}
	if($offdutykeyman == ''){
		if($row['offdutykeyman'] == 0){
			$offdutykeyman = '0';
		}
		else {
			$offdutykeyman = $row['offdutykeyman'];
		}
	}
	else{
		if($row['offdutykeyman'] == 0){
			$offdutykeyman = $offdutykeyman.',0';
		}
		else {
			$offdutykeyman .= ','.$row['offdutykeyman'];
		}
	}
	if($ondutypatrolman_g == ''){
		if($row['ondutypatrolman_g'] == 0){
			$ondutypatrolman_g = '0';
		}
		else {
			$ondutypatrolman_g = $row['ondutypatrolman_g'];
		}
	}
	else{
		if($row['ondutypatrolman_g'] == 0){
			$ondutypatrolman_g = $ondutypatrolman_g.',0';
		}
		else {
			$ondutypatrolman_g .= ','.$row['ondutypatrolman_g'];
		}
	}
	if($ondutypatrolman_y == ''){
		if($row['ondutypatrolman_y'] == 0){
			$ondutypatrolman_y = '0';
		}
		else {
			$ondutypatrolman_y = $row['ondutypatrolman_y'];
		}
	}
	else{
		if($row['ondutypatrolman_y'] == 0){
			$ondutypatrolman_y = $ondutypatrolman_y.',0';
		}
		else {
			$ondutypatrolman_y .= ','.$row['ondutypatrolman_y'];
		}
	}
	if($offdutypatrolman == ''){
		if($row['offdutypatrolman'] == 0){
			$offdutypatrolman = '0';
		}
		else {
			$offdutypatrolman = $row['offdutypatrolman'];
		}
	}
	else{
		if($row['offdutypatrolman'] == 0){
			$offdutypatrolman = $offdutypatrolman.',0';
		}
		else {
			$offdutypatrolman .= ','.$row['offdutypatrolman'];
		}
	}
	if($ondutystock == ''){
		if($row['ondutystock'] == 0){
			$ondutystock = '0';
		}
		else {
			$ondutystock = $row['ondutystock'];
		}
	}
	else{
		if($row['ondutystock'] == 0){
			$ondutystock = $ondutystock.',0';
		}
		else {
			$ondutystock .= ','.$row['ondutystock'];
		}
	}
	if($offdutystock == ''){
		if($row['offdutystock'] == 0){
			$offdutystock = '0';
		}
		else {
			$offdutystock = $row['offdutystock'];
		}
	}
	else{
		if($row['offdutystock'] == 0){
			$offdutystock = $offdutystock.',0';
		}
		else {
			$offdutystock .= ','.$row['offdutystock'];
		}
	}
	if($ondutymate == ''){
		if($row['ondutymate'] == 0){
			$ondutymate = '0';
		}
		else {
			$ondutymate = $row['ondutymate'];
		}
	}
	else{
		if($row['ondutymate'] == 0){
			$ondutymate = $ondutymate.',0';
		}
		else {
			$ondutymate .= ','.$row['ondutymate'];
		}
	}
	if($offdutymate == ''){
		if($row['offdutymate'] == 0){
			$offdutymate = '0';
		}
		else {
			$offdutymate = $row['offdutymate'];
		}
	}
	else{
		if($row['offdutymate'] == 0){
			$offdutymate = $offdutymate.',0';
		}
		else {
			$offdutymate .= ','.$row['offdutymate'];
		}
	}


	if($ondutyothers_g == ''){
		if($row['ondutyothers_g'] == 0){
			$ondutyothers_g = '0';
		}
		else {
			$ondutyothers_g = $row['ondutyothers_g'];
		}
	}
	else{
		if($row['ondutyothers_g'] == 0){
			$ondutyothers_g = $ondutyothers_g.',0';
		}
		else {
			$ondutyothers_g .= ','.$row['ondutyothers_g'];
		}
	}
	if($ondutyothers_y == ''){
		if($row['ondutyothers_y'] == 0){
			$ondutyothers_y = '0';
		}
		else {
			$ondutyothers_y = $row['ondutyothers_y'];
		}
	}
	else{
		if($row['ondutyothers_y'] == 0){
			$ondutyothers_y = $ondutyothers_y.',0';
		}
		else {
			$ondutyothers_y .= ','.$row['ondutyothers_y'];
		}
	}

	if($offdutyothers == ''){
		if($row['offdutyothers'] == 0){
			$offdutyothers = '0';
		}
		else {
			$offdutyothers = $row['offdutyothers'];
		}
	}
	else{
		if($row['offdutyothers'] == 0){
			$offdutyothers = $offdutyothers.',0';
		}
		else {
			$offdutyothers .= ','.$row['offdutyothers'];
		}
	}
}
/*if($sectiondetails[0]['onduty'] == 0){
	$onduty = '0,'.$onduty;
}
if($sectiondetails[0]['offduty'] == 0){
	$offduty = '0,'.$offduty;
}*/
?>
var highchartcolor = ['#32CD32', '#e3ed1f', '#0e630e', '#b2b505', '#FF1919', '#990606', '#008080', '#800040', '#5bbd5b', '#b84242', '#000075', '#8A8AFF','#2B3856'];
Highcharts.setOptions({
	colors: highchartcolor
});

Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
	credits: {
		 enabled: false
	},
    title: {
        text: ''
    },
    xAxis: {
        categories: [<?php echo $categories; ?>]
    },
    yAxis: {
        min: 0,
		title: {
            text: 'No. of devices'
        },
        stackLabels: {
            enabled: true,
            style: {
                fontWeight: 'bold',
                color: ( // theme
                    Highcharts.defaultOptions.title.style &&
                    Highcharts.defaultOptions.title.style.color
                ) || 'gray'
            }
        }
    },
    legend: {
        align: 'right',
        x: -30,
        verticalAlign: 'top',
        y: 25,
        floating: false,
        backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || 'white',
        borderColor: '#CCC',
        borderWidth: 1,
        shadow: false
    },
    tooltip: {
        headerFormat: '<b>{point.x}</b><br/>',
        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
    },
    plotOptions: {
        column: {
            stacking: 'normal',
            dataLabels: {
                enabled: true
            }
        }
    },
    series: [{
        name: 'On Duty Keyman(G)',
        data: [<?php echo $ondutykeyman_g; ?>]
    }, {
        name: 'On Duty Keyman(Y)',
        data: [<?php echo $ondutykeyman_y; ?>]
    }, {
        name: 'On Duty Patrolman(G)',
        data: [<?php echo $ondutypatrolman_g; ?>]
    }, {
        name: 'On Duty Patrolman(Y)',
        data: [<?php echo $ondutypatrolman_y; ?>]
    }, {
        name: 'Off Duty Keyman',
        data: [<?php echo $offdutykeyman; ?>]
    }, {
        name: 'Off Duty Patrolman',
        data: [<?php echo $offdutypatrolman; ?>]
    }, {
        name: 'On Duty Stock',
        data: [<?php echo $ondutystock; ?>]
    }, {
        name: 'Off Duty Stock',
        data: [<?php echo $offdutystock; ?>]
    }, {
        name: 'On Duty Mate',
        data: [<?php echo $ondutymate; ?>]
    }, {
        name: 'Off Duty Mate',
        data: [<?php echo $offdutymate; ?>]
    }, {
        name: 'On Duty Others (G)',
        data: [<?php echo $ondutyothers_g ?>]
    },{
        name: 'On Duty Others (Y)',
        data: [<?php echo $ondutyothers_y ?>]
    }, {
        name: 'Off Duty Others',
        data: [<?php echo $offdutyothers; ?>]
    }]
});
window.dispatchEvent(new Event('resize'));
</script>