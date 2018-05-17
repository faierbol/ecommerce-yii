<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
 	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<?php $metaInformation = Myclass::getMetaData();//echo "<pre>";print_r($metaInformation); ?>
	<meta name="description"
		content="<?php echo isset($this->fbdescription) ? strip_tags($this->fbdescription) : $metaInformation['description']; ?>" />
	<meta name="keywords" content="<?php echo $metaInformation['metaKeywords']; ?>" />

	<meta name="language" content="en">

	<!-- For Facebook meta values -->
	<meta property="og:site_name" content="<?php echo $metaInformation['sitename']; ?>"/>
	<?php //if(isset($this->fbtitle)) { ?>
	<meta property="og:title" content="<?php echo isset($this->fbtitle) ? $this->fbtitle : $metaInformation['title']; ?>" />
	<?php //} ?>
	<meta property="og:type" content="products" />
	<meta property="og:url"
		content="<?php echo Yii::app()->request->hostInfo . Yii::app()->request->url; ?>" />
	<?php if(isset($this->fbimg)) { ?>
	<meta property="og:image" content="<?php echo $this->fbimg; ?>" />
	<meta name="twitter:image" content="<?php echo $this->fbimg; ?>">
	<meta itemprop="image" content="<?php echo $this->fbimg; ?>">
	<?php } //if(isset($this->fbdescription)) ?>
	<meta property="og:description"
		content="<?php echo isset($this->fbdescription) ? $this->fbdescription : $metaInformation['description']; ?>" />

	<!-- For Twitter meta values -->
	<meta name="twitter:title" content="<?php echo CHtml::encode($this->pageTitle); ?>">
	<meta name="twitter:description" content="<?php echo isset($this->fbdescription) ? $this->fbdescription : $metaInformation['description']; ?>">
	<meta name="twitter:card" content="summary">
	<meta name="twitter:site" content="<?php echo $metaInformation['sitename']; ?>">

	<!-- For Google+ meta values -->
	<meta itemprop="name" content="<?php echo $metaInformation['sitename']; ?>">
	<meta itemprop="description" content="<?php echo isset($this->fbdescription) ? $this->fbdescription : $metaInformation['description']; ?>">

<?php
if($this->uniqueid != "products" && $this->action->Id != "view"){
	echo '<link href="'.Yii::app()->request->hostInfo . Yii::app()->request->url.'" rel="canonical" />';
}

