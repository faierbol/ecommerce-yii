<?php if(empty($exchanges)){
	$empty_tap = " empty-tap ";
}else{
	$empty_tap = "";
	} ?>

<?php if(count($logModel) != '0') {
	foreach ($logModel as $log){
		$productModel = array();
		if($log->itemid != 0){
			$productModel = Myclass::getProductDetails($log->itemid);
		}
		$userModel = Myclass::getUserDetails($log->userid);
		if(!empty($userModel->userImage)){
			$userImage = Yii::app()->createAbsoluteUrl('user/resized/150/'.$userModel->userImage);
		}else{
			$userImage = Yii::app()->createAbsoluteUrl('user/resized/150/default/'.Myclass::getDefaultUser());
		}
		//$createdDate = date('jS M Y', $log->createddate);
		$createdDate = $log->createddate;
?>
	<div class="notification-row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
		<div class="notification-pro-pic-cnt">
		<?php if ($log->type != 'admin' && $log->type != "adminpayment"){ ?>
			<a href="<?php echo Yii::app()->createAbsoluteUrl('user/profiles',
					array('id'=>Myclass::safe_b64encode($userModel->userId.'-'.rand(0,999)))); ?>"  target="_blank"
					title="<?php echo $userModel->username; ?>">
				<div class="notification-prof-pic" id="notif-prof-1" style="background-image: url('<?php echo $userImage; ?>');"></div>
			</a>
		<?php }else{ ?>
			<a href="javascript:void(0);">
				<div class="notification-prof-pic" id="notif-prof-1" style="background-image: url('<?php echo $userImage; ?>');"></div>
			</a>
		<?php } ?>
		</div>
		<div class="notification-message-cnt">
			<div class="notification-message">
			<?php if ($log->type != 'admin' && $log->type != "adminpayment"){ ?>
				<a href="<?php echo Yii::app()->createAbsoluteUrl('user/profiles',
					array('id'=>Myclass::safe_b64encode($userModel->userId.'-'.rand(0,999)))); ?>"  title="_blank"
					title="<?php echo $userModel->username; ?>">
					<?php echo $userModel->name; ?>
				</a> <?php
				if($log->type == 'order')
				{
				$string = $log->notifymessage;
				$value = explode(" Order Id :", $string);
				if (strpos($string, 'Order Id :') !== false) {
				echo Yii::t("app", $value[0])." ".Yii::t("app", 'Order Id :').$value[1];
			    }
			    else
			    {
				echo Yii::t("app", $log->notifymessage);

			    }
				}
				elseif($log->type == 'myoffer')
				{
				$string = $log->notifymessage;
				$value = explode("sent offer request", $string);
					if($value[0] != "contacted you on your product")
					{
						$split_point = "on your product";
						$strings = $value[1];
						$rev = array_reverse(explode($split_point, $strings));
						//$value = explode("sent offer request", $string);
		//				print_r($rev);
						echo Yii::t("app", "sent offer request")." ".$rev[1]." ".Yii::t("app", "on your product");
					}
					else
					{
						echo Yii::t("app", "contacted you on your product");
					}
				}
				else
				{
				echo Yii::t("app", $log->notifymessage);
				}
//			   echo $var;
			     ?>
				<?php if (!empty($productModel)){ ?>
				<a href="<?php echo Yii::app()->createAbsoluteUrl('item/products/view',
						array('id' => Myclass::safe_b64encode($productModel->productId.'-'.rand(0,999)))).'/'.Myclass::productSlug(
						$productModel->name); ?>" class="notification-product-name" title="_blank">
					<?php echo $productModel->name; ?>
				</a>
				<?php } ?>
			<?php }else if($log->type=="adminpayment")
			{?>
				<a href="javascript:void(0);">
					<?php echo Myclass::getSiteName()." "; ?>
				</a> <?php echo Yii::t("app", $log->notifymessage); ?>
			<?php }
			else{ ?>
				<a href="javascript:void(0);">
					<?php echo Myclass::getSiteName()." "; ?>
				</a> <?php echo Yii::t("app", $log->notifymessage)." '".$log->message."'"; ?>
			<?php } ?>
			</div>
			<div class="notification-date">
											<?php
						$date=date('Y-m-d', $createdDate);
						$dateToLocale=Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($date, 'yyyy-MM-dd'),'medium',null);
						$dateBackToMySQL=date('Y-m-d', CDateTimeParser::parse($dateToLocale, Yii::app()->locale->dateFormat));


											//echo Yii::app()->dateFormatter->formatDateTime(date('M jS Y', $chatDate)).'<br/>';
											?>
												<?php echo $dateToLocale; ?>

			</div>

			<!--div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
				<a class="acceptt-btn notify-btn margin_btm_btn" href="#">Accept</a>
				<a class="delete-btn notify-btn margin_btm_btn margin_right10" href="#">Decline </a>
				<a class="counter-btn notify-btn margin_btm_btn" href="#" data-dismiss="modal" data-toggle="modal" data-target="#count_modal">Counter </a>
			</div-->


		</div>
	</div>
<?php }
	} else { ?>
		<div class="modal-dialog modal-dialog-width">
			<div class="col-xs-8 col-sm-12 col-md-12 col-lg-12 no-hor-padding" style="margin-bottom:100px;">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
					<div class="payment-decline-status-info-txt" style="margin: 8% auto 0;">
						<img src="<?php echo Yii::app()->createAbsoluteUrl("/images/empty-tap.jpg");?>">
						</br><span class="payment-red"><?php echo Yii::t('app','Sorry...');?></span> <?php echo Yii::t('app','You have no notification');?><?php echo ".";?>
					</div>
				</div>
			</div>
		</div>

<?php  } ?>


	<!--offer modal-->
			<div class="modal fade" id="count_modal" role="dialog" tabindex='-1'>
				<div class="modal-dialog modal-dialog-width">
					<div class="login-modal-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
							<div class="login-modal-header col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
									<h2 class="login-header-text">Counter</h2>
									<button data-dismiss="modal" class="close login-close" type="button">×</button>
							</div>

								<div class="login-line col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"></div>

								<div class="login-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding ">
									<div class="counter-price-text clearfix">
										 <div class="right-borer col-xs-6 col-sm-6 col-md-6 col-lg-6">
										 	<div class="price-text">Asking Price</div>
										 	<div class="price-rate">$ 90.00</div>
										 </div>
										 <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										 	<div class="price-text">Offer Price</div>
										 	<div class="price-rate">$ 60.00</div>
										 </div>
									</div>
									<div class="offer-price-section col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

										<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">Your Counter Price :</label>
										<div class="offer-price-txt-field-cnt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

											<div class="offer-text-field-label col-xs-1 col-sm-1 col-md-1 col-lg-1 no-hor-padding">$</div>
											<div class="offer-text-field col-xs-11 col-sm-11 col-md-11 col-lg-11 no-hor-padding">
												<input type="text" class="my-offer-rate" maxlength="9" id="MyOfferForm_offer_rate" placeholder="Enter your price">
												<div class="message-error" style="color: red;"></div>
											</div>

										</div>

										<div class="send-btn-container col-xs-12 col-sm-3 col-md-3 col-lg-3 no-hor-padding pull-right">
											<a href="javascript:;" onClick="myoffer()">
												<div class="send-btn primary-bg-color txt-white-color text-align-center offer-send-btn">Send</div>
											</a>
										</div>
										<div id="errorMessage" style="color: red"></div>
									</div>
								</div>

					</div>
				</div>
			</div>
		<!--E O offer modal-->
