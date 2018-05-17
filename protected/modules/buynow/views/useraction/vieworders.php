							<!----------------------Order Details------------------->

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding" id="order-details">
	<div class="profile-tab-content-heading col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
		<span><?php echo Yii::t('app','Order Details'); ?></span>
		<div class="exchange-back-link pull-right col-xs-3 col-sm-3 col-md-3 col-lg-3 no-hor-padding"><a href="javascript:void(0);" onclick="hide_order_details();" id="element1"><?php echo Yii::t('app','Back'); ?></a></div>
	</div>
	<div class="sale-cnt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
		<!--row 1-->
			<?php
			$productId = $model['orderitems'][0]['productId'];
			$check = Products::model()->findByPk($productId);

			if(!empty($check)) {
				$productImage = isset($model['orderitems'][0]->product->photos[0]->name) ? $model['orderitems'][0]->product->photos[0]->name : "";
			}

			if(!empty($check) && !empty($productImage)) {
				$productimageurl = Yii::app()->createAbsoluteUrl("/item/products/resized/350/".$productId."/".$productImage);
			}
			else
			{
				$productimageurl = Yii::app()->createAbsoluteUrl("/item/products/resized/350/".'default.jpeg');
			}
			?>
		<div class="promotions-details-row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
				<div class="promotions-left-content col-xs-12 col-sm-12 col-md-4 col-lg-4 no-hor-padding">
					<div class="promotions-prod-pic-container col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="promotion-detail-prod-pic" style="background-image:url(<?php echo $productimageurl;?>);"></div>
						<div class="order-detail-name-header col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
							<?php echo $model['orderitems'][0]['itemName']; ?>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
	<?php
	if($_SESSION['_lang'] == 'ar')
		echo '<span>'.$model->currency.'</span> <span>'.$model['orderitems'][0]['itemunitPrice'].'</span>';
	else
		echo '<span>'.$model['orderitems'][0]['itemunitPrice'].'</span> <span>'.$model->currency.'</span>';
	?>
							</div>
							<?php if(!empty($reviewModel)){ ?>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding review-stars-container">
								<div class="write-review-1">
									<?php
									$orderRatting = $reviewModel->rating;
									for($i = 0; $i < $orderRatting; $i++){
										echo '<span class="g-color fa fa-star"></span>';
									}
									if($i != 5){
										for(; $i < 5; $i++){
											echo '<span class="gray fa fa-star"></span>';
										}
									}
									?>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<!--div class="print-icon pull-right"><a href=""><img src="<?php echo Yii::app()->createAbsoluteUrl('/images/printer.png'); ?>"><br/><span class="print-txt">Print</span></a></div-->
				<?php if(empty($reviewModel) && ($model->status=="paid" || $model->status=="delivered")){ ?>
				<div class="product-review-p text-align-center write-review-new-link">
					<a class="xs-margin-top-10  mobile-none exchange-btn" id="writebtn" href="" data-toggle="modal" data-target="#write-review-modal"><?php echo Yii::t('app','Write a review'); ?></a>
				</div>
				<div class="review-append-container"></div>
				<input type="hidden" class="review-id" value="" />
				<?php }else if(!empty($reviewModel)){ ?>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="bold review-content-heading"><?php echo $reviewModel->reviewTitle; ?></div>
						<div class="review-content-description"><?php echo $reviewModel->review; ?></div>
					<div class="review-date"><span><?php echo Yii::t('app','on'); ?></span> <?php echo date('dS M Y', $reviewModel->createdDate); ?></div>
					<div class="padding-top-10"><a class="g-color" href="" data-toggle="modal" data-target="#write-review-modal">
						<?php  echo Yii::t('app','Edit review'); ?></a>
					</div>
					<input type="hidden" class="review-id" value="<?php echo $reviewModel->reviewId; ?>" />
				</div>
				<?php } ?>
				<div class="divider col-xs-12 col-sm-12 col-md-12 col-lg-12 "></div>

			<div class="promotion-details-cnt col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
						<div class="promotions-type-cnt col-xs-12 col-sm-12 col-md-2 col-lg-2 no-hor-padding">
							<div class="promotions-type col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Seller name:'); ?></div>
							<?php $seller = Myclass::getUserDetails($model->sellerId); ?>
							<div class="padding-right-10 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
								<?php echo $seller->username; ?>
							</div>
						</div>
						<div class="vertical-divider"></div>
							<div class="paid-amt-cnt padding-right-10 col-xs-12 col-sm-12 col-md-2 col-lg-2 no-hor-padding">
								<div class="status col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Status :'); ?></div>
								<div class="status-txt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<?php if($model->status=="paid")
											echo Yii::t('app',"delivered");
										else
											echo Yii::t('app',$model->status); ?>
								</div>
							</div>
							<div class="vertical-divider"></div>
							<div class="paid-amt-cnt col-xs-12 col-sm-12 col-md-2 col-lg-2 no-hor-padding">
								<div class="paid-amt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Payment type:'); ?></div>
								<div class="paid-amt-txt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<?php echo $model['invoices'][0]['paymentMethod']; ?>
								</div>
							</div>
							<div class="vertical-divider"></div>
							<div class="paid-amt-cnt col-xs-12 col-sm-12 col-md-3 col-lg-5 no-hor-padding">
								<div class="transaction-id col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Transaction Id :'); ?></div>
								<div class="transaction-id-txt padding-right-10 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<?php echo $model['invoices'][0]['paymentTranxid']; ?>
								</div>
							</div>
						</div>
						<div class="divider col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
							<div class="upto-txt-cnt col-xs-12 col-sm-12 col-md-2 col-lg-2 no-hor-padding">
								<div class="upto col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Order Id:'); ?> </div>
								<div class="upto-txt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<?php echo $model->orderId; ?>
								</div>
							</div>
							<div class="vertical-divider"></div>
							<div class="paid-amt-cnt upto-txt-cnt col-xs-12 col-sm-12 col-md-3 col-lg-2 no-hor-padding">
								<div class="upto col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Order date:'); ?> </div>
								<div class="upto-txt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
											<?php
						$date=date('Y-m-d', $model->orderDate);
						$dateToLocale=Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($date, 'yyyy-MM-dd'),'medium',null);
						$dateBackToMySQL=date('Y-m-d', CDateTimeParser::parse($dateToLocale, Yii::app()->locale->dateFormat));


											//echo Yii::app()->dateFormatter->formatDateTime(date('M jS Y', $chatDate)).'<br/>';
											?>
												<?php echo $dateToLocale; ?>
								</div>
							</div>
							<div class="vertical-divider"></div>
							<div class="paid-amt-cnt upto-txt-cnt col-xs-12 col-sm-12 col-md-2 col-lg-2 no-hor-padding">
								<div class="upto col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Shipped date:'); ?></div>
								<div class="upto-txt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<?php if(!empty($trackingDetails)) {
										if(!empty($trackingDetails->shippingdate)) {
											//echo date("F d Y",$trackingDetails->shippingdate);
						$date=date('Y-m-d', $trackingDetails->shippingdate);
						$dateToLocale=Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($date, 'yyyy-MM-dd'),'medium',null);
						$dateBackToMySQL=date('Y-m-d', CDateTimeParser::parse($dateToLocale, Yii::app()->locale->dateFormat));


											//echo Yii::app()->dateFormatter->formatDateTime(date('M jS Y', $chatDate)).'<br/>';
												echo $dateToLocale;
										}
										else
										{
											echo "__";
										}
									}
									else
									{
										echo "__";
									}
											?>
								</div>
							</div>
							<div class="vertical-divider"></div>
							<div class="paid-amt-cnt upto-txt-cnt col-xs-12 col-sm-12 col-md-3 col-lg-3 no-hor-padding">
								<div class="upto col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Delivery confirmed date:'); ?></div>
								<div class="upto-txt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<?php
									if($model->statusDate != 0)
									{
											//echo date("F d Y",$model->statusDate);
						$date=date('Y-m-d', $model->statusDate);
						$dateToLocale=Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($date, 'yyyy-MM-dd'),'medium',null);
						$dateBackToMySQL=date('Y-m-d', CDateTimeParser::parse($dateToLocale, Yii::app()->locale->dateFormat));


											//echo Yii::app()->dateFormatter->formatDateTime(date('M jS Y', $chatDate)).'<br/>';
												echo $dateToLocale;
									}
									else
									{
										echo "__";
									}
									?>
								</div>
							</div>
						</div>
						<div class="divider col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
						<div class="promotions-type-cnt col-xs-12 col-sm-12 col-md-2 col-lg-2 no-hor-padding">
							<div class="upto col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Item amount:'); ?></div>
							<div class="padding-right-10 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
	<?php
	if($_SESSION['_lang'] == 'ar')
		echo '<span>'.$model->currency.'</span> <span>'.$model['orderitems'][0]['itemunitPrice'].'</span>';
	else
		echo '<span>'.$model['orderitems'][0]['itemunitPrice'].'</span> <span>'.$model->currency.'</span>';
	?>
							</div>
						</div>
						<div class="vertical-divider"></div>
							<div class="paid-amt-cnt padding-right-10 col-xs-12 col-sm-12 col-md-2 col-lg-2 no-hor-padding">
								<div class="upto col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Shipping fee:'); ?></div>
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
	<?php
	if($_SESSION['_lang'] == 'ar')
		echo '<span>'.$model->currency.'</span> <span>'.round($model->totalShipping,2).'</span>';
	else
		echo '<span>'.round($model->totalShipping,2).'</span> <span>'.$model->currency.'</span>';
	?>
								</div>
							</div>
							<div class="vertical-divider"></div>

							<!--div class="paid-amt-cnt padding-right-10 col-xs-12 col-sm-12 col-md-2 col-lg-2 no-hor-padding">
								<div class="upto col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Received fund:'); ?></div>
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<?php
										$received = $model->totalCost - $model->admincommission;
										echo $received.' '.$model->currency;
									?>
								</div>
							</div>
							<div class="vertical-divider"></div-->

							<div class="paid-amt-cnt col-xs-12 col-sm-12 col-md-3 col-lg-5 no-hor-padding">
								<div class="transaction-id col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Shipping address:'); ?></div>
								<div class="transaction-id-txt padding-right-10 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<?php if(!empty($shipping)) { ?>
										<b><?php echo $shipping->name; ?> </b>,<br>
										<?php echo $shipping->address1; ?>
										,<br>
										<?php echo $shipping->address2; ?>
										,<br>
										<?php echo $shipping->city; ?>
										-
										<?php echo $shipping->zipcode; ?>
										,<br>
										<?php echo $shipping->state; ?>
										,<br>
										<?php echo $shipping->country; ?>
										,<br>Phone no. :
										<?php echo $shipping->phone; ?>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>


					<div class="sale-info col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
						<div class="exchange-btn-cnt ">
						<?php if(!empty($trackingDetails)) { ?>
							<a data-target="#shipping-user-modal" data-toggle="modal" id="exc-success" class="xs-margin-top-10 xs-margin-0 exchange-btn" href=""><?php echo Yii::t('app','Shipping detail'); ?></a>
							<?php } ?>
							<a id="exc-accept" class="xs-margin-top-10 xs-margin-0 exchange-btn" href="<?php echo Yii::app()->baseUrl."/message/".Myclass::safe_b64encode($model->sellerId.'-0'); ?>">
							<?php echo Yii::t('app','Chat with seller'); ?>
							</a>
						</div>
						<div class="sale-initiate-date pull-right"> <?php echo Yii::t('app','Order total:'); ?>
						<span class="bold">
		<?php
		if($_SESSION['_lang'] == 'ar')
			echo '<span>'.$model->currency.'</span> <span>'.$model->totalCost.'</span>';
		else
			echo '<span>'.$model->totalCost.'</span> <span>'.$model->currency.'</span>';
		?>
						</span></div>
					</div>



					</div>
				</div>
			</div>
		</div>
	</div>