$baseUrl = Yii::app()->baseUrl;
Yii::app()->clientScript->registerScript('helpers','
  		yii = {
  		urls: {
		  	base: '.CJSON::encode(Yii::app()->baseUrl).'
		}
	  };',CClientScript::POS_HEAD);
Yii::app()->clientScript->registerCoreScript('jquery');
$cs = Yii::app()->getClientScript();
$cs->scriptMap=array(
		'jquery.js'=>false,
		'jquery.min.js'=>false,
		'jquery-ui.min.js'=>false
);
//Include to avoid exception in jquery
$cs->coreScriptPosition = $cs::POS_END;



//design integration
$cs->registerScriptFile($baseUrl.'/js/design/salvattore.min.js');
//$cs->registerScriptFile($baseUrl.'/js/design/jquery.js');
$cs->registerScriptFile($baseUrl.'/js/design/jquery.easing.1.3.js');
$cs->registerScriptFile($baseUrl.'/js/design/jquery.magnific-popup.min.js');
//$cs->registerScriptFile($baseUrl.'/js/design/jquery.min.js');
$cs->registerScriptFile($baseUrl.'/js/design/jquery.waypoints.min.js');
$cs->registerScriptFile($baseUrl.'/js/design/modernizr-2.6.2.min.js');
$cs->registerScriptFile($baseUrl.'/js/design/modernizr.js');
$cs->registerScriptFile($baseUrl.'/js/design/respond.min.js');


//$cs->registerScriptFile($baseUrl.'/js/design/jquery.js');
$cs->registerScriptFile($baseUrl.'/js/design/bootstrap.min.js');
//$cs->registerScriptFile($baseUrl.'/js/bootstrap.min.js');
$cs->registerScriptFile($baseUrl.'/js/front.js');
//$cs->registerScriptFile($baseUrl.'/js/design/bootstrap.js');
//$cs->registerScriptFile($baseUrl.'/js/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js');
//$cs->registerScriptFile($baseUrl.'/js/nodeClient.js');
//$cs->registerCssFile($baseUrl.'/css/bootstrap.min.css');
$cs->registerCssFile($baseUrl.'/css/design/bootstrap.min.css');
$cs->registerCssFile($baseUrl.'/css/form.css');
//$cs->registerCssFile($baseUrl.'/css/design/joysale-style.css');
//$cs->registerCssFile($baseUrl.'/css/design/animate.css');
$cs->registerCssFile($baseUrl.'/font-awesome-4.1.0/css/font-awesome.min.css');

//$cs->registerCssFile($baseUrl.'/css/main.css');


//$cs->registerScriptFile($baseUrl.'/js/design/maps.js');
//$cs->registerScriptFile($baseUrl.'/js/design/main.js');


//$cs->registerCssFile($baseUrl.'/css/design/bootstrap.css');
/*
$cs->registerCssFile($baseUrl.'/css/design/bootstrap.css.map');*/
//$cs->registerCssFile($baseUrl.'/css/design/bootstrap.min.css');
/*
$cs->registerCssFile($baseUrl.'/css/design/bootstrap.min.css.map');
$cs->registerCssFile($baseUrl.'/css/design/bootstrap.theme.css');
$cs->registerCssFile($baseUrl.'/css/design/bootstrap.theme.css.map');
$cs->registerCssFile($baseUrl.'/css/design/bootstrap.theme.min.css.map');
$cs->registerCssFile($baseUrl.'/css/design/bootstrap.theme.min.css');*/
//$cs->registerCssFile($baseUrl.'/css/joysale-style.css');
//$cs->registerCssFile($baseUrl.'/css/animate.css');
?>

<!-- <link rel="stylesheet" type="text/css"	href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection">
<link rel="stylesheet" type="text/css"	href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print">
-->
<!-- blueprint CSS framework
<link rel="stylesheet" type="text/css"	href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css">
<link rel="stylesheet" type="text/css"	href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css">
<link rel="stylesheet" type="text/css"	href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css"	href="<?php echo Yii::app()->request->baseUrl; ?>/font-awesome-4.1.0/css/font-awesome.min.css">
 -->
<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection">
<![endif]-->


<link rel="icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.png">
<?php
$siteSettings = Myclass::getSitesettings();
$search = str_replace("'", "", $_GET['search']);
if(!empty($siteSettings) && isset($siteSettings->googleapikey) && $siteSettings->googleapikey!="")
$googleapikey = "&key=".$siteSettings->googleapikey;
else
$googleapikey = "";
?>
<script	src="<?php echo Yii::app()->request->baseUrl; ?>/js/design/jquery.js"></script>
<script	src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=<?php echo $googleapikey;?>&language=<?php echo Yii::app()->language;?>"></script>

<title><?php echo CHtml::encode(isset($this->fbtitle) ? $metaInformation['sitename']." | ".$this->fbtitle : $metaInformation['sitename']." | ".$metaInformation['title']); ?></title>


<!-- Bootstrap -->
<?php if(Yii::app()->language=='ar'){?>
			<link href="<?php echo Yii::app()->createAbsoluteUrl('css/boostrap-rtl.css'); ?>" rel="stylesheet">
			<?php } else { ?>
<link href="<?php echo Yii::app()->createAbsoluteUrl('css/bootstrap.min.css'); ?>" rel="stylesheet">
<?php } ?>

<link href="<?php echo Yii::app()->createAbsoluteUrl('css/jquery-ui.css'); ?>" rel="stylesheet">

<link href="<?php echo Yii::app()->createAbsoluteUrl('css/bootstrap-slider.min.css'); ?>" rel="stylesheet">
<!--Joysale style -->
<link href="<?php echo Yii::app()->createAbsoluteUrl('css/style.css'); ?>" rel="stylesheet">
<?php if(Yii::app()->language=='ar'){?>
<link href="<?php echo Yii::app()->createAbsoluteUrl('css/style_rtl.css'); ?>" rel="stylesheet">
<?php }?>


</head>

<body>
<?php $footerSettings = Myclass::getFooterSettings();?>
<?php $logoDark = Myclass::getLogoDarkVersion(); ?>
<?php $sitePaymentModes = Myclass::getSitePaymentModes();
//echo "<pre>";print_r($sitePaymentModes); die;?>
<?php //if(!empty(Yii::app()->user->id)) {?>
	<!-- mobile Sidebar -->
	<div id="wrapper">

		<?php if(!empty(Yii::app()->user->id)) {?>
			<div id="sidebar-wrapper" class="sidebar-wraper">
		<?php }else {?>
			<div id="sidebar-wrapper">
		<?php }?>

			<div class="sidebar-menu-listng">
				<div class="joysale-mobile-Category-catgr">Category</div>
				<ul class="nav navbar-nav sidebar-nav">
				<!-- Hot fixes -->
					<li class="dropdown">
					<a class="dropdown-toggle bold joysale-for-sale disabled" data-toggle="dropdown" href="<?php echo Yii::app()->createAbsoluteUrl('/category/allcategories'); ?>" style="background:url(<?php echo Yii::app()->createAbsoluteUrl('admin/categories/resized/70/allcategories.png'); ?>) no-repeat scroll left center / 32px auto; " ><?php echo Yii::t('app','All Categories');?></a>
										  	</li>
					<!-- Hot fixes -->
				<?php  $categorypriority = Myclass::getCategoryPriority();?>

						<?php foreach($categorypriority as $key => $category):
							if($category != "empty"){
								//$getcaname =  Myclass::getCatName($category);
								$getcatdet = Myclass::getCatDetails($category);
								$getcatimage = Myclass::getCatImage($category);
								$subCategory = Myclass::getSubCategory($category);

						?>
						<!-- mobile category view-->
						<li class="dropdown  main-mobile-menu">
							<?php if(!empty($subCategory)) {?>
						<a class="dropdown-toggle" data-toggle="dropdown" href="#" style="background:url(<?php echo Yii::app()->createAbsoluteUrl('admin/categories/resized/70/'.$getcatimage); ?>) no-repeat scroll left center / 32px auto; " >
							<?php } else { ?>
						<a class="dropdown-toggle" href="<?php echo Yii::app()->createAbsoluteUrl('/category/'.$getcatdet->slug); ?>" style="background:url(<?php echo Yii::app()->createAbsoluteUrl('admin/categories/resized/70/'.$getcatimage); ?>) no-repeat scroll left center / 32px auto; " >
						<?php } ?>
							<span><?php echo Yii::t('app',$getcatdet->name); ?></span>
							<?php if(!empty($subCategory)) {?>
							<i class="angle-down"></i>
							<?php } ?>
						</a>
							<?php if(!empty($subCategory)) {?>
							<ul class="dropdown-menu">
								<?php foreach($subCategory as $key => $subCategory):
								//echo $key;
										$subCatdet = Myclass::getCatDetails($key);
								?>
								<li><a href="<?php echo Yii::app()->createAbsoluteUrl('/category/'.$getcatdet->slug.'/'.$subCatdet->slug); ?>"><?php echo Yii::t('app',$subCategory); ?></a></li>
								<?php endforeach;?>
								<li><a href="<?php echo Yii::app()->createAbsoluteUrl('/category/'.$getcatdet->slug); ?>">View all</a></li>
					  		</ul>
					  		<?php }?>
					  	</li>
					  	<!-- mobile category view end-->
						<?php } endforeach;?>
				</ul>

			</div>

			<?php if(!empty(Yii::app()->user->id)) {?>
			<div class="al-mobile-user-area">
				<a href="<?php echo Yii::app()->createAbsoluteUrl('item/products/create'); ?>" class="joysale-stuff-mob"><?php echo Yii::t('app','Sell your stuff'); ?></a>
				<a href="<?php echo Yii::app()->createAbsoluteUrl('user/profiles'); ?>" class="joysale-account"><?php echo Yii::t('app','My account'); ?></a>
				<?php if($sitePaymentModes['exchangePaymentMode'] == 1){ ?>
				<a href="<?php echo Yii::app()->createAbsoluteUrl('item/exchanges',array('type' => 'incoming')); ?>" class="joysale-exchange"><?php echo Yii::t('app','My Exchanges'); ?></a>
				<?php } ?>

					<a href="<?php echo Yii::app()->createAbsoluteUrl('user/logout'); ?>" class="joysale-logout"><?php echo Yii::t('admin','Logout'); ?></a>
			</div>
			<?php }else {?>
			<div class="mobile-user-area">
				<a href="#" data-toggle="modal" data-target="#login-modal" class="joysale-login primary-bg-color text-align-center border-radius-5"><?php echo Yii::t('app','Login'); ?></a>
				<a href="#" data-toggle="modal" data-target="#signup-modal" class="joysale-signup txt-white-color text-align-center border-radius-5"><?php echo Yii::t('app','Sign up'); ?></a>
			</div>
			<?php }?>
</div>
		</div>


	</div>
  <?php //}?>

	<!-- E o mobile Sidebar -->
<script type="text/javascript">
	/*$(function() {


	 $('.joysale-header-nav')(function(e) {
				$('.sticky-header-menu-icon').fadeOut("slow");


    });
	});*/

</script>
	<!--Header code-->
	<?php if(!empty(Yii::app()->user->id)) {?>
	<div class="joysale-header">
		<div class="container-fluid">
			<div class="row">
			  <div class="joysale-header-bar col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="header-left col-xs-5 col-sm-5 col-md-3 col-lg-2">
				<div class="joysale-logo">
					<?php $logo = Myclass::getLogo();
					echo CHtml::link(CHtml::image(Yii::app()->createAbsoluteUrl('media/logo/'.$logo),"Logo",
							array('style'=>'')),Yii::app()->createAbsoluteUrl('/')); ?>
					<!-- <a href="#"><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/design/joysale-logo.png'); ?>" alt="Menu"></a>-->
				</div>
			<div class="joysale-header-nav dropdown">
						<a class="sticky-header-menu-icon dropdown-toggle" data-toggle="dropdown" href="#">
							<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/nav.png" alt="Message">
						</a>
						<?php  $categorypriority = Myclass::getCategory();
						if(count($categorypriority) > 5) {
							$scrollbar = '';'height:205px; overflow-y:scroll;';
						} else {
							$scrollbar = '';
						}
						?>
						<ul id="dropdown-block" class="sticky-header-dropdown dropdown-menu" style="<?php echo $scrollbar; ?>">
						<!-- Hot fixes desktop-->
					<li>
					<a class="sticky-header-dropdown-height bold dropdown-toggle joysale-for-sale-sticky" href="<?php echo Yii::app()->createAbsoluteUrl('/category/allcategories'); ?>" style="background:url(<?php echo Yii::app()->createAbsoluteUrl('admin/categories/resized/70/allcategories.png'); ?>) no-repeat scroll 10px 4px / 32px auto; " ><span><?php echo Yii::t('app','All Categories');?></span></a>
										  	</li>
					<!-- Hot fixes desktop-->

							<?php foreach($categorypriority as $key => $category):
								if($category != "empty"){
									//$getcaname =  Myclass::getCatName($category);
									$getcatdet = $category;
									$getcatimage = !empty($category) ? $category->image : "";
									$subCategory = Myclass::getSubCategory($category->categoryId);

							?>
							<li>
								<a class="sticky-header-dropdown-height bold dropdown-toggle joysale-for-sale-sticky"
									href="<?php echo Yii::app()->createAbsoluteUrl('/category/'.$getcatdet->slug); ?>"
									 style="background:url(<?php echo Yii::app()->createAbsoluteUrl('admin/categories/resized/70/'.$getcatimage); ?>) no-repeat scroll 10px 4px / 32px auto; ">
									<span><?php echo Yii::t('app',$getcatdet->name); ?></span>
								</a>
							</li>
							<?php } endforeach;?>
						</ul>
					</div>

				</div>

			  	<div class="full-vheader col-md-5 col-lg-6 no-hor-padding">
					<div class="joysale-search-bar col-md-12 col-lg-12 no-hor-padding">
						<form role="form" onsubmit="return doSearch();" class="searchform navbar-form- navbar-left- search-form" style="padding-left: 0;"
						action="<?php echo Yii::app()->createAbsoluteUrl('/'); ?>" method="get">
							<div class="col-md-6 col-lg-6 no-hor-padding">
								<div class="ui-widget topsearch-locatn">
									<input id="searchval" onkeyup="ajaxSearch(this,event);" type="text" maxlength="30" placeholder="<?php echo Yii::t('app','Search products'); ?>" class="tags joysale-search-icon form-control input-search <?php echo !empty(Yii::app()->user->id) ? "" : "sign" ?>" value="<?php echo $search;?>" name="search">
								</div>
							</div>
							</form>
							<div class="col-md-6 col-lg-6 no-hor-padding">
								<div class="hme-top-location map-input-section col-md-10 col-lg-10 no-hor-padding">
									<div class="map-input-box">
										<input id="pac-input" placeholder="World Wide" value="<?php echo Yii::app()->session['place1'];?>" class="controls" autocomplete="off" type="text">
									</div>
									<!--<div class="map-select-box">
										<select id="select-mapdistance" class="select-box-arrow">
											<option value="1" selected="">1 km</option>
											<option value="5">5 km</option>
											<option value="10">10 km</option>
											<option value="20">20 km</option>
											<option value="50">50 km</option>
											<option value="700">700 km</option>
										</select>
									</div>-->
									<!--<a href="javascript:void(0);" class="location-submit-button" onclick="return gotogetLocationData();">Submit</a>-->
								</div>
								<div class="col-md-2 col-lg-2 no-hor-padding"><a href="javascript:void(0);">
									<div type="submit" class="search-go" onclick="return gotogetLocationData();">Go</div></a></div>
							</div>

					</div>
				</div>



				<!--<div class="search-for-location col-md-5 col-lg-4 no-hor-padding">
					<input id="Products_location" class="header-location-icon" name="Products[location]" value="" placeholder="Where to go?" onchange="return resetLatLong()" class="col-xs-12 col-sm-12 col-md-5 col-lg-5 no-hor-padding" autocomplete="off" type="text">

				</div>-->

				 <!-- <div class="joysale-search-bar col-md-3 col-lg-3 no-hor-padding form-group search-input-container">
				  <form role="form" class="navbar-form- navbar-left- search-form"
					style="padding-left: 0;"
					action="<?php echo Yii::app()->createAbsoluteUrl('/'); ?>"
					method="get">
					<input type="text" maxlength="30" placeholder="<?php echo Yii::t('app','Search products'); ?>" class="joysale-search-icon form-control input-search <?php echo !empty(Yii::app()->user->id) ? "" : "sign" ?>" name="search"></input>
					</form>
					</div> -->




					<div class="joysale-login-user-nav col-xs-7 col-sm-7 col-md-4 col-lg-4 pull-right">
					  <ul class="navbar-nav">


								<li class="joysale-header-message">
									<a href="<?php echo Yii::app()->createAbsoluteUrl('message'); ?>">
									<img src="<?php echo Yii::app()->createAbsoluteUrl('/images/design/message.png'); ?>" alt="Message" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Message">
								<?php
								$messageCount = Myclass::getMessageCount(Yii::app()->user->id); ?>
								<script>
									var liveCount = <?php echo $messageCount; ?>;
								</script>
								<?php
								$messageStatus = "";
								if($messageCount == 0){
									$messageStatus = "message-hide";
								}
								?>
								<span class="message-counter message-count <?php echo $messageStatus; ?>"><?php echo $messageCount; ?></span>

								</a></li>
								<span class="joysale-header-har-line"></span>
								<li class="joysale-header-message">
									<a href="<?php echo Yii::app()->createAbsoluteUrl('notification'); ?>">
										<img alt="Notification" src="<?php echo Yii::app()->createAbsoluteUrl('/images/notification.png'); ?>" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Notification">
										<?php $notificationCount = Myclass::getNotificationCount(Yii::app()->user->id);
											$notificationStatus = "";
											if($notificationCount == 0 || Yii::app()->controller->action->id == 'notification'){
												$notificationStatus = "message-hide";
											}
											?>
										<span class="message-counter <?php echo $notificationStatus; ?>"><?php echo $notificationCount; ?></span>
									</a>
								</li>
								<span class="joysale-header-har-line"></span>
									<?php
									$userImage = Myclass::getUserDetails(Yii::app()->user->id);
									if(!empty($userImage->userImage)) {
										$userimg = Yii::app()->createAbsoluteUrl('user/resized/35/'.$userImage->userImage);
										//echo CHtml::image(Yii::app()->createAbsoluteUrl('user/resized/35/'.$userImage->userImage),$userImage->username);
									} else {
										$userimg = Yii::app()->createAbsoluteUrl('user/resized/35/default/'.Myclass::getDefaultUser());
										//echo CHtml::image(Yii::app()->createAbsoluteUrl('user/resized/35/default/'.Myclass::getDefaultUser()),$userImage->username);
									}
									?>
									<li class="dropdown joysale-header-profile">
									  <a class="dropdown-toggle" data-toggle="dropdown" href="#">
									  <span class="joysale-header-profile-img img-responsive" style=" background: rgba(0, 0, 0, 0) url(<?php echo $userimg; ?>) no-repeat scroll 0 0 / cover ;"></span>
										<span class="joysale-header-down-arrow"></span>
									  </a>
									  <ul class="dropdown-menu dropdown-submenu">
										<li><a href="<?php echo Yii::app()->createAbsoluteUrl('user/profiles'); ?>"><?php echo Yii::t('app','Profile'); ?></a></li>
									<?php
									if(isset($siteSettings->promotionStatus) && $siteSettings->promotionStatus == "1")
									{
									?>
										<li><a href="<?php echo Yii::app()->createAbsoluteUrl('user/promotions',array(
												'id'=>Myclass::safe_b64encode(Yii::app()->user->id.'-'.rand(0,999)))); ?>"><?php echo Yii::t('app','My Promotions'); ?></a></li>
									<?php
									}
									?>
										<?php if($sitePaymentModes['exchangePaymentMode'] == 1){ ?>
										<li><a href="<?php echo Yii::app()->createAbsoluteUrl('item/exchanges',array('type' => 'incoming')); ?>"><?php echo Yii::t('app','My Exchanges'); ?></a></li>
										<?php } ?>
										<?php if($sitePaymentModes['buynowPaymentMode'] == 1){ ?>
										<li>
											<a href="<?php echo Yii::app()->createAbsoluteUrl('/orders'); ?>" class="joysale-exchange"><?php echo Yii::t('app','My Orders & My Sales'); ?></a>
										</li>
										<?php } ?>
										<!-- <li><a href="<?php echo Yii::app()->createAbsoluteUrl('orders'); ?>"><?php echo Yii::t('app','My Orders'); ?></a></li>
										<li><a href="<?php echo Yii::app()->createAbsoluteUrl('sales'); ?>"><?php echo Yii::t('app','My Sales'); ?></a></li>
										<li><a href="<?php echo Yii::app()->createAbsoluteUrl('coupons',array('type' => 'item')); ?>"><?php echo Yii::t('app','Coupons'); ?></a></li>
										<li><a href="<?php echo Yii::app()->createAbsoluteUrl('shippingaddress'); ?>"><?php echo Yii::t('app','Shipping Addresses'); ?></a></li>
										<li><a href="#">Chat</a></li> -->
										<li class="logout"><a href="<?php echo Yii::app()->createAbsoluteUrl('user/logout'); ?>"><?php echo Yii::t('admin','Logout'); ?></a></li>
									  </ul>
									</li>
								 <li class="joysale-header-stuff border-radius-5"><a class="joysale-camera-icon" href="<?php echo Yii::app()->createAbsoluteUrl('item/products/create'); ?>"><?php echo Yii::t('app','Sell'); ?></a></li>

					   </ul>
					   <!-- Mobile sidebar Content -->
							<div id="page-content-wrapper">
								<a class="col-xs-2 col-sm-1 col-md-1 no-hor-padding" href="#menu-toggle" id="menu-toggle"><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/design/3-line.png'); ?>" alt="Menu"></a>
							</div>
						<!-- /E o Mobile sideba -->
					</div>

					<!-- /#sidebar-wrapper -->

				</div>
			</div>
		</div>
	</div>






	<!--Mobile search bar code-->


	<div class="joysale-search-bar-bg">
		<div class="container-fluid">
			<div class="app-responsive-adjust"></div>

			<div class="joysale-search-bar-mobile col-xs-12 col-sm-12 col-md-12 no-hor-padding form-group search-input-container">

			<form role="form" class="navbar-form- navbar-left- search-form" style="padding-left: 0;" action="<?php echo Yii::app()->createAbsoluteUrl('/'); ?>"
					method="get">
					<a href="" class="joysale-icon-mobile input-search" data-toggle="modal" data-target="#search-mobile-cal">Search products</a>
				<!--<input type="text" maxlength="30" placeholder="<?php echo Yii::t('app','Search products'); ?>" class="joysale-search-icon-mobile  form-control input-search" <?php echo !empty(Yii::app()->user->id) ? "" : "sign" ?>" name="search">-->
			</form>
			</div>

		</div>
	</div>
	<!--Mobile search bar code-->

	<div class="joysale-menu">
		<div class="container-fluid">
			<div class="row" style="height: 64px;"></div>
			<div class="row">
				<nav class="navbar col-xs-12 col-sm-12 col-md-12">
					<ul class="rtl-header nav navbar-nav">
					<!-- Hot fixes -->
					<li class="dropdown">
					<a class="dropdown-toggle bold joysale-for-sale disabled" data-toggle="dropdown" href="<?php echo Yii::app()->createAbsoluteUrl('/category/allcategories'); ?>" style="background:url(<?php echo Yii::app()->createAbsoluteUrl('admin/categories/resized/70/allcategories.png'); ?>) no-repeat scroll left center / 32px auto; " ><?php echo Yii::t('app','All Categories');?></a>
										  	</li>
					<!-- Hot fixes -->
					<?php  $categorypriority = Myclass::getCategoryPriority();?>

					<?php foreach($categorypriority as $key => $category):
						if($category != "empty"){
							//$getcaname =  Myclass::getCatName($category);
							$getcatdet = Myclass::getCatDetails($category);
							$getcatimage = Myclass::getCatImage($category);
							$subCategory = Myclass::getSubCategory($category);

					?>
					<li class="dropdown">
					<a class="dropdown-toggle bold joysale-for-sale disabled" data-toggle="dropdown" href="<?php echo Yii::app()->createAbsoluteUrl('/category/'.$getcatdet->slug); ?>" style="background:url(<?php echo Yii::app()->createAbsoluteUrl('admin/categories/resized/70/'.$getcatimage); ?>) no-repeat scroll left center / 32px auto; " ><?php echo Yii::t('app',$getcatdet->name); ?></a>
						<?php if(!empty($subCategory)) {?>
						<ul id="dropdown-block" class="dropdown-menu joysale-dropdown-submenu">
							<?php foreach($subCategory as $key => $subCategory):
							//echo $key;
									$subCatdet = Myclass::getCatDetails($key);
							?>
							<li><a class="bold" href="<?php echo Yii::app()->createAbsoluteUrl('/category/'.$getcatdet->slug.'/'.$subCatdet->slug); ?>"><?php echo Yii::t('app',$subCategory); ?></a></li>
							<?php endforeach;?>
				  		</ul>
				  		<?php }?>
				  	</li>
					<?php } endforeach;?>

					</ul>
				</nav>
			</div>
		</div>
	</div>





	<?php }else{ ?>
		<!--Header code-->
		<div class="joysale-header">
		<div class="container-fluid">
		<div class="row">
		<div class="joysale-header-bar col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="header-left col-xs-5 col-sm-5 col-md-3 col-lg-2">
				<div class="joysale-logo">
					<?php $logo = Myclass::getLogo();
					echo CHtml::link(CHtml::image(Yii::app()->createAbsoluteUrl('media/logo/'.$logo),"Logo",
							array('style'=>'')),Yii::app()->createAbsoluteUrl('/')); ?>

					<!-- <a href="#"><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/design/joysale-logo.png'); ?>" alt="Menu"></a>-->
				</div>
			<div class="joysale-header-nav dropdown">
						<a class="sticky-header-menu-icon dropdown-toggle" data-toggle="dropdown" href="#">
							<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/nav.png" alt="Message">
						</a>
						<?php  $categorypriority = Myclass::getCategory();
						if(count($categorypriority) > 5) {
							$scrollbar = '';'height:205px; overflow-y:scroll;';
						} else {
							$scrollbar = '';
						}
						?>
						<ul id="dropdown-block" class="sticky-header-dropdown dropdown-menu" style="<?php echo $scrollbar; ?>">
							<!-- Hot fixes desktop-->
					<li>
					<a class="sticky-header-dropdown-height bold dropdown-toggle joysale-for-sale-sticky" href="<?php echo Yii::app()->createAbsoluteUrl('/category/allcategories'); ?>" style="background:url(<?php echo Yii::app()->createAbsoluteUrl('admin/categories/resized/70/allcategories.png'); ?>) no-repeat scroll 10px 4px / 32px auto; " ><span><?php echo Yii::t('app','All Categories');?></span></a>
										  	</li>
					<!-- Hot fixes desktop-->
							<?php foreach($categorypriority as $key => $category):
								if($category != "empty"){
									//$getcaname =  Myclass::getCatName($category);
									$getcatdet = $category;
									$getcatimage = !empty($category) ? $category->image : "";
									$subCategory = Myclass::getSubCategory($category->categoryId);

							?>
							<li>
								<a class="sticky-header-dropdown-height bold dropdown-toggle joysale-for-sale-sticky"
									href="<?php echo Yii::app()->createAbsoluteUrl('/category/'.$getcatdet->slug); ?>"
									 style="background:url(<?php echo Yii::app()->createAbsoluteUrl('admin/categories/resized/70/'.$getcatimage); ?>) no-repeat scroll 10px 4px / 32px auto; ">
									<span><?php echo Yii::t('app',$getcatdet->name); ?></span>
								</a>
							</li>
							<?php } endforeach;?>
						</ul>
					</div>

				</div>
			<div class="full-vheader col-md-5 col-lg-7 no-hor-padding">
			  	<div class="joysale-search-bar col-md-12 col-lg-12 no-hor-padding">
					<form role="form" onsubmit="return doSearch();" class="searchform navbar-form- navbar-left- search-form" style="padding-left: 0;"
					action="<?php echo Yii::app()->createAbsoluteUrl('/'); ?>" method="get">
						<div class="col-md-6 col-lg-6 no-hor-padding">
							<div class="ui-widget topsearch-locatn">
								<input type="text" id="searchval" onkeyup="ajaxSearch(this,event);" maxlength="30" placeholder="<?php echo Yii::t('app','Search products'); ?>" class="tags joysale-search-icon form-control input-search <?php echo !empty(Yii::app()->user->id) ? "" : "sign" ?>" value="<?php echo $search;?>" name="search">
							</div>
						</div>
						</form>
						<div class="col-md-6 col-lg-6 no-hor-padding">
							<div class="hme-top-location map-input-section col-md-10 col-lg-10 no-hor-padding">
								<div class="map-input-box">
									<input id="pac-input" placeholder="World Wide" value="<?php echo Yii::app()->session['place1'];?>" class="controls" autocomplete="off" type="text">
								</div>
								<!--<div class="map-select-box">
									<select id="select-mapdistance" class="select-box-arrow">
										<option value="1" selected="">1 km</option>
										<option value="5">5 km</option>
										<option value="10">10 km</option>
										<option value="20">20 km</option>
										<option value="50">50 km</option>
										<option value="700">700 km</option>
									</select>
								</div>-->
								<!--<a href="javascript:void(0);" class="location-submit-button" onclick="return gotogetLocationData();">Submit</a>-->
							</div>
							<div class="col-md-2 col-lg-2 no-hor-padding"><a href="javascript:void(0);">
								<div class="search-go" onclick="return gotogetLocationData();">Go</div></a></div>
						</div>

				</div>
			</div>
				<!--<div class="search-for-location col-md-5 col-lg-4 no-hor-padding">
					<input id="Products_location" name="Products[location]" value="" placeholder="Where to go?" onchange="return resetLatLong()" class="search-for-location-icon col-xs-12 col-sm-12 col-md-5 col-lg-5 no-hor-padding" autocomplete="off" type="text">
				</div> -->

				  <!-- <div class="joysale-search-bar col-md-3 col-lg-3 no-hor-padding form-group search-input-container">
				  <form role="form" class="navbar-form- navbar-left- search-form"
					style="padding-left: 0;"
					action="<?php echo Yii::app()->createAbsoluteUrl('/'); ?>"
					method="get">
					<input type="text" maxlength="30" placeholder="<?php echo Yii::t('app','Search products'); ?>" class="joysale-search-icon form-control input-search <?php echo !empty(Yii::app()->user->id) ? "" : "sign" ?>" name="search"></input>
					</form>
					</div>	 -->





		<div class="joysale-user-nav col-sm-6 col-md-4 col-lg-3 pull-right">
		<div id="page-content-wrapper pull-right open-overlay" class="rtl-menuicon-align">
		<a class="col-xs-1 col-sm-1 col-md-1 no-hor-padding margin-left-20" href="#menu-toggle" id="menu-toggle"><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/3-line.png'); ?>" alt="Menu"></a>
		</div>
		<ul class="navbar-nav pull-right">
		<li class="joysale-header-stuff border-radius-5"><a class="joysale-camera-icon" href="<?php echo Yii::app()->createAbsoluteUrl('item/products/create'); ?>"><?php echo Yii::t('app','Sell'); ?></a></li>
		<?php if((Yii::app()->controller->action->id != 'login') && (Yii::app()->controller->action->id != 'signup') && (Yii::app()->controller->action->id != 'forgotpassword') && (Yii::app()->controller->action->id != 'socialLogin')){  ?>
		<li class="joysale-header-login"><a href="#login-modal" data-toggle="modal" data-target="#login-modal" id="joysale-login"><?php echo Yii::t('app','Login'); ?></a></li>
		<li class="joysale-header-signup"><a href="#" data-toggle="modal" data-target="#signup-modal"><?php echo Yii::t('app','Sign up'); ?></a></li>
		<?php } ?>

		</ul>
		</div>
		<!-- /#sidebar-wrapper -->
		<!-- Mobile sidebar Content -->

		<!-- /E o Mobile sideba -->


		</div>
		</div>
		</div>
		</div>

		<?php if((Yii::app()->controller->action->id != 'login') && (Yii::app()->controller->action->id != 'signup') && (Yii::app()->controller->action->id != 'socialLogin') && (Yii::app()->controller->action->id != 'forgotpassword')){  ?>
		<!--Login modal-->

		<div class="modal fade" id="login-modal" role="dialog">
		<div class="modal-dialog modal-dialog-width">
		<div class="login-modal-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
		<div class="login-modal-header col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
		<h2 class="login-header-text"><?php echo Yii::t('app','Login to '); ?><?php echo Myclass::getSiteName(); ?></h2></h2>
		<button data-dismiss="modal" class="close login-close" type="button">×</button>
		<p class="login-sub-header-text"><?php echo Yii::t('app','Signup or login to explore the great things available near you'); ?></p>
		</div>

		<div class="login-line col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"></div>

		<div class="login-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding ">
			<div class="login-box col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
				<div class="login-text-box col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

					 <?php
				         $model=new LoginForm();
				         $socialLogin = Myclass::getsocialLoginDetails();
	                     /*$siteSettingsModel = Sitesettings::model()->findByAttributes(array('id'=>1));
	                     $socialLogin = json_decode($siteSettingsModel->socialLoginDetails, true);*/
	                   $form=$this->beginWidget('CActiveForm', array(
	                                  'id'=>'login-form',
	                                 'action'=>Yii::app()->request->baseUrl.'/login',
	                                'enableAjaxValidation'=>true,
	                               'enableClientValidation'=>true,
	                           'clientOptions'=>array(
	                           		'validateOnSubmit'=>true,
	                           		'validateOnChange'=>false,
	                           		),
	                           		'htmlOptions' => array(
	                           				'onSubmit' => 'return validsigninfrm()',
	                           		),
	                           							)); ?>


					<?php echo $form->textField($model,'username',array('class'=>'popup-input', 'placeholder'=>Yii::t('app','Enter your email address'))); ?>
					<?php echo $form->error($model,'username'); ?>
					<?php echo $form->passwordField($model,'password',array('class'=>'popup-input', 'placeholder'=>Yii::t('app','Enter your password'))); ?>
					<?php echo $form->error($model,'password'); ?>


				<?php echo CHtml::submitButton(Yii::t('app','Login'), array('class'=>'col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding login-btn')); ?>


			<div class="remember-pwd col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
				<div class="checkbox checkbox-primary remember-me-checkbox ">
					<input type="checkbox" class="remember-me-checkbox cust_checkbox" name="rememberMe" >
					<label><?php echo Yii::t('app','Remember me'); ?></label>
				</div>
				<span class="remember-div">l</span>
				<a href="#" data-toggle="modal" data-target="#forgot-password-modal" data-dismiss="modal" class="forgot-pwd"><?php echo Yii::t('app','Forgot Password ?'); ?></a>
			</div>
			</div>
			<?php $this->endWidget(); ?>
			</div>
		</div>
		<?php $lineMaring = "no-margin"; ?>
		<?php if($socialLogin['facebook']['status'] == 'enable' || $socialLogin['twitter']['status'] == 'enable'
		|| $socialLogin['google']['status'] == 'enable'){ ?>
		<div class="login-div-line col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="left-div-line"></div>
			<div class="right-div-line"></div>
			<span class="login-or"><?php echo Yii::t('app','Social Login'); ?></span>
		</div>
		<div class="social-login col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
		<div class="social-login-center">
		<?php if($socialLogin['facebook']['status'] == 'enable'){ ?>
			<div class="facebook-login">
				<a href='<?php echo Yii::app()->createAbsoluteUrl("/user/socialLogin?provider=facebook"); ?>' title='Facebook'>
					<img src="<?php echo Yii::app()->createAbsoluteUrl("/images/design/facebook.png"); ?>" alt="Facebook">
				</a>
			</div>
			<?php } ?>

			<?php if($socialLogin['google']['status'] == 'enable'){ ?>
			<div class="googleplus-login">
				<a href="<?php echo Yii::app()->createAbsoluteUrl("/user/socialLogin?provider=google"); ?>" title='Google'>
					<img src="<?php echo Yii::app()->createAbsoluteUrl("/images/design/google-plus.png"); ?>" alt="Google">
				</a>
			</div>
			<?php } ?>
		</div>
		</div>
		<?php $lineMaring = ""; ?>
		<?php } ?>
		<div class="login-line-2 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding <?php echo $lineMaring; ?>"></div>
		<div class="new-signup col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

		<span><?php echo Yii::t('app','Not a member yet ?'); ?></span><a class="signup-link txt-pink-color" data-dismiss="modal" data-toggle="modal" data-target="#signup-modal" href="#signup-modal"><?php echo Yii::t('app','click here'); ?></a>
		</div>

		</div>
		</div>
		</div>

		<!--E O Login modal-->

		<!--signup modal-->

		<div class="modal fade" id="signup-modal" role="dialog">
		<div class="modal-dialog modal-dialog-width">
		<div class="signup-modal-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
		<div class="signup-modal-header col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
		<h2 class="signup-header-text"><?php echo Yii::t('app','Signup'); ?></h2>
		<button data-dismiss="modal" class="close signup-close" type="button">×</button>

		</div>
		<div class="sigup-line col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"></div>

		<div class="signup-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding ">
		<div class="signup-box col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
		<div class="signup-text-box col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

			<?php
			$model=new Users('register');
			$form=$this->beginWidget('CActiveForm', array(
				'id'=>'users-signup-form',
				'action'=>Yii::app()->createURL('/user/signup'),
				// Please note: When you enable ajax validation, make sure the corresponding
				// controller action is handling ajax validation correctly.
				// See class documentation of CActiveForm for details on this,
				// you need to use the performAjaxValidation()-method described there.
				    'enableAjaxValidation' => true,
				    'enableClientValidation'=>true,
			     	'clientOptions'=>array(
						'validateOnSubmit'=>true,
						'validateOnChange'=>false,
				    ),
					'htmlOptions'=>array(
						'onsubmit'=> 'return signform()',
				        //'onchange' => 'return signform()',
					),
				)); ?>

				<?php echo $form->textField($model,'name',array('class'=>'popup-input', 'placeholder'=>Yii::t('app','Enter your name'), 'onkeypress' => 'return IsAlphaNumeric(event)')); ?>
				<?php echo $form->error($model,'name', array('id'=>'Users_name_em_')); ?>

				<?php echo $form->textField($model,'username',array('class'=>'popup-input', 'placeholder'=>Yii::t('app','Enter your username'), 'onkeypress' => 'return IsAlphaNumeric(event)')); ?>
				<?php echo $form->error($model,'username', array('id'=>'Users_username_em_')); ?>

				<?php echo $form->textField($model,'email',array('class'=>'popup-input', 'placeholder'=>Yii::t('app','Enter your email address'))); ?>
				<?php echo $form->error($model,'email', array('id'=>'Users_email_em_')); ?>

				<?php echo $form->passwordField($model,'password',array('class'=>'popup-input', 'placeholder'=>Yii::t('app','Enter your password'))); ?>
				<?php echo $form->error($model,'password', array('id'=>'Users_password_em_')); ?>

				<?php echo $form->passwordField($model,'confirm_password',array('class'=>'popup-input', 'placeholder'=>Yii::t('app','Confirm your password'))); ?>
				<?php echo $form->error($model,'confirm_password', array('id'=>'Users_confirm_password_em_')); ?>

		</div>
		<?php echo CHtml::submitButton(Yii::t('app','Sign Up'), array('class'=>'col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding login-btn')); ?>
		<?php $this->endWidget(); ?>

		</div>
		</div>
		<?php if($socialLogin['facebook']['status'] == 'enable' || $socialLogin['twitter']['status'] == 'enable'
		|| $socialLogin['google']['status'] == 'enable'){ ?>
		<div class="signup-div-line col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="left-div-line"></div>
		<div class="right-div-line"></div>
		<span class="signup-or"><?php echo Yii::t('app','Social signup');?></span>
		</div>

		<div class="social-login col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
		<div class="social-login-center">
		<?php if($socialLogin['facebook']['status'] == 'enable'){ ?>
			<div class="facebook-login">
				<a href='<?php echo Yii::app()->createAbsoluteUrl("/user/socialLogin?provider=facebook"); ?>' title='Facebook'>
					<img src="<?php echo Yii::app()->createAbsoluteUrl("/images/design/facebook.png"); ?>" alt="Facebook">
				</a>
			</div>
			<?php } ?>
			<?php if($socialLogin['google']['status'] == 'enable'){ ?>
			<div class="googleplus-login">
				<a href="<?php echo Yii::app()->createAbsoluteUrl("/user/socialLogin?provider=google"); ?>" title='Google'>
					<img src="<?php echo Yii::app()->createAbsoluteUrl("/images/design/google-plus.png"); ?>" alt="Google">
				</a>
			</div>
			<?php } ?>
		</div>
		</div>
		<?php } ?>

		<div class="login-line-2 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding <?php echo $lineMaring; ?>"></div>
		<div class="user-login col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
		<span><?php echo Yii::t('app','Already a member?'); ?></span><a class="login-link txt-pink-color" href="#login-modal" data-dismiss="modal" data-toggle="modal" data-target="#login-modal"><?php echo Yii::t('app','login'); ?></a>
		</div>

		</div>
		</div>
		</div>

		<!--E O signup modal-->
		<?php } ?>
		<?php if((Yii::app()->controller->action->id != 'signup') && (Yii::app()->controller->action->id != 'socialLogin') && (Yii::app()->controller->action->id != 'forgotpassword')){  ?>
		<!--Forgot password-->

		<div class="modal fade" id="forgot-password-modal" role="dialog">
		<div class="modal-dialog modal-dialog-width">
		<div class="login-modal-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
		<div class="login-modal-header col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
		<h2 class="forgot-header-text"><?php echo Yii::t('app','Forgot Password'); ?></h2>
		<button data-dismiss="modal" class="close login-close" type="button">×</button>
		<p class="forgot-sub-header-text"><?php echo Yii::t('app',"Enter your email address and we'll send you a link to reset your password."); ?></p>
					</div>

						<div class="forgot-line col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"></div>

							<div class="forgot-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding ">
								<div class="forgot-box col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<div class="forgot-text-box col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<?php
									$models = new Users('forgetpassword');
									$form = $this->beginWidget('CActiveForm', array(
										'id'=>'forgetpassword-form',
										'action'=>Yii::app()->createURL('/forgotpassword'),
										'enableAjaxValidation'=>true,
										'htmlOptions'=>array(
											'onsubmit'=>'return validforgot()',
									),
									)); ?>
									<?php echo $form->textField($models,'email',array('class' => 'forgetpasswords popup-input forget-input',
											'placeholder'=>Yii::t('app',"Enter your email address"))); ?>
									<?php echo $form->error($models,'emails'); ?>
									<?php echo CHtml::submitButton(Yii::t('app','Reset Password'),
											array('class'=>'col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding forgot-btn','style'=>'margin-top:10px;')); ?>
									<?php $this->endWidget(); ?>
									</div>
								</div>

							</div>



			</div>
		</div>
	</div>

<!--E O Forgot password--->
	<?php } ?>

	<!--Mobile search bar code-->
	<div class="joysale-search-bar-bg">
		<div class="container-fluid">
			<div class="app-responsive-adjust"></div>


			<div class="joysale-search-bar-mobile col-xs-12 col-sm-12 col-md-12 no-hor-padding form-group search-input-container">

				<form role="form" class="navbar-form- navbar-left- search-form" style="padding-left: 0;" action="<?php echo Yii::app()->createAbsoluteUrl('/');?>"
						method="get">
						<a href="" class="joysale-icon-mobile input-search" data-toggle="modal" data-target="#search-mobile-cal">Search products</a>
					<!--<input type="text" maxlength="30" placeholder="<?php echo Yii::t('app','Search products'); ?>" class="joysale-search-icon-mobile  form-control input-search" <?php echo !empty(Yii::app()->user->id) ? "" : "sign" ?>" name="search">-->
				</form>
			</div>

			<!--<div class="joysale-search-bar-mobile col-xs-12 col-sm-12 col-md-12 no-hor-padding form-group search-input-container">
			<div class="mobile-search">
				<form role="form" class="navbar-form- navbar-left- search-form"
					style="padding-left: 0;"
					action="<?php echo Yii::app()->createAbsoluteUrl('/'); ?>"
					method="get">
				<input type="text" maxlength="30" placeholder="<?php echo Yii::t('app','Search products'); ?>" class="joysale-search-icon-mobile  form-control input-search" <?php echo !empty(Yii::app()->user->id) ? "" : "sign" ?>" name="search">
				</form>
			</div>

			</div>-->

		</div>
	</div>


	<!--Mobile search bar code-->

	<div class="joysale-menu" >
		<div class="container-fluid">
			<div class="row" style="height: 64px;"></div>
			<div class="row">
				<nav class="navbar col-xs-12 col-sm-12 col-md-12">
					<ul class="nav navbar-nav">
					<!-- Hot fixes -->
					<li class="dropdown">
					<a class="dropdown-toggle bold joysale-for-sale disabled" data-toggle="dropdown" href="<?php echo Yii::app()->createAbsoluteUrl('/category/allcategories'); ?>" style="background:url(<?php echo Yii::app()->createAbsoluteUrl('admin/categories/resized/70/allcategories.png'); ?>) no-repeat scroll left center / 32px auto; " ><?php echo Yii::t('app','All Categories');?></a>
										  	</li>
					<!-- Hot fixes -->
					<?php  $categorypriority = Myclass::getCategoryPriority();?>

					<?php foreach($categorypriority as $key => $category):
						if($category != "empty"){
							//$getcaname =  Myclass::getCatName($category);
							$getcatdet = Myclass::getCatDetails($category);
							$getcatimage = Myclass::getCatImage($category);
							$subCategory = Myclass::getSubCategory($category);

					?>
					<!-- desktop category view -->
					<li class="dropdown">

					<a class="dropdown-toggle bold joysale-for-sale disabled" data-toggle="dropdown" href="<?php echo Yii::app()->createAbsoluteUrl('/category/'.$getcatdet->slug); ?>" style="background:url(<?php echo Yii::app()->createAbsoluteUrl('admin/categories/resized/70/'.$getcatimage); ?>) no-repeat scroll left center / 32px auto; " ><?php echo Yii::t('app',$getcatdet->name); ?></a>
						<?php if(!empty($subCategory)) {?>
						<ul id="dropdown-block" class="dropdown-menu joysale-dropdown-submenu">
							<?php foreach($subCategory as $key => $subCategory):
							//echo $key;
									$subCatdet = Myclass::getCatDetails($key);
							?>
							<li><a class="bold" href="<?php echo Yii::app()->createAbsoluteUrl('/category/'.$getcatdet->slug.'/'.$subCatdet->slug); ?>"><?php echo Yii::t('app',$subCategory); ?></a></li>
							<?php endforeach;?>
				  		</ul>
				  		<?php }?>
				  	</li>
				  	<!-- desktop view end-->
					<?php } endforeach;?>

					</ul>
				</nav>
			</div>
		</div>
	</div>

<!---- Slider ------>
	<!--Slider code-->

	<?php }?>
	<?php /*?>  <div class="container-fluid">
		<header class="navbar navbar-fixed-top bg-white">
			<div class="col-md-2">
			<?php $logo = Myclass::getLogo();
			echo CHtml::link(CHtml::image(Yii::app()->createAbsoluteUrl('media/logo/'.$logo),"Logo",
					array('style'=>'height: 36px; margin: 3px 0px;float:left;')),Yii::app()->createAbsoluteUrl('/')); ?>
			</div>
			<?php if(!empty(Yii::app()->user->id)) { ?>
			<div class="pull-right after-login col-md-6" style="text-align: center">
			<?php }else{ ?>
			<div class="pull-right before-login col-md-6" style="text-align: center">
			<?php } ?>
				<div class="lang-menu-front  navbar-form navbar-left">
				<?php $this->widget('Language'); ?>
				</div>
				<div class="btn-group navbar-form navbar-left"
					style="padding-left: 0;">
					<a class="btn btn-primary sell-button"
						href="<?php echo Yii::app()->createAbsoluteUrl('item/products/create'); ?>">
						<i class="fa fa-plus"> </i> <?php echo Yii::t('app','SELL'); ?>
					</a>
				</div>

				<?php $category = Myclass::getCategory(); ?>
				<div class="btn-group navbar-form navbar-left shop-menu"
					style="paddidng-left: 0;">
					<a class="btn btn-primary dropdown-toggle shop-button"
						data-toggle="dropdown" href="#"><?php echo Yii::t('app','SHOP'); ?>
						<!-- <span class="caret"></span> -- > </a>
					<ul
						class="dropdown-menu <?php echo count($category) > 10 ? 'more-menu' : ""; ?>">
						<?php foreach($category as $cat): ?>
						<li><a
							href="<?php echo Yii::app()->createAbsoluteUrl('/category/'.$cat->slug); ?>">
							<?php echo CHtml::image(Yii::app()->createAbsoluteUrl('admin/categories/resized/70/'.$cat->image),$cat->name);
							/* $count = strlen($cat->name);
							if($count > 10){
								$substring = substr($cat->name,0,6).'...';
							} else {
								$substring = $cat->name;
							} * /
							echo "<p>".$cat->name."</p>"; ?>
						</a>
						</li>
						<?php endforeach;?>
					</ul>
				</div>

				<form role="form" class="navbar-form navbar-left search-form"
					style="padding-left: 0;"
					action="<?php echo Yii::app()->createAbsoluteUrl('/'); ?>"
					method="get">
					<div class="form-group search-input-container">
						<input type="text" name="search"
							class="form-control input-search <?php echo !empty(Yii::app()->user->id) ? "" : "sign" ?>"
							placeholder="<?php echo Yii::t('app','Search'); ?>"> <i
							class="fa fa-search input-search-icon"></i>
					</div>
					<!-- <button class="btn btn-success" type="submit">Search</button> -- >
				</form>

				<?php if(!empty(Yii::app()->user->id)) {
					$messageCount = Myclass::getMessageCount(Yii::app()->user->id); ?>
				<script>
					var liveCount = <?php echo $messageCount; ?>;
				</script>
				<div class="btn-group navbar-form navbar-left" style="padding-left: 0;">
					<a class="message-linkk"
						href="<?php echo Yii::app()->createAbsoluteUrl('message'); ?>"> <i
						class="fa fa-envelope-o fa-2x"></i>
						<?php
						$messageStatus = "";
						if($messageCount == 0){
							$messageStatus = "message-hide";
						} ?>
						<div class="message-count <?php echo $messageStatus; ?>">
							<?php echo $messageCount; ?>
						</div>
					</a>
				</div>

				<div class="btn-group navbar-form navbar-right profile-left" style="padding-left: 0;">

					<a class="dropdown-toggle" data-toggle="dropdown" href="#"> <?php $userImage = Myclass::getUserDetails(Yii::app()->user->id);
					if(!empty($userImage->userImage)) {
						echo CHtml::image(Yii::app()->createAbsoluteUrl('user/resized/35/'.$userImage->userImage),$userImage->username);
					} else {
						echo CHtml::image(Yii::app()->createAbsoluteUrl('user/resized/35/default/'.Myclass::getDefaultUser()),$userImage->username);
					}
					?>
					</a>
					<ul class="dropdown-menu">
						<li><a
							href="<?php echo Yii::app()->createAbsoluteUrl('user/profiles'); ?>"><?php echo Yii::t('app','Profile'); ?>
						</a>
						</li>
						<li><a
							href="<?php echo Yii::app()->createAbsoluteUrl('shippingaddress'); ?>"><?php echo Yii::t('app','Shipping Addresses'); ?>
						</a>
						</li>
						<li role="presentation" class="divider"></li>
						<li><a
							href="<?php echo Yii::app()->createAbsoluteUrl('item/exchanges',array('type' => 'incoming')); ?>">
							<?php echo Yii::t('app','My Exchanges'); ?>
						</a>
						</li>
						<li><a
							href="<?php echo Yii::app()->createAbsoluteUrl('orders'); ?>"><?php echo Yii::t('app','My Orders'); ?>
						</a>
						</li>
						<li><a
							href="<?php echo Yii::app()->createAbsoluteUrl('sales'); ?>"><?php echo Yii::t('app','My Sales'); ?>
						</a>
						</li>
						<li><a
							href="<?php echo Yii::app()->createAbsoluteUrl('coupons',array('type' => 'item')); ?>"><?php echo Yii::t('app','Coupons'); ?>
						</a>
						</li>
						<li role="presentation" class="divider"></li>
						<li><a
							href="<?php echo Yii::app()->createAbsoluteUrl('user/logout'); ?>"><?php echo Yii::t('admin','Logout'); ?>
						</a>
						</li>
					</ul>

				</div>
				<?php } else { ?>
				<div class="btn-group navbar-form navbar-left" style="padding-left: 0px;padding-top: 2px;">
					<ul class="login-menu" style="float: right; display: inline;">
						<li><a href="<?php echo Yii::app()->createAbsoluteUrl('user/login'); ?>"><i
								class="fa fa-sign-in" style="font-size: 2em;"></i> </a>
						</li>
						<!-- <li><a style="padding: 0; height: 100%"
							href="<?php echo Yii::app()->createAbsoluteUrl('user/signup'); ?>"><i
								class="fa fa-user" style="font-size: 2em;"></i> </a>
						</li> -- >

					</ul>

				</div>


				<?php } ?>
			</div>

		</header>
		<!-- header -- >

		<div class="row">
			<div class="col-lg-12 userinfo">
			<?php
			$flashMessages = Yii::app()->user->getFlashes();
			if ($flashMessages) {
				echo '<ul class="flashes">';
				foreach($flashMessages as $key => $message) {
					echo '<li><div class="flash-' . $key . '">' . $message . "</div></li>\n";
				}
				echo '</ul>';
			}
			?>
			</div>
		</div>
		<?php */ ?>

		<!--Confirmation popup-->
		<div class="modal fade" id="confirm_popup_container" role="dialog" aria-hidden="true">
			<div id="confirm-popup" class="modal-dialog modal-dialog-width confirm-popup">
				<div class="login-modal-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
					<div class="login-modal-header col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
							<h2 class="login-header-text"><?php echo Yii::t('app','Confirm'); ?></h2>

					</div>

					<div class="login-line col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"></div>

					<div class="login-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding ">
						<span class="delete-sub-text col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
							<?php echo Yii::t('app','Are you sure you want to proceed ?'); ?>
						</span>
						<span class="confirm-btn">
							<a class="margin-bottom-0 post-btn" href="#" onclick="closeConfirm()">
								<?php echo Yii::t('app','ok'); ?>
							</a>
						</span>
						<a class="margin-bottom-0 delete-btn margin-10" href="#" onclick="closeConfirm()">
							<?php echo Yii::t('app','cancel'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>

		<!--E O Confirmation popup
		<div id="confirm_popup_container">
			<div id="confirm-popup" style="display: none;" class="popup ly-title update confirm-popup">
				<p class="ltit">
				<?php echo Yii::t('app','Confirm'); ?>
				</p>
				<div class="confirm-popup-content">
					<div class="confirm-message">
						<?php echo Yii::t('app','Are you sure you want to proceed ?'); ?>
					</div>
					<div class="btn-area">
						<button type="button" class="btn-confirm-cancel btn-done" id="btn-doneid"
							onclick="closeConfirm()">
							<?php echo Yii::t('app','cancel'); ?>
						</button>
						<div class="confirm-btn">
							<button type="button" class="btn-confirm-cancel btn-done" id="btn-doneid"
								onclick="closeConfirm()">
								<?php echo Yii::t('app','ok'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div> -->


		<div class="container-fluid no-hor-padding">
			<?php $flashMessages = Yii::app()->user->getFlashes();
			if ($flashMessages) {
				foreach($flashMessages as $key => $message) { ?>
			<div class=" flashes message-floating-div-cnt col-xs-12 col-sm-4 col-md-3 col-lg-3 no-hor-padding">
				<div class="flash-<?php echo $key; ?> floating-div no-hor-padding pull-right" style="width:auto;">
					<div class="message-user-info-cnt no-hor-padding" style="width:auto;">
						<div class="message-user-info"><?php echo $message; ?></div>
					</div>
				</div>
			</div>
			<?php } } ?>
		</div>



		<?php echo $content; ?>

		<?php 
$siteSettings = Sitesettings::model()->find();
if($siteSettings->google_ads_footer == 1) 
{?>
<div style="background-color:#f4f4f4;" id="topbanner-ads-footer" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">	
<div style="display:none;" class="adscontents">		
<script type="text/javascript" src="//pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<script type="text/javascript">
google_ad_client = "<?php echo $siteSettings->google_ad_client_footer; ?>";
google_ad_slot = "<?php echo $siteSettings->google_ad_slot_footer; ?>";
google_ad_width = 728;
google_ad_height = 90;
</script>
</div>
<div class="adscontents-footer">		
<script type="text/javascript">
var width = window.innerWidth || document.documentElement.clientWidth;
google_ad_client = "<?php echo $siteSettings->google_ad_client_footer; ?>";
if (width > 1200) {
google_ad_slot = "<?php echo $siteSettings->google_ad_slot_footer; ?>";
google_ad_width = 1130;
google_ad_height = 100;
}
else if ((width <= 1200) && (width > 760)) { 
google_ad_slot = "<?php echo $siteSettings->google_ad_slot_footer; ?>";
google_ad_width = 710; 
google_ad_height = 95;
}
else if ((width <= 760) && (width > 450)) { 
google_ad_slot = "<?php echo $siteSettings->google_ad_slot_footer; ?>";
google_ad_width = 400; 
google_ad_height = 95;
}
else
{
google_ad_slot = "<?php echo $siteSettings->google_ad_slot_footer; ?>";
 google_ad_width = 250; 
 google_ad_height = 95;
}
</script>
<script type="text/javascript" src="//pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
</div>
</div>
<?php } ?>

	<?php if(!empty($footerSettings['appLinks']) && count($footerSettings['appLinks']) > 0){ ?>
	<!--div class="joysale-app-download">
		<div class="container-fluid">
			<div class="row">
				<div class="joysale-app-section col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
					<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 no-hor-padding">
						<h1 class="joysale-app-heading "><?php echo Yii::t('app','Get your online classifieds apps now'); ?></h1>
						<p class="joysale-app-text"><?php echo Yii::t('app','Start earning by selling or buying stuffs nearer to your locality by using this great online classifieds.'); ?></p>
						</div>
							<div class="joysale-app-logo col-xs-12 col-sm-6 col-md-4 col-lg-4 no-hor-padding">
								<?php if(isset($footerSettings['appLinks']['ios'])){ ?>
								<div class="joysale-app-store">
									<a href="<?php echo $footerSettings['appLinks']['ios']; ?>" target="_blank"><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/design/app-store.png'); ?>" alt="IOS"></a>
								</div>
								<?php } if(isset($footerSettings['appLinks']['android'])){ ?>
									<div class="joysale-play-store">
										<a href="<?php echo $footerSettings['appLinks']['android']; ?>" target="_blank"><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/design/play-store.png'); ?>" alt="ANDRIOD"></a>
									</div>
								<?php } ?>
							</div>
				</div>
			</div>
		</div>
	</div-->





	<?php }?>

		<!-- back to top button -->
			<div class="backtop">
				<a id="back2Top" title="Back to top" href="#"><span>Back to top</span></a>
			</div>

		<!-- End back to top -->

		<div class="footer">
			<div class="container">

				<div class="row">
					<div class="joysale-footer-head col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="joysale-social-connect col-xs-12 col-sm-6 col-md-3 col-lg-3 no-hor-padding">
							<span class="joysale-social-head">
								<?php echo $footerSettings['socialloginheading']; ?></span>
							<?php if(!empty($footerSettings['socialLinks']) && count($footerSettings['socialLinks']) > 0){ ?>
								<div class="joysale-social-icon">
								  <?php if(isset($footerSettings['socialLinks']['facebook'])){ ?>
									<a href="<?php echo $footerSettings['socialLinks']['facebook']; ?>" target="_blank"><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/design/facebook.png'); ?>" alt="facebook"></a>
								  <?php }if(isset($footerSettings['socialLinks']['twitter'])){ ?>
									<a href="<?php echo $footerSettings['socialLinks']['twitter']; ?>" target="_blank"><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/design/twitter.png'); ?>" alt="twitter"></a>
								  <?php }if(isset($footerSettings['socialLinks']['google'])){ ?>
									<a href="<?php echo $footerSettings['socialLinks']['google']; ?>" target="_blank"><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/design/google-plus.png'); ?>" alt="google plus"></a>
								  <?php }?>
									<!-- <a href="#"><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/design/instagram.png'); ?>" alt="instagram"></a>-->
								</div>
							<?php }else{ ?>
								<div class="joysale-nosocial-icon"><?php echo Yii::t('app','Yet no sociallinks are not updated.'); ?></div>
							<?php } ?>
						</div>

						<div class="joysale-app-links col-xs-12 col-sm-6 col-md-2 col-lg-2 no-hor-padding">
							<span class="joysale-app-head"><?php echo $footerSettings['applinkheading']; ?> </span>
							<?php if(!empty($footerSettings['appLinks']) && count($footerSettings['appLinks']) > 0){ ?>
							<div class="joysale-app-icon">
							  <?php if(isset($footerSettings['appLinks']['ios'])){ ?>
								<a class="joysale-ios-app" href="<?php echo $footerSettings['appLinks']['ios']; ?>" target="_blank"><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/design/ios.png'); ?>" alt="ios app" data-toggle="tooltip" title="" data-original-title="iOS app"></a>
							  <?php } if(isset($footerSettings['appLinks']['ios']) && isset($footerSettings['appLinks']['android']) ){?>
								<span class="joysale-footer-vertical-line"></span>
							  <?php } if(isset($footerSettings['appLinks']['android'])){ ?>
								<a href="<?php echo $footerSettings['appLinks']['android']; ?>" target="_blank" class="joysale-android-app"><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/design/android.png'); ?>" alt="android app"data-toggle="tooltip" title="" data-original-title="Android app"></a>
							  <?php } ?>
							</div>
							<?php }else{ ?>
							<div class="joysale-noapp-icon"><?php echo Yii::t('app','Yet no applinks are not updated.'); ?></div>
							<?php }?>
						</div>
						<?php if(empty(Yii::app()->user->id)) {?>
							<div class="joysale-new-account col-xs-12 col-sm-12 col-md-7 col-lg-7 no-hor-padding">
								<p class="joysale-new-account-info col-xs-12 col-sm-9 col-md-9 col-lg-9 no-hor-padding">
									<?php echo $footerSettings['generaltextguest']; ?>
								</p>

								<a href="<?php echo Yii::app()->createAbsoluteUrl('user/signup'); ?>"
									class="joysale-create-btn border-radius-5 primary-bg-color col-xs-12 col-sm-3 col-md-3 col-lg-3 no-hor-padding">
									<?php echo Yii::t('app','Create an account'); ?>
								</a>

							</div>
						<?php }else{ ?>
							<div class="joysale-new-account col-xs-12 col-sm-12 col-md-7 col-lg-7 no-hor-padding">
								<p class="joysale-new-account-info col-xs-12 col-sm-9 col-md-9 col-lg-9 no-hor-padding">
								<?php echo $footerSettings['generaltextuser']; ?>
								</p>
							<?php
							if(isset($siteSettings->promotionStatus) && $siteSettings->promotionStatus == "1")
							{
							?>
								<a href="<?php echo Yii::app()->createAbsoluteUrl(
										'user/profiles',array('id'=>Myclass::safe_b64encode(Yii::app()->user->id.'-'.rand(0,999)))); ?>"
									class="joysale-create-btn border-radius-5 primary-bg-color col-xs-12 col-sm-3 col-md-3 col-lg-3 no-hor-padding">
									<?php echo Yii::t('app','Promote your list'); ?>
								</a>
							<?php
							}
							?>

							</div>
						<?php }?>
					</div>
				</div>

				<div class="row copyright-foter">
					<div class="joysale-footer-horizontal-line col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"></div>
					<div class="joysale-footer-bottom col-xs-12 col-sm-8 col-md-8 col-lg-10 no-hor-padding">
						<div class="joysale-footer-menu-links col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
							<ul>
							<?php $footerLinks = Myclass::getFooterLinks();
								if (!empty($footerLinks)){
							?>
							<li>
							<?php
								foreach ($footerLinks as $footerKey => $footerLink){
								$pageLink = Yii::app()->createAbsoluteUrl('help/'.$footerLink->slug);
							?>
							<a class="primary-txt-color" target="_blank" href="<?php echo $pageLink; ?>"><?php echo $footerLink->page; ?></a>
							</li>
							<?php if(count($footerLinks) > ($footerKey + 1)){ ?>
							<li class="joysale-footer-dev"><?php echo Yii::t('app','l'); ?></li>
							<?php
									}
								}
							?>

							<?php }?>

							<!--<li><a href="#">Contact</a></li>
							<li class="joysale-footer-dev">l</li>
							<li><a href="#">Terms of sales</a></li>
							<li class="joysale-footer-dev">l</li>
							<li><a href="#">Terms of Services</a></li>
							<li class="joysale-footer-dev">l</li>
							<li><a href="#">Privacy policy </a></li>
							<li class="joysale-footer-dev">l</li>
							<li><a href="#">Terms and conditions</a></li>-->
							</ul>
						</div>


						<div class="joysale-footer-Copyright col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
							<?php if(!empty($footerSettings['footerCopyRightsDetails'])){
								echo $footerSettings['footerCopyRightsDetails'];
							}else{ ?>
							<span><?php echo Yii::t('app','© Copyright 2016 Hitasoft.com Limited. All rights reserved.'); ?> </span>
							<?php } //echo Yii::app()->controller->id.'/'.Yii::app()->controller->action->id; ?>
						</div>

					</div>

					<?php if(Yii::app()->language=='ar'){?>
						<div class="language col-xs-12 col-sm-4 col-md-4 col-lg-2 no-hor-padding">
					<?php } else { ?>
						<div class="language col-xs-12 col-sm-4 col-md-4 col-lg-2 no-hor-padding">
					<?php } ?>
						<?php $this->widget('Language'); ?>
					</div>



				</div>

			</div>
			<div class="analytics-codes">
				<?php if(!empty($footerSettings['analytics'])){
					echo $footerSettings['analytics'];
				} ?>
			</div>
		</div>
<!-- Location Floting icon -->
<?php if(Yii::app()->controller->id=='site' && Yii::app()->controller->action->id=='index') {?>
	<div class="floting-location">
		<div class="mobile-location">
					<div class="loader-front">
					<a href="javascript:void(0);" data-toggle="tooltip" title="" onclick="getLatLong();">
					<div class="mobile-location-icon primary-bg-color"><img src="<?php echo Yii::app()->createAbsoluteUrl('images/location-w.png'); ?>"></div></a>
					</div>
					<div class="loader-back-old" style="display:none;">
					<div class="location-loader"></div>
					</div>	
				</div>
				<div class="mobile-location-info txt-white-color border-radius-5 primary-bg-color">
					<div class="location-info-txt">Current Location</div>
				</div>
			<div class="circle"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div>
	</div>

<?php } ?>


<div class="modal fade in" id="search-mobile-cal" role="dialog">
	<div class="searcls-mobile modal-dialog modal-dialog-width">
		<div class="login-modal-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
			<div class="login-modal-header col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
				<h2 class="login-header-text">Search</h2>
				<button data-dismiss="modal" class="close login-close" type="button">×</button>
			</div>

			<div class="login-line col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"></div>

			<div class="login-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding ">
				<div class="login-box">
					<div class="login-text-box">

					 	<form role="form" class="searchform navbar-form- navbar-left- search-form" style="padding-left: 0;"
						action="<?php echo Yii::app()->createAbsoluteUrl('/'); ?>" method="get">
					 		<div class="joysale-search-bar col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
						 		<div class="ui-widget topsearch-locatn">
									<input type="text" id="searchvalmobile" onkeyup="ajaxSearch(this,event);" maxlength="30" placeholder="<?php echo Yii::t('app','Search products'); ?>" class="tags joysale-search-icon form-control input-search <?php echo !empty(Yii::app()->user->id) ? "" : "sign" ?>" name="search" value="<?php echo $search;?>">
								</div>
							</div>
						</form>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
								<div class="hme-top-location map-input-section">
									<div class="map-input-box">
										<input id="pac-input2" placeholder="World Wide" class="controls" autocomplete="off" value="<?php echo Yii::app()->session['place1'];?>" type="text">
									</div>
									<!--<div class="map-select-box">
										<select id="select-mapdistance" class="select-box-arrow">
											<option value="1" selected="">1 km</option>
											<option value="5">5 km</option>
											<option value="10">10 km</option>
											<option value="20">20 km</option>
											<option value="50">50 km</option>
											<option value="700">700 km</option>
										</select>
									</div>-->
								</div>
							</div>

							<input class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding login-btn" name="Search" value="Search" type="submit" onclick="return gotogetLocationDatamobile();">

					</div>
				</div>
			</div>

		</div>
	</div>
</div>








<!-- E O Location Floting icon -->
    <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
        $("#wrapper.toggled").css("display", "block");
        $("body").toggleClass("scroll-hidden");
        //$("#wrapper.toggled").parent("body").css('overflow':'hidden');
    });
    </script>
	<style>
	.scroll-hidden{
	overflow:hidden;
	}
	</style>

