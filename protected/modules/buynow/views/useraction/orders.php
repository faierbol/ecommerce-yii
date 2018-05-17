<script>
var offset = 15;
var limit = 15;
</script>
<?php
	if(count($products) == 0)
		$empty_tap = " empty-tap ";
	else
		$empty_tap = "";
	if(empty($followerlist))
		$fempty_tap = " empty-tap ";
	else
		$fempty_tap = "";
?>

<div class="container profile-page-dev">

			<div class="row">
				<div class="joysale-breadcrumb add-product col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
					 <ol class="breadcrumb">
						<li><a href="<?php echo Yii::app()->createAbsoluteUrl('/'); ?>"><?php echo Yii::t('app','Home'); ?></a></li>
						<li><a href="#"><?php echo Yii::t('app','Profile'); ?></a></li>
					 </ol>
				</div>
			</div>
	<div class="row">
		<div class="profile-vertical-tab-section col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<?php $this->renderPartial('application.modules.useractivity.views.useraction.profilemenu',array('user'=>$user,
				 			'followerIds'=>$followerIds)); ?>

				<div class="tab-content col-xs-12 col-sm-9 col-md-9 col-lg-9">

					<?php if(Yii::app()->controller->action->id == 'orders') {?>
												<!--My sales and orders-->
						<div id="Sale-order" style="display:block;" class="profile-tab-content tab-pane col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

							<div class="order-details"></div>
							<div id="loadimage" class="hiddencls" align="center">
								<div class="joysale-loader">
									<div class="cssload-loader"></div>
								</div>
							</div>
							<div class="order-content exchange-rows col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding" id="order-content">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<ul class="recent-activities-tab nav nav-tabs">
									  <li class="active"><a href="<?php echo Yii::app()->createAbsoluteUrl('/orders'); ?>"><?php echo Yii::t('app','My orders'); ?></a></li>
									  <li><a href="<?php echo Yii::app()->createAbsoluteUrl('/sales'); ?>"><?php echo Yii::t('app','My sales'); ?></a></li>

									</ul>
								</div>

								<!----------------------My order------------------->
								<div class="recent-activities-tab-content tab-content">
									<div id="my-orders" class="tab-pane fade in active">
										<div class="profile-listing-product-cnt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
											<!--row 1-->
			<?php if(!empty($orders)) { ?>
			<?php foreach($orders as $order){
			$productId = $order['orderitems'][0]['productId'];
			$check = Products::model()->findByPk($productId);

			if(!empty($check)) {
				$productImage = isset($order['orderitems'][0]->product->photos[0]->name) ? $order['orderitems'][0]->product->photos[0]->name : "";
			}

			if(!empty($check) && !empty($productImage)) {
				$productimageurl = Yii::app()->createAbsoluteUrl("/item/products/resized/100/".$productId."/".$productImage);
			}
			else
			{
				$productimageurl = Yii::app()->createAbsoluteUrl("/item/products/resized/100/".'default.jpeg');
			}

			$productName = $order['orderitems'][0]['itemName'];
			$productQuanity = $order['orderitems'][0]['itemQuantity'];
			$orderPrice = $order['orderitems'][0]['itemPrice'];
			$orderTotal = $order['totalCost'];
			$orderTotalShipping = (int)$order['totalShipping']; ?>
								<div class="exchange-row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<div class="exchange-status-row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
										<div class="exchange-status"><?php echo Yii::t('app','Status :'); ?> <span class="status-txt" id="status-accepted">
											<?php
											if($order->status == "paid")
												echo Yii::t('app',ucfirst('delivered'));
											else
											echo Yii::t('app',ucfirst($order->status));
											?>
										</span></div>
										<?php
										if($order->status != "delivered" && $order->status != "cancelled") {
											?>
										<div class="view-exchange pull-right">

								<?php echo CHtml::ajaxLink(Yii::t('app','More info >'), array('/vieworders/'.Myclass::safe_b64encode($order->orderId.'-0')),
								array(
											'beforeSend'=>'js:function(){
												$(".order-content").hide();
												$("#loadimage").show();
												$(".joysale-loader").show();
											}',
											'success' => 'js:function(response){
										        var output = response.trim();
												if (output) {
													$("#loadimage").hide();
													$(".joysale-loader").hide();
													document.body.scrollTop = document.documentElement.scrollTop = 0;
													$(".order-details").show();
										 			$(".order-details").html(output);
										 			autoHeight();
										 			$(".order-content").hide();
												}
											 }',
								)
								); ?>

										</div>
										<?php } ?>
									</div>
									<div class="exchange-content-cnt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
										<div class="exchange-left-content col-xs-12 col-sm-12 col-md-5 col-lg-7 no-hor-padding">
											<div class="prof-pic-container col-xs-12 col-sm-5 col-md-6 col-lg-5 no-hor-padding">
												<a href="<?php echo Yii::app()->createAbsoluteUrl('item/products/view',
									array('id' => Myclass::safe_b64encode($productId.'-'.rand(0,999)))).'/'.Myclass::productSlug($productName); ?>">
													<div class="exchange-prof-pic" id="exchange-pic-1" style="background-image:url(<?php echo $productimageurl;?>);">
													</div>
												</a>
												<?php $userDetail = Myclass::getUserDetails($order->sellerId); ?>
												<a href="<?php echo Yii::app()->createAbsoluteUrl('user/profiles',array('id' => Myclass::safe_b64encode($userDetail->userId.'-'.rand(1,999)))); ?>"><div class="exchange-prof-name col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
													<span class="exchange-place"> <?php echo Yii::t('app','Seller :'); ?></span>&nbsp;<?php echo $userDetail->name; ?></div>
												</a>
											</div>
											<div class="exchange-content col-xs-12 col-sm-7 col-md-6 col-lg-7 no-hor-padding">
												<a href="<?php echo Yii::app()->createAbsoluteUrl('item/products/view',
									array('id' => Myclass::safe_b64encode($productId.'-'.rand(0,999)))).'/'.Myclass::productSlug($productName); ?>"><div class="xs-text-center exchange-name bold col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
													<?php echo $productName; ?></div>
												</a>
												<div class="xs-text-center col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
	<?php

	if($_SESSION['_lang'] == 'ar')
		echo '<span>'.$order->currency.'</span> <span>'.$orderPrice.'</span>';
	else
		echo '<span>'.$orderPrice.'</span> <span>'.$order->currency.'</span>';

	?>
												</div>
												<div class="exchange-place col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Qty:'); ?>
													<span><?php echo $productQuanity; ?></span>
												</div>
											</div>
										</div>

										<div class="right-content col-xs-12 col-sm-12 col-md-5 col-lg-5 no-hor-padding">
											<div class="xs-text-center xs-paddingtop-10"><span class="exchange-place"><?php echo Yii::t('app','Order on:'); ?></span>
											<?php
						$date=date('Y-m-d', $order->orderDate);
						$dateToLocale=Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($date, 'yyyy-MM-dd'),'medium',null);
						$dateBackToMySQL=date('Y-m-d', CDateTimeParser::parse($dateToLocale, Yii::app()->locale->dateFormat));


											//echo Yii::app()->dateFormatter->formatDateTime(date('M jS Y', $chatDate)).'<br/>';
											?>
												<?php echo $dateToLocale; ?>
											</div>
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
													<div class="xs-width-100-a order-id-btn float-right">
														<?php echo Yii::t('app','Order Id:'); ?> <?php echo $order->orderId;?>
													</div>
												</div>
												<?php
												$paymentmodes = Myclass::getSitePaymentModes();
												if(($paymentmodes['cancelEnableStatus'] == "processing" && $order->status == "pending") || ($paymentmodes['cancelEnableStatus'] == "shipped" && ($order->status == "pending" || $order->status == "processing"))) {
													$cancelurl = Yii::app()->createAbsoluteUrl('buynow/useraction/cancelorder',array('id'=>$order->orderId));
												?>
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
													<a id="exc-failed" class="xs-width-100-a order-btn float-right" href="javascript:void(0);" onclick="cancel_order(<?php echo $order->orderId;?>)"><?php echo Yii::t('app','Cancel'); ?></a>
												</div>
												<?php }
												if($order->status == "delivered" || $order->status == "cancelled") {
													?>
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
										<?php echo CHtml::ajaxLink(Yii::t('app','Order details'), array('/vieworders/'.Myclass::safe_b64encode($order->orderId.'-0')),
											array(
														'beforeSend'=>'js:function(){
															$(".order-content").hide();
															$("#loadimage").show();
															$(".joysale-loader").show();
														}',
														'success' => 'js:function(response){
													        var output = response.trim();
															if (output) {
																$("#loadimage").hide();
																$(".joysale-loader").hide();
																document.body.scrollTop = document.documentElement.scrollTop = 0;
																$(".order-details").show();
													 			$(".order-details").html(output);
													 			autoHeight();
													 			$(".order-content").hide();
															}
														 }',
											),array('class'=> 'xs-width-100-a order-btn float-right exc-success')
											); ?>
												</div>
													<?php } ?>
												<?php
												if($order->status == "shipped" || $order->status == "claimed") {
													?>
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
													<a id="exc-accept" href="javascript:void(0);" onclick="changeOrderStatus('delivered','<?php echo $order->orderId; ?>')" class="xs-width-100-a order-btn float-right" ><?php echo Yii::t('app','Order received'); ?></a>
												</div>
												<?php } ?>
										</div>
									</div>
									<div class="exchange-info col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
										<!--div class="exchange-initiate-date"><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/order-placed.png'); ?>" alt="twitter"><span class="order-placed">Order palced!</span></div-->
										<div class="xs-text-center xs-paddingtop-10 xs-floatnone pull-right">
											<?php if ($order['discountSource'] != ""){ $orderTotal -= $order['discount']; } ?>
											<div class="Order-total"><?php echo Yii::t('app','Order total:'); ?>
												<span class="order-placed" id="status-accepted">
		<?php
		if($_SESSION['_lang'] == 'ar')
			echo '<span>'.$order->currency.'</span> <span>'.$orderTotal.'</span>';
		else
			echo '<span>'.$orderTotal.'</span> <span>'.$order->currency.'</span>';
		?>
												</span>
											</div>
										</div>
									</div>
								</div>
								<?php }}
								else
											{
												echo '<div style="margin: 8% auto 0;" class="payment-decline-status-info-txt">
												<img src="'.Yii::app()->createAbsoluteUrl("/images/empty-tap.jpg").'"><br>
												<span class="payment-red">'.Yii::t('app','Sorry...').'</span>
												'.Yii::t('app','You do not have any orders.').'</div>';
											}
					echo '<div class="clear urgent-tab-right">';
					$this->widget('CLinkPager',array('pages' => $pages));
					echo '</div>';
								?>


										</div>
									</div>
								</div>
									</div>
								</div>
							</div>

											<?php }?>
										</div>
									</div>
								</div>
							</div>


<div class="paypal-form-container"></div>


<style type="text/css">
.footer {
    margin-top: 0px !important;
}


</style>
<script type="text/javascript">
function hide_order_details()
{
			$(".order-details").hide();
 			$(".order-details").html("");
 			$(".order-content").show();
 		}
</script>