</div>


					<!----------------------E O Order Details------------------->

<!-- Review Popup -->

	<div class="modal fade" id="write-review-modal" role="dialog">
		<div class="modal-dialog modal-dialog-width">
			<div class="login-modal-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
				<div class="login-modal-header col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
						<h2 class="login-header-text"><?php echo Yii::t('app','Write a review'); ?></h2>
						<button data-dismiss="modal" class="close login-close" type="button">×</button>
				</div>

				<div class="login-line col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"></div>

				<div class="login-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding ">
					<div class="login-box col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
						<div class="login-text-box col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
							<label class="Category-input-box-heading col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
								<?php echo Yii::t('app','Your rating'); ?><span class="required">*</span>
							</label>
							<div class="write-review col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
								<span class="<?php echo !empty($reviewModel) && $reviewModel->rating >= 1 ? "g-color" :  "gray" ?> action-star fa fa-star star1 star2 star3 star4 star5" data-star="1"></span>
								<span class="<?php echo !empty($reviewModel) && $reviewModel->rating >= 2 ? "g-color" :  "gray" ?> action-star fa fa-star star2 star3 star4 star5" data-star="2"></span>
								<span class="<?php echo !empty($reviewModel) && $reviewModel->rating >= 3 ? "g-color" :  "gray" ?> action-star fa fa-star star3 star4 star5" data-star="3"></span>
								<span class="<?php echo !empty($reviewModel) && $reviewModel->rating >= 4 ? "g-color" :  "gray" ?> action-star fa fa-star star4 star5" data-star="4"></span>
								<span class="<?php echo !empty($reviewModel) && $reviewModel->rating >= 5 ? "g-color" :  "gray" ?> action-star fa fa-star star5" data-star="5"></span>
								<!--
								<span class="g-color fa fa-star-half-full"></span>
								<span class="gray fa fa-star-o"></span>
								-->
								<input type="hidden" class="ratting-stars" value="<?php echo empty($reviewModel) ? "0" :  $reviewModel->rating ?>" />
								<input type="hidden" class="review-order-id" value= "<?php echo $model->orderId; ?>" />
							</div>
							<label class="Category-input-box-heading  col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
								<?php echo Yii::t('app','Title'); ?><span class="required">*</span>
							</label>
							<input type="text" name="ratting-title" class="popup-input ratting-title" maxlength="60"
								value="<?php echo empty($reviewModel) ? "" :  $reviewModel->reviewTitle ?>">
							<div class="Category-input-box-row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
								<label class="Category-input-box-heading col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<?php echo Yii::t('app','Review'); ?><span class="required">*</span>
								</label>
								<textarea class="ratting-textarea form-control Category-textarea col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding" maxlength="500"><?php echo empty($reviewModel) ? "" :  $reviewModel->review ?></textarea>
							</div>
							<div class="review-error col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding" style="display:none;color:red;">
								<?php echo Yii::t('app','Please fill all the details'); ?>
							</div>
						</div>
						<a class="review-btn col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding login-btn" href="javascript:void(0);" onclick="updateReview();">
							<?php echo Yii::t('app','Submit'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

<!--E O Review Popup-->

<!--Shippping details for user popup-->

	<div class="modal fade" id="shipping-user-modal" role="dialog">
		<div class="modal-dialog modal-dialog-width">
			<div class="login-modal-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
					<div class="login-modal-header col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
							<h2 class="login-header-text"><?php echo Yii::t('admin','Tracking Details'); ?></h2>
							<button data-dismiss="modal" class="close login-close" type="button">×</button>
					</div>

						<div class="login-line col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"></div>

							<div class="login-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding ">
								<div class="login-box col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<div class="login-text-box col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

									<?php if(!empty($trackingDetails)) { ?>
									<div class="inv-shipping" style="width: 35%; float: left;">

										<?php if(!empty($trackingDetails->trackingid)) {
											echo '<label class="margin-bottom-0 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">';
											echo Yii::t('app','Tracking ID');
											echo ':</label>';
											?>
										 <div class="padding-bottom-10 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
										<?php echo $trackingDetails->trackingid; ?> </div>
										<?php } ?>
										<br>
										<?php if(!empty($trackingDetails->shippingdate)) {
											echo '<label class="margin-bottom-0 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">';
											echo Yii::t('app','Shipment Date');
											echo ':</label>';
											?>
										 <div class="padding-bottom-10 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
										<?php echo date("d-m-Y",$trackingDetails->shippingdate); ?>
										</div>
										<?php } ?>
										<br>
										<?php if(!empty($trackingDetails->couriername)) {
											echo '<label class="margin-bottom-0 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">';
											echo Yii::t('admin','Logistic Name');
											echo ':</label>';
											?>
										 <div class="padding-bottom-10 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
										<?php echo $trackingDetails->couriername; ?> </div>
										<?php } ?>
										<br>
										<?php if(!empty($trackingDetails->courierservice)) {
											echo '<label class="margin-bottom-0 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">';
											echo Yii::t('admin','Shipment Service');
											echo ':</label>';
											?>
										 <div class="padding-bottom-10 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
										<?php echo $trackingDetails->courierservice; ?> </div>
										<?php } ?>
										<br>
										<?php if(!empty($trackingDetails->notes)) {
											echo '<label class="margin-bottom-0 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">';
											echo Yii::t('admin','Additional Notes');
											echo ':</label>';
											?>
										 <div class="padding-bottom-10 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
										<?php echo $trackingDetails->notes; ?> </div>
										<?php } ?>
										<br>

									</div>
									<?php } ?>
										</div>
									</div>
								</div>
							</div>
			</div>
		</div>
	</div>

<!--E O Shippping details for user popup-->