<!-- Sticky menu -->
<script>
$(document).ready(function(){
	$(window).scroll(function() {
	    var scroll = $(window).scrollTop();
	    var headerHeightTrack = ($('.joysale-menu').height() - 64);

	    if (scroll >= headerHeightTrack) {
	        $(".joysale-header").addClass("affix");
	    } else {
	        $(".joysale-header").removeClass("affix");
	    }
	});
});
</script>



<!-- E O sticky menu -->

<!-- Tooltip menu -->
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

<script type="text/javascript">

	/*Scroll to top when arrow up clicked BEGIN*/
$(window).scroll(function() {
    var height = $(window).scrollTop();
    if (height > 500) {
        $('#back2Top').fadeIn();
    } else {
        $('#back2Top').fadeOut();
    }
});
$(document).ready(function() {
    $("#back2Top").click(function(event) {
        event.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, "slow");
        return false;
    });

});
 /*Scroll to top when arrow up clicked END*/

</script>
<!-- E o tooltip menu -->

		<?php /*?>

		<div class="clear"></div>
		<footer class="container-fluid no-hor-padding hidden">
			<div class="container footer-links no-hor-padding">
				<ul>
				<?php $footerLinks = Myclass::getFooterLinks();
					if (!empty($footerLinks)){
						echo '<li>';
						foreach ($footerLinks as $footerKey => $footerLink){
							$pageLink = Yii::app()->createAbsoluteUrl('help/'.$footerLink->slug);
				?>
					<a href="<?php echo $pageLink; ?>"><?php echo $footerLink->page; ?></a>
					<?php if(count($footerLinks) > ($footerKey + 1)){ ?>
						<span class="menu-divider">|</span>
					<?php } ?>
				<?php }	echo '</li>'; } ?>
				</ul>
			</div>
			<div class="container-fluid download-follow no-hor-padding hidden">
				<div class="col-md-12 no-hor-padding">
					<?php $footerSettings = Myclass::getFooterSettings();?>
					<?php if(!empty($footerSettings['appLinks']) && count($footerSettings['appLinks']) > 0){ ?>
					<div class="col-md-6 download-app" style="margin-left: 5px;">
						<span class="download-app-text" style="margin-top: 6px;"><?php echo Yii::t('app','Download apps'); ?> :</span>
						<?php if(isset($footerSettings['appLinks']['ios'])){ ?>
							<a href="<?php echo $footerSettings['appLinks']['ios']; ?>"
								target="_blank">
								<div class="app-store-button"></div>
							</a>
						<?php }
							if(isset($footerSettings['appLinks']['android'])){ ?> &nbsp;
							<a href="<?php echo $footerSettings['appLinks']['android']; ?>"
								target="_blank">
								<div class="play-store-button"></div>
							</a>
						<?php } ?>
					</div>
					<?php } ?>
					<?php if(!empty($footerSettings['socialLinks']) && count($footerSettings['socialLinks']) > 0){ ?>
					<div class="col-md-3 follow-us pull-right" style="margin-right: 5px;">
						<?php
						if(isset($footerSettings['socialLinks']['google'])){ ?>
						<a href="<?php echo $footerSettings['socialLinks']['google']; ?>"
							target="_blank" style="text-decoration: none">
							<div class="google-icon"></div>
						</a>
						<?php }
						if(isset($footerSettings['socialLinks']['twitter'])){ ?>
						<a href="<?php echo $footerSettings['socialLinks']['twitter']; ?>"
							target="_blank" style="text-decoration: none">
							<div class="twitter-icon"> </div>
						</a>
						<?php } ?>
						<?php if(isset($footerSettings['socialLinks']['facebook'])){ ?>
						<a href="<?php echo $footerSettings['socialLinks']['facebook']; ?>"
							target="_blank" style="text-decoration: none">
							<div class="fb-icon"> </div>
						</a>
						<?php } ?>
						<span class="download-app-text" style="margin-top: 6px;float: right;"><?php echo Yii::t('app','Follow Us'); ?> :</span>
					</div>
					<?php } ?>
				</div>
			</div>
		</footer>
		<?php /* ?><div class="footer">
			<div class="col-md-12">
				<?php $footerLinks = Myclass::getFooterLinks();
					if (!empty($footerLinks)){
						echo '<div class="col-md-12 link-to-support">';
						foreach ($footerLinks as $footerLink){
							$pageLink = Yii::app()->createAbsoluteUrl('help/'.$footerLink->slug);
				?>
					<a href="<?php echo $pageLink; ?>"><?php echo $footerLink->page; ?></a>
				<?php }	echo '</div>'; } ?>
				<?php $footerSettings = Myclass::getFooterSettings();?>
				<?php if(!empty($footerSettings['socialLinks']) && count($footerSettings['socialLinks']) > 0){ ?>
				<div class="col-md-12">
					<div class="col-md-6" style="float: right; padding-top: 1px">
						<p class="pull-right follow-us" style="color: #2FDAB8">
						<?php echo Yii::t('app','Follow Us'); ?>
							: &nbsp;
							<?php if(isset($footerSettings['socialLinks']['facebook'])){ ?>
							<a href="<?php echo $footerSettings['socialLinks']['facebook']; ?>"
								target="_blank" style="text-decoration: none">
								<i class="fa fa-facebook-square follow-fb fa-2x" style="color: #888"></i>
							</a>
							<?php }
							if(isset($footerSettings['socialLinks']['twitter'])){ ?>
							<a href="<?php echo $footerSettings['socialLinks']['twitter']; ?>"
								target="_blank" style="text-decoration: none">
								<i class="fa fa-twitter-square fa-2x follow-tweet" style="color: #888"></i>
							</a>
							<?php }
							if(isset($footerSettings['socialLinks']['google'])){ ?>
							<a href="<?php echo $footerSettings['socialLinks']['google']; ?>"
								target="_blank" style="text-decoration: none">
								<i class="fa fa-google-plus-square fa-2x follow-google"
									style="color: #888"></i>
							</a>
							<?php } ?>
						</p>
					</div>
					<?php } ?>
					<?php if(!empty($footerSettings['appLinks']) && count($footerSettings['appLinks']) > 0){ ?>
					<div class="col-md-6">
						<p class="pull-left follow-us" style="color: #2FDAB8">
						<?php echo Yii::t('app','Download apps'); ?>: &nbsp;
						<?php if(isset($footerSettings['appLinks']['ios'])){ ?>
							<a href="<?php echo $footerSettings['appLinks']['ios']; ?>"
								class="fa fa-2x fa-apple" target="_blank"></a>
						<?php }
							if(isset($footerSettings['appLinks']['android'])){ ?> &nbsp;
							<a href="<?php echo $footerSettings['appLinks']['android']; ?>"
								class="fa fa-2x fa-android" target="_blank"></a>
						<?php } ?>
						</p>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php * / ?>
		<!--<div class="clear"></div>
	</div> <?php */ ?>
	<!-- page -->
	<style type="text/css">
		.flashes{
			 -webkit-transition: all 3s ease-out;
		    -moz-transition: all 3s ease-out;
		    -ms-transition: all 3s ease-out;
		    -o-transition: all 3s ease-out;
		    transition: all 3s ease-out;

		}
		.move{

			 position: absolute;
		    -webkit-transition: all 3s ease-out;
		    -moz-transition: all 3s ease-out;
		    -ms-transition: all 3s ease-out;
		    -o-transition: all 3s ease-out;
		    transition: all 3s ease-out;

		}
	</style>
	<script>

	$(document).keyup(function(e) {
		if (e.keyCode === 27){

	   		if ($('#login-modal').css('display') == 'block'){
		       $('#login-modal').modal('hide');
			}

	   		if ($('#signup-modal').css('display') == 'block'){
 			   $('#signup-modal').modal('hide');
			}

	   		if ($('#forgot-password-modal').css('display') == 'block'){
		       $('#forgot-password-modal').modal('hide');
			}

			if ($('#nearmemodals').css('display') == 'block'){
		       $('#nearmemodals').modal('hide');
			}

			if ($('#post-your-list').css('display') == 'block'){
		       $('#post-your-list').modal('hide');
			}

			if ($('#mobile-otp').css('display') == 'block'){
		       $('#mobile-otp').modal('hide');
			}

			if ($('#chat-with-seller-success-modal').css('display') == 'block'){
		       $('.modal').modal('hide');
		       $('#chat-with-seller-success-modal').css('display','none');
			}

			if ($('#offer-success-modal').css('display') == 'block'){
		       $('.modal').modal('hide');
		       $('#offer-success-modal').css('display','none');
			}
		}
	});

	var loginSession = readCookie('PHPSESSID');
	setTimeout(function() {
			//$(".flashes").slideRight();
			 //$('.flashes').toggle( "slide" );
			//$(".flashes").addClass('move');
			$('.flashes').fadeOut('fast');
		}, 3000);
	function readCookie(name) {
	    var nameEQ = escape(name) + "=";
	    var ca = document.cookie.split(';');//console.log(document.cookie);
	    for (var i = 0; i < ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
	        if (c.indexOf(nameEQ) === 0) return unescape(c.substring(nameEQ.length, c.length));
	    }
	    return null;
	}
	if (typeof timerId != 'undefined'){
		clearInterval(timerId);
	}
	var timerId = setInterval(function() {
		var currentSession = readCookie('PHPSESSID');
	    if(loginSession != currentSession) {
		    //console.log('in reload '+loginSession+" "+currentSession);
		    window.location = '<?php echo Yii::app()->createAbsoluteUrl('/'); ?>';
		    clearInterval(timerId);
	        //Or whatever else you want!
	    }

	},1000);




