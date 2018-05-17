<script>
var offset = 32;
var limit = 32;
var adsoffset = 8;
</script>
<style>
.no-more {
	font-weight: bold;
	padding: 5%;
	text-align: center;
	margin-top: 20px;
}
#content {
    min-height: 0 !important;
}
</style>
<?php
//echo $subcategory;

//die;
 if(empty($subcats)) {  ?>

<!--  <div class="jumbotron banner-bg fixed">
	<div class="container-fluid">
		<br>
		<h1 align="center">Welcome To Happy Sale!</h1>
		<p>This is a template for a simple marketing or informational website.
			It includes a large callout called a jumbotron and three supporting
			pieces of content. Use it as a starting point to create something
			more unique.</p>
		<p>
			<a role="button" href="#" class="btn btn-primary btn-lg">Learn more »</a>
		</p>
	</div>
</div> -->
<?php }

?>
	<!-- location Modal -->
	<?php
	$sitesetting = Myclass::getSitesettings();
	$bannervideo=$sitesetting->bannervideo;
	$bannervideoStatus=$sitesetting->bannervideoStatus;
	$bannervideoposter=$sitesetting->bannervideoposter;
	$bannerText=$sitesetting->bannerText;
	$extensionArray=explode(".",$bannervideo);
if(!isset($_GET['search']) && empty($category) && empty($subcategory))
	{
	if(isset($sitesetting->bannerstatus) && $sitesetting->bannerstatus == "1" && !empty($banners))
	{

	?>
<?php if($bannervideo!="" && $bannervideoStatus==1) {
$slider="display: none;";
	?>
<div class="slider-imag">
<div class="vide-slider-imag">
	<div class="img-video">
		<video id="intro-video" src="<?php echo Yii::app()->createAbsoluteUrl('/media/banners/videos/'.$bannervideo);?>" type="video/<?php echo $extensionArray[1];?>" class="video-cover" preload="" loop="loop" muted="muted" autoplay="autoplay" poster="<?php //echo Yii::app()->createAbsoluteUrl('/media/banners/videos/'.$bannervideoposter);?>">
		</video>
	</div>
<?php $footerSettings = Myclass::getFooterSettings();?>
	<div class="img-slide-contetnt">
		<div class="img-text txt-white-color text-align-center"><h1 class="bold"><?php echo $bannerText;?></h1>

			<ul class="text-link">
				<?php if(isset($footerSettings['appLinks']['android'])){ ?>
				<li><a class="sendapp_link ios_link" target="_blank"  href="<?php echo $footerSettings['appLinks']['android']; ?>" target="_self"><img src="images/google-play-download-badge.svg" alt="Android" width="145" height="50"></a></li>
				<?php }
				if(isset($footerSettings['appLinks']['ios'])){ ?>
				<li><a class="sendapp_link" target="_blank" href="<?php echo $footerSettings['appLinks']['ios']; ?>" target="_self"><img src="images/app-store-download-badge.svg" alt="Ios" width="145" height="50"></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
</div>
<?php }
else{
	$slider="display: block;";
}
?>


<!--E O banner Video-->
<?php
?>
<div class="slider-container">
			<div class="container-fluid">
			<div class="row">




		<!--Banner image-->
			 <div class="banner-image" style="<?php echo $slider;?>">
					<div id="myCarousel" class="carousel slide" data-ride="carousel">
					  <!-- Indicators -->
					  <ol class="slider-pointer carousel-indicators">
					  <?php
					  foreach ($banners as $key => $banner) {
					  	if($key == 0)
					  	{
					  		echo '<li data-target="#myCarousel" data-slide-to="0" class="active"></li>';
					  	}
					  	else
					  	{
					  		echo '<li data-target="#myCarousel" data-slide-to="'.$key.'"></li>';
					  	}
					  }
					  ?>
					  </ol>

					  <!-- Wrapper for slides -->
					  <div class="carousel-inner" role="listbox">
					  <?php
					  foreach ($banners as $key => $banner) {
					  	if($key == 0)
					  	{
					  		$imageurl = Yii::app()->createAbsoluteUrl('/media/banners/'.$banner->bannerimage);
							echo '<div class="item active">
							  <a href="'.$banner->bannerurl.'" target="_blank"><img src="'.$imageurl.'" alt="'.$banner->bannerimage.'"></a>
							</div>';
						}
						else
						{
							$imageurl = Yii::app()->createAbsoluteUrl('/media/banners/'.$banner->bannerimage);
							echo '<div class="item">
							  <a href="'.$banner->bannerurl.'" target="_blank"><img src="'.$imageurl.'" alt="'.$banner->bannerimage.'"></a>
							</div>';
						}
					}
						?>

					  <!-- Left and right controls
					  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
						<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
						<span class="sr-only">Previous</span>
					  </a>
					  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
						<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					  </a>-->
					</div>
				</div>
			</div>

			<!--E O banner image-->

		</div>
	</div>
	</div>
	<?php
}
}
?>

	<div class="container-fluid">
	<?php
//if(!empty($subcats)) {
	if(!empty($category)) {?>
<div class="col-md-12 category_button">
<?php	//foreach($subcats as $subcat):
$subactive = "";$subIcon = "";
$subcategoryUrl = Yii::app()->createAbsoluteUrl('/category/'.$category.'/'.$subcat->slug);
if($subcategory == $subcat->slug){
$subactive = "active";
//$subcategoryUrl = Yii::app()->createAbsoluteUrl('/category/'.$category);
$categoryUrl = Yii::app()->createAbsoluteUrl('/category/'.$category);
$subIcon = "<i class='fa fa-times-circle'></i> ";
}
if(!empty($subcategory)){
	$subcategoryUrl = Yii::app()->createAbsoluteUrl('/category/'.$category.'/'.$subcat->slug);
}
if(!empty($category)){
	$categoryUrl = Yii::app()->createAbsoluteUrl('/category/'.$category);
}

$searchUrl = "";
if(isset($_GET['search'])){
	$searchUrl = "?search=".$search;
}?>
<?php /*?>
	<div class="btn-group">
	<?php echo CHtml::link($subIcon.$subcat->name, $subcategoryUrl, array('class' => 'btn btn-lg btn-category '.$subactive));  ?>
	</div>
<?php */ ?>

	<div class="row">
			<div class="joysale-breadcrumb add-product col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
				 <ol class="breadcrumb">
					<li><a href="<?php echo Yii::app()->createAbsoluteUrl('/')?>"><?php echo Yii::t('app','Home');?></a></li>
					<?php if(!empty($categoryname)){?>
					<li><a href="<?php echo $categoryUrl.$searchUrl; ?>"><?php echo Yii::t('app',$categoryname); ?></a></li>
					<?php if (!empty($subcategory)) {
						$subcategoryUrll= Yii::app()->createAbsoluteUrl('/category/'.$category.'/'.$subcategory);?>
						<li><a href="<?php echo $subcategoryUrll.$searchUrl; ?>"><?php echo Yii::t('app',$subcategory); ?></a></li>
						<?php }}
						else{?>
						<li><a href="<?php echo $categoryUrl.$searchUrl; ?>"><?php echo Yii::t('app','All categories');?></a></li>
					<?php 	} ?>
				 </ol>
			</div>
		</div>

			<div class="row">
				<div class="full-horizontal-line col-xs-12 col-sm-12 col-md-12 col-lg-12 "></div>
			</div>
	<?php //endforeach; ?>
</div>
	<?php  }if(isset($_GET['search']) && !empty($_GET['search'])) { ?>
	<div class="row">
		<div class="search-result col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
			<div><?php echo Yii::t('app','Search Result');?> <span class="search-result-text">"<?php echo $search; ?>"</span></div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
			<div class="full-horizontal-line col-xs-12 col-sm-12 col-md-12 col-lg-12 "></div>
		</div>
	</div>
	<?php } //echo CHtml::link('Clear',Yii::app()->createAbsoluteUrl('/'),array('class' => 'btn btn-lg btn-primary')); } ?>

	<?php /*?>
			<a class="dropdown-near-you col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding" href="#" data-toggle="modal" data-target="#nearmemodals">
			<img src="<?php echo Yii::app()->createAbsoluteUrl('images/design/location.png'); ?>" alt="Location">
			<span class="miles">3 mi from you</span>
			<div class="dropdown-btn"><img src="<?php echo Yii::app()->createAbsoluteUrl('images/design/down-arrow.png'); ?>" alt="arrow"></div></a>
	<?php */ ?>



	  </div>


	</div>




<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div style="background-color: rgba(255, 255, 255, 0.5); z-index: 9999; width: 100%; height: 100%; position: fixed; left: 0px; top: 0px; display: none;" id="location-loader" class="joysale-loader-old">

								<div style="position: relative; left: 50%; top: 50%;" class="cssload-loader"></div>
							</div>
<?php
if((!empty($products) && count($products) > 0) || (!empty($adsProducts) && count($adsProducts) > 0)){
	if(!isset($_GET['loadData']) && (!empty($category) || (isset($_GET['search'])))) { ?>

<div class="productfileters">
	<div class="container-fluid">
		<div class="row">

			<div class="categories-filter-list filter-search col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

				<span class="for-sale-heading"><?php echo Yii::t('app','Search Only'); ?></span>
			<?php if(isset($sitesetting->promotionStatus) && $sitesetting->promotionStatus == "1"){ ?>

			<div class="search-filter-list">
				<div class="checkbox checkbox-primary">
					<?php $urgentCheck = ""; if(isset($urgent) && $urgent == 1) $urgentCheck = "checked";?>
				  <input type="checkbox" name="sport[]" value="" <?php echo $urgentCheck; ?> class="cust_checkbox urgent" onclick="promotionsearch('urgent');" />
				  <label><?php echo Yii::t('app','Urgent'); ?></label>
				</div>
			<?php } ?>

				<div class="checkbox checkbox-primary">
				 <?php $adsCheck = ""; if(isset($ads) && $ads == 1) $adsCheck = "checked";?>
				  <input type="checkbox" name="sport[]" value="" <?php echo $adsCheck; ?> class="cust_checkbox ads" onclick="promotionsearch('ads');" />
				  <label><?php echo Yii::t('app','Popular'); ?></label>
				</div>
			</div>

			</div>

			<div class="categories-filter-list filter-distnc-km mobile-distnce-view col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
				<span class="for-sale-heading">Filter distance</span>
				<div class="filter-distance col-md-12 col-sm-12 col-xs-12 no-hor-padding">
					<div class="filter-align">
						<div class="filtr-hm"></div>
	      			 	<div class="layout-slider">
	      			 		<?php
	      			 		//echo $sitesetting->searchList;
	      			 		if(isset(Yii::app()->session['distance']))
	      			 			$distance = Yii::app()->session['distance'];
	      			 		else
	      			 			$distance = '1';
	      			 		?>
				     	 	<input id="Sliders3" type="slider" name="price" value="0;<?php echo $distance;?>" />
				    	</div>
		      			<div class="filtr-road"></div>
	      			</div>
				</div>
			</div>


		</div>
	</div>
</div>


<!-- <div class="col-md-12 category_button"> -->
<?php	//foreach($subcats as $subcat):
//echo "<pre>"; print_r($category);die;
$searchUrl = "";
if(isset($_GET['search'])){
	$searchUrl = "?search=".$search;
}
$categoryName = Myclass::getCategoryName($category);
?>
	<div id="products" class="col-xs-2 col-sm-3 col-md-3 col-lg-2 no-hor-padding categories margin-top-20 margin-bottom_20">
	<div class="item categories">
		<div class="grid cs-style-3 no-hor-padding">
		<?php if(!empty($subcats)) { ?>
			<div class="categories-list col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

				<ul><span class="for-sale-heading"><?php echo Yii::t('app',$categoryName); ?></span>
				<?php foreach($subcats as $subcat):
				$subactive = "";$subIcon = "";
				$subcategoryUrl = Yii::app()->createAbsoluteUrl('/category/'.$category.'/'.$subcat->slug).$searchUrl;
				if($subcategory == $subcat->slug){
				$subactive = "active";
				$subcategoryUrl = Yii::app()->createAbsoluteUrl('/category/'.$category).$searchUrl;
				$subIcon = "<i class='fa fa-times-circle'></i> ";
				}?>
				<li class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><a class=" btn-lg btn-category <?php echo $subactive; ?>" href="<?php echo $subcategoryUrl; ?>"><?php echo Yii::t('app',$subcat->name); ?></a></li>
				<?php endforeach; ?>
				</ul>

			</div>
			<?php }else {

			if($category=='allcategories') { $categoryName='All categories'; }
				$categoryUrl = Yii::app()->createAbsoluteUrl('/category/'.$category);
				if(!empty($category))
				{
				?>
				<div class="categories-list col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

				<ul>
				<span class="for-sale-heading"><?php echo Yii::t('app','Current Category');?></span>
				<li class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><a href="<?php echo $categoryUrl; ?>" class=" btn-lg btn-category active"><?php echo Yii::t('app',$categoryName); ?></a></li>
				</ul>

			</div>
			<?php } ?>

				<?php } ?>

			<div class="categories-filter-list filter-search col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

				<span class="for-sale-heading"><?php echo Yii::t('app','Search Only'); ?></span>
			<?php if(isset($sitesetting->promotionStatus) && $sitesetting->promotionStatus == "1"){ ?>

				<div class="checkbox checkbox-primary col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
					<?php $urgentCheck = ""; if(isset($urgent) && $urgent == 1) $urgentCheck = "checked";?>
				  <input type="checkbox" name="sport[]" value="" <?php echo $urgentCheck; ?> class="cust_checkbox urgent" onclick="promotionsearch('urgent');" />
				  <label><?php echo Yii::t('app','Urgent'); ?></label>
				</div>
			<?php } ?>

				<div class="checkbox checkbox-primary col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
				 <?php $adsCheck = ""; if(isset($ads) && $ads == 1) $adsCheck = "checked";?>
				  <input type="checkbox" name="sport[]" value="" <?php echo $adsCheck; ?> class="cust_checkbox ads" onclick="promotionsearch('ads');" />
				  <label><?php echo Yii::t('app','Popular'); ?></label>
				</div>

			</div>

			<div class="categories-filter-list filter-distnc-km mobile-distnce-view col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
				<span class="for-sale-heading">Filter distance</span>
				<div class="filter-distance col-md-12 col-sm-12 col-xs-12 no-hor-padding">
					<div class="filter-align">
						<div class="filtr-hm"></div>
	      			 	<div class="layout-slider">
	      			 		<?php
	      			 		//echo $sitesetting->searchList;
	      			 		if(isset(Yii::app()->session['distance']))
	      			 			$distance = Yii::app()->session['distance'];
	      			 		else
	      			 			$distance = '1';
	      			 		?>
				     	 	<input id="Sliders2" type="slider" name="price" value="0;<?php echo $distance;?>" />
				    	</div>
		      			<div class="filtr-road"></div>
	      			</div>
				</div>
			</div>

			<div class="categories-menu-list categories-list col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
				<ul><span class="for-sale-heading"><?php echo Yii::t('app','Categories'); ?></span>
				<?php
				$categoryId[] = Myclass::getCategoryId($category);
				$categorypriority = Myclass::getCategory();
				//echo "<pre>"; print_r($categorypriority); die;
				foreach($categorypriority as $key => $categorypriority):
				if($categorypriority != "empty"){
					if(!in_array($categorypriority->categoryId,$categoryId)) {
						//$getcatdet = Myclass::getCatDetails($categorypriority);
						$categoryUrl = Yii::app()->createAbsoluteUrl('/category/'.$categorypriority->slug).$searchUrl;
						?>
							<li class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><a href="<?php echo $categoryUrl; ?>"><?php echo Yii::t('app',$categorypriority->name); ?></a></li>
						<?php
					}
				}
				endforeach;
				?>
				</ul>
			</div>



		</div>
	</div>
	</div>
	<?php //endforeach; ?>
<!-- </div> -->

	<?php  }
}	?>
<?php
if(!isset($_GET['loadData']) && (!empty($category) || (isset($_GET['search'])))) {
	?>
		<div id="products" class="slider container-fluid col-xs-12 col-sm-12 col-md-9 col-lg-10 no-hor-padding">
<?php } else { ?>
<div id="products" class="slider container-fluid col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
	<?php } ?>
		<?php $this->renderPartial('loadresults',compact('adsProducts','catrest','products','locationReset','pages','search','category','subcategory','subcats','lat','lon','place','loadMore','productcount')); ?>
		</div>
		<div class="row no-more" style="display: none;">
			<div class="no-item col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
				<div>
					<span class="no-item-text col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<?php echo Yii::t('app','Sorry! No item this location'); ?>.
					</span>
					<span class="world-wide col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<?php echo Yii::t('app','We are showing world wide'); ?>
					</span>
					<div class="back-botton col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<a href="<?php echo Yii::app()->createAbsoluteUrl('/'); ?>" class="back-btn">
							<?php echo Yii::t('app','Back'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>


		<?php /* <div class="no-more" style="display: none; min-height: 320px;margin-top:3%">
			<div class="paypal-success" style="margin: 0 auto;">
				<div class="paypal-success-icon fa fa-exclamation-triangle"
					style="color: #2FDAB8; font-size: 40px;"></div>
				<br>
				<div class="not-found-message"></div>
				<a class="btn btn-primary sell-button"
					href="<?php echo Yii::app()->createAbsoluteUrl('/'); ?>"> <i
					class="fa fa-home" style="font-size: 20px;"> </i> <?php echo Yii::t('app','Back To Home Page'); ?>
				</a>
			</div>
		</div>
		<?php /* $this->widget('ext.yiinfinite-scroll.YiinfiniteScroller', array(
		'contentSelector' => '#products',
		'itemSelector' => 'div.col-md-4.product',
		'loadingText' => 'Loading...',
		'donetext' => 'No More Products',
		'pages' => $pages,
		)); */
		?>
		<?php if(empty($products)) { ?>
		<script>
            /* $(".load").hide();
            $(".no-more").show();
            $("#products").hide();
            $(".not-found-message").html('<b>'+Yii.t('app','No Items Found.')+'</b>'); */
		</script>



		<?php } ?>
		<!--<script>
			$(window).load(function() {
				$('.imgcls').load(function () {
					$('.imgcls').addClass('hgtremoved');
				}).attr('style', 'height: auto !important');
			});
		</script>-->
<script type="text/javascript">


	var isPreviousEventComplete = true, isDataAvailable = true;
	var search = '<?php echo $search; ?>';
	var subcategory = '<?php echo $subcategory;?>';
	var lat = '<?php echo $lat; ?>';
	var lon = '<?php echo $lon; ?>';
	var urgent = '<?php echo $urgent;?>';
	var ads = '<?php echo $ads; ?>';

$(window).scroll(function () {
 //if ($(document).height() - 50 <= $(window).scrollTop() + $(window).height()) {
	if ($(window).scrollTop() >= ($(document).height() - $(window).height())*0.5 && loadflag==2) {
		$(".more-listing").hide();
  if (isPreviousEventComplete && isDataAvailable) {

    isPreviousEventComplete = false;

    $.ajax({
      type: "GET",
      url: yii.urls.base+'/site/loadresults?search=' + search + '&subcategory=' + subcategory + '&lat='+lat+'&lon='+lon,
      data : {
      	"limit": limit, "offset": offset, "loadData": 1,"adsOffset": adsoffset,"urgent": urgent,"ads": ads
      },
	  dataType: 'html',
      beforeSend: function () {
			$(".more-listing").hide();$(".joysale-loader").show();
		},
      success: function (response) {
      	var out = $.trim(response.toString());
			if (out) {//When data is not available
				var grid = document.querySelector("#fh5co-board");
				$(".more-listing").show();$(".joysale-loader").hide();
         		var output = response.trim();
				var contentData = eval($.trim(output));
				if (output) {
					offset = offset + limit;
					adsoffset = adsoffset + 10;
					//$("#products").append(output);
					//$("#fh5co-board").append($.trim(output));
					//salvattore.recreateColumns(grid);
					for(var i = 0; i < contentData.length; i++){
			            var item = document.createElement("div");
						salvattore["append_elements"](grid, [item]);
						item.outerHTML = contentData[i];
					}

				$('.imgcls').load(function () {
					$('.imgcls').addClass('hgtremoved');
				}).attr('style', 'height: auto !important');

				} else {
					$(".joysale-loader").hide();
					$(".more-listing").hide();
				}
        }else {
            isDataAvailable = false;
					$(".joysale-loader").hide();
					$(".more-listing").hide();
		}
        //offset = offset + limit;
        isPreviousEventComplete = true;
      }
    });

  }
 }
 });
</script>