/*	    $.fn.scrollStopped = function(callback) {
        var $this = $(this), self = this;
        $this.scroll(function(){
            if ($this.data('scrollTimeout')) {
              clearTimeout($this.data('scrollTimeout'));
            }
            $this.data('scrollTimeout', setTimeout(callback,400,self));

        });
    };

$(window).scrollStopped(function(){

	 $.data(this, 'scrollTimer', setTimeout(function() {
        $('.floting-location').fadeIn();
  }, 600));
});

$(window).scroll(function(){

	 $.data(this, 'scrollTimer', setTimeout(function() {
        $('.floting-location').fadeOut();
  }, 100));
});*/

</script>

<input id="map-latitude" class="map-latitude" type="hidden" value="<?php echo $lat; ?>">
<input id="map-longitude" class="map-longitude" type="hidden" value="<?php echo $lon; ?>">
<div id="googleMap" style="display:none;" class="google-Map col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"></div>
<script>
var map;
<?php if ($place == ""){ ?>
var myCenter=new google.maps.LatLng(51.508742,-0.120850);
var mapzoom = 2;
<?php }else{ ?>
//console.log("Default Location: <?php echo $lat.",".$lon; ?>");
var myCenter=new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $lon; ?>);
var mapzoom = 10;
<?php } ?>
var geocoder = new google.maps.Geocoder();
var marker;
geocoder.geocode({'latLng': myCenter}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
    //console.log(results)
      if (results[1]) {
          // document.getElementById("pac-input").value = results[0].formatted_address;

      } else {
        alert("No results found");
      }
    } else {
      alert("Geocoder failed due to: " + status);
    }
  });

function mapinitialize()
{

	var input = document.getElementById('pac-input');
	var autocomplete = new google.maps.places.Autocomplete(input);
	autocomplete.addListener('place_changed', function() {
	    var place = autocomplete.getPlace();
	    var address = place.formatted_address;
	    var latitude = place.geometry.location.lat();
	    var longitude = place.geometry.location.lng();
	    document.getElementById("map-latitude").value = latitude;
	    document.getElementById("map-longitude").value = longitude;
	  });
}

function mapinitialize1()
{
	var input = document.getElementById('pac-input2');
	var autocomplete = new google.maps.places.Autocomplete(input);
	autocomplete.bindTo('bounds', map);
	autocomplete.addListener('place_changed', function() {
	var place = autocomplete.getPlace();
	var address = place.formatted_address;
	var latitude = place.geometry.location.lat();
	var longitude = place.geometry.location.lng();
	document.getElementById("map-latitude").value = latitude;
	document.getElementById("map-longitude").value = longitude;
    });
}
google.maps.event.addDomListener(window, 'load', mapinitialize);
google.maps.event.addDomListener(window, 'load', mapinitialize1);

var mapStickyTrack;
<?php if(isset($sitesetting->bannerstatus) && $sitesetting->bannerstatus == "1" && !empty($banners)) {?>
var bannerdetails = 1;
<?php }else{ ?>
var bannerdetails = 0;
<?php } ?>
<?php if(!empty(Yii::app()->user->id)) {?>
var userdetails = 1;
<?php }else{ ?>
var userdetails = 0;
<?php } ?>
$(document).ready(function(){
	$(window).on('load resize', function () {
		if($(window).width() >= 1024){
			if(userdetails == 0)
				mapStickyTrack = $('.joysale-menu').height() + $('.joysale-app-download').height();
			else
				mapStickyTrack = $('.joysale-menu').height();
		}else{
			if(userdetails == 0)
				mapStickyTrack = $('.joysale-app-download').height();
			else
				mapStickyTrack = $('.joysale-header').height();
		}
		if(bannerdetails == 1)
		{
			mapStickyTrack = $('.joysale-header').height() + $("#myCarousel").height() + 50;
		}
		mapStickyTrack -= 56;
		//console.log('mapStickyTrack: '+mapStickyTrack);
	   // $('.sticky-screen').height($(window).height()).width($(window).width());
	});

	$(window).on('scroll', function () {
	    if ($(window).scrollTop() >= mapStickyTrack) {
	        $('.find-near-you').addClass('map-menu-fixed');
	    } else {
	        $('.find-near-you').removeClass('map-menu-fixed');
	    }
	});
});
</script>

<script	src="<?php echo Yii::app()->request->baseUrl; ?>/js/design/jquery-ui.min.js"></script>
<script type="text/javascript">
$('#pac-input').focus(function() {
    $(this).attr('placeholder', 'Where to?')
}).blur(function() {
    $(this).attr('placeholder', 'World Wide')
})

$('#pac-input2').focus(function() {
    $(this).attr('placeholder', 'Where to?')
}).blur(function() {
    $(this).attr('placeholder', 'World Wide')
})

var loadflag = 0;
</script>
<!-- Distance filter km -->
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/dstnc_filter/jshashtable-2.1_src.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/dstnc_filter/jquery.numberformatter-1.2.3.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/dstnc_filter/tmpl.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/dstnc_filter/draggable-0.1.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/dstnc_filter/jquery.slider.js"></script>
  <!-- end distance filter km -->
 <script type="text/javascript" charset="utf-8">

 	distancelimit = '<?php echo $siteSettings->searchList;?>'/2;
      jQuery("#Sliders2").slider({ from: 1, to: <?php echo $siteSettings->searchList;?>, step: 1, dimension: '&nbsp;<?php echo $siteSettings->searchType;?>',
      callback: function (value) {
        getLocationData();
    }, });
 </script>
 <script type="text/javascript" charset="utf-8">
      jQuery("#Sliders3").slider({ from: 1, to: <?php echo $siteSettings->searchList;?>, step: 1, dimension: '&nbsp;<?php echo $siteSettings->searchType;?>',
      callback: function (evt) {
        getLocationDatamobile();
    }, });

 </script>

<!--End Distance Filter -->
</body>
</html>
