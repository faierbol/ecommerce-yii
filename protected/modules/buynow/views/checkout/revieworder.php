<div class="container">
	<div class="row">
		<div class="joysale-breadcrumb add-product col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
			 <ol class="breadcrumb">
				<li><a href="<?php echo Yii::app()->createAbsoluteUrl("/"); ?>"><?php echo Yii::t("app", "Home")?></a></li>
				<li><a href="javascript:void(0);"><?php echo Yii::t("app", "Checkout")?></a></li>
			 </ol>
		</div>

	</div>

		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="full-horizontal-line col-xs-12 col-sm-12 col-md-12 col-lg-12 "></div>

			</div>
		</div>

		<div class="row">
				<div class="add-product-heading col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<h2 class="top-heading-text"><?php echo Yii::t('app','Review Order'); ?></h2>
					<p class="top-heading-sub-text"><?php echo Yii::t('app','Your order summary and other details'); ?></p>
				</div>
		</div>

		<!--payment accordion-->
		<div class="joysale-accorddion-cnt col-xs-12 col-sm-12 no-hor-padding">

			 <div class="panel-group" id="accordion">
				  <div class="joysale-accordion panel panel-default">
					<div class="joysale-accordion-heading panel-heading">
					  <div class="joysale-accordion-title-cnt panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                            <div class="joysale-accordion-title-txt joysale-accordion-title-sel"><?php echo Yii::t('app','1.Select address');?></div>
                        </a>
                        <?php
                        if(!empty($shippingAddressesModel)) {
                        	?>
                        <a class="joysale-acc-add-addr" onclick="clear_add_address();" data-toggle="modal" data-target="#address-modal" href="" style="float: right;">
                        	+ <?php echo Yii::t('app','Add address'); ?></a>
                        	<?php } ?>
					  </div>

					</div>
					<div id="collapse1" class="panel-collapse collapse in">
					  <div class="panel-body">
					  <?php if(empty($shippingAddressesModel)) { ?>
						<div class="joysale-acc-noaddr-cont col-xs-12 col-sm-12">
							<div class="joysale-acc-noaddr-txt"><?php echo Yii::t('app',"You haven’t added any shipping address yet."); ?></div>
							<div class="joysale-acc-noaddr-btn-cnt">
								<div class="joysale-acc-noaddr-btn">
									<a class="joysale-acc-add-addr" data-toggle="modal" data-target="#address-modal" href="">
										<?php echo Yii::t('app','Add Shipping'); ?>
									</a>
								</div>
							</div>
						</div>
						<input type="hidden" class="selected-shipping" id="selectedshipping" value="">
					  <?php }else{ ?>
						<div class="joysale-acc-addr-container col-xs-12 col-sm-12 no-hor-padding">
							<?php
							if(isset($_SESSION['shipping_id']) && $_SESSION['shipping_id'] != "")
							{
								$sessionshippingId = $_SESSION['shipping_id'];
								?>
							<input type="hidden" class="selected-shipping" id="selectedshipping" value="<?php echo $_SESSION['shipping_id'];?>">
							<?php
							}
							else if($shippingAddress->countryCode == $countrydata->countryId)
							{
							?>
							<input type="hidden" class="selected-shipping" id="selectedshipping" value="<?php echo $shippingAddress->shippingaddressId;?>">
							<?php } else if(count($shippingAddressesModel)==1){
								foreach ($shippingAddressesModel as $shippingAddresses){
								?>
							<input type="hidden" class="selected-shipping" id="selectedshipping" value="<?php echo $shippingAddresses->shippingaddressId; ?>">
							<?php }} else {?>
							<input type="hidden" class="selected-shipping" id="selectedshipping" value="">
							<?php } ?>
							<?php

							foreach ($shippingAddressesModel as $shippingAddresses){
								$userDefaultAddress = Myclass::getDefaultShippingAddress($shippingAddresses->userId);
								$nickName = $shippingAddresses->shippingaddressId.'-'.$shippingAddresses->nickname;
								$nicknameLabel = $shippingAddresses->nickname;
								$selected = '';
								if ($userDefaultAddress == $shippingAddresses->shippingaddressId && $sessionshippingId == "")
								$selected = 'selected';
							?>
							<div class="joysale-acc-addr-cont col-xs-12 col-sm-4 col-md-3">
								<?php
								if ($userDefaultAddress == $shippingAddresses->shippingaddressId  && $sessionshippingId == "" || count($shippingAddressesModel)==1) {
									?>
								<div class="joysale-acc-addr-cnt col-xs-12 col-sm-12 no-hor-padding shadowcls" id="highlight<?php echo $shippingAddresses->shippingaddressId; ?>">
									<div class="addressblock address-active <?php echo $selected; ?>" id="activeaddr<?php echo $shippingAddresses->shippingaddressId; ?>"></div>
									<?php
								}else if(isset($sessionshippingId) && $sessionshippingId != "" && $sessionshippingId == $shippingAddresses->shippingaddressId){
								?>
								<div class="joysale-acc-addr-cnt col-xs-12 col-sm-12 no-hor-padding shadowcls" id="highlight<?php echo $shippingAddresses->shippingaddressId; ?>">
									<div class="addressblock address-active <?php echo $selected; ?>" id="activeaddr<?php echo $shippingAddresses->shippingaddressId; ?>"></div>
								<?php }else {?>

								<div class="joysale-acc-addr-cnt col-xs-12 col-sm-12 no-hor-padding" id="highlight<?php echo $shippingAddresses->shippingaddressId; ?>">
									<div class="address-active <?php echo $selected; ?>" id="activeaddr<?php echo $shippingAddresses->shippingaddressId; ?>"></div>
								<?php }
								$_SESSION['shipping_id'] = "";
								 ?>
									<div class="joysale-acc-icon-cnt col-xs-12 col-sm-6 col-md-6 col-lg-5 no-hor-padding pull-right">
										<?php
										if ($userDefaultAddress != $shippingAddresses->shippingaddressId) {
											?>
										<a href="javascript:void(0);"><img onclick="confirmModal('method', 'deleteShipping', '<?php echo $shippingAddresses->slug; ?>')" src="<?php echo Yii::app()->createAbsoluteUrl("/images/delete-icon.png");?>"></a>
										<?php } ?>
										<a data-toggle="modal" data-target="#address-modal" href="" onclick="edit_shipping(<?php echo $shippingAddresses->shippingaddressId; ?>)"><img src="<?php echo Yii::app()->createAbsoluteUrl("/images/edit-icon.png");?>"></a>
									</div>
									<div class="joysale-acc-addr col-xs-12 col-sm-12 no-hor-padding">
										<div class="joysale-acc-addr-heading"><?php echo $nicknameLabel; ?></div>
										<div class="joysale-acc-addr-txt">
											<span class="address-user-name"><?php echo $shippingAddresses->name; ?></span>
											<span class="joysale-acc-addr-line1">
												<?php echo $shippingAddresses->address1.$shippingAddresses->address2; ?>
											</span>
											<span class="joysale-acc-addr-line2">
												<?php echo $shippingAddresses->city." - ".$shippingAddresses->zipcode; ?>
											</span>
											<span class="joysale-acc-addr-city"><?php echo $shippingAddresses->state; ?></span>
											<span class="joysale-acc-addr-city"><?php echo $shippingAddresses->country; ?></span>
										</div>
									</div>
									<div class="joysale-acc-phone col-xs-12 col-sm-12 no-hor-padding">
										<?php echo $shippingAddresses->phone; ?>
									</div>
									<div class="joysale-acc-btn-cnt col-xs-12 col-sm-12 no-hor-padding">
										<div class="joysale-acc-select-cnt">
											<!--a href="javascript:void(0);" onclick="select_shipping(<?php echo $shippingAddresses->shippingaddressId; ?>);" class="joysale-acc-continue-btn joysale-acc-btn joysale-acc-select"><?php echo Yii::t('app','Continue'); ?></a-->
											<!--a href="javascript:void(0);" onclick="select_shipping(<?php echo $shippingAddresses->shippingaddressId; ?>);" class="joysale-acc-continue-btn joysale-acc-btn joysale-acc-select">Continue</a-->
											<a href="javascript:void(0);" onclick="select_shipping(<?php echo $shippingAddresses->shippingaddressId; ?>);"
												class="joysale-acc-continue-btn joysale-acc-btn joysale-acc-select" data-toggle="collapse"
												data-target="#collapse2" data-parent="#accordion"><?php echo Yii::t('app','Continue'); ?></a>
										</div>
									</div>
								</div>
							</div>
							<?php }?>
						</div>
					  <?php } ?>
					  </div>
				</div>
			  </div>
			  <div class="joysale-accordion panel panel-default">
				<div class="joysale-accordion-heading panel-heading">
				  <div class="joysale-accordion-title-cnt panel-title">
					<a class="joysale-accordion-fullwidth-head" data-toggle="collapse" data-parent="#accordion" href="#collapse2">
						<div class="joysale-accordion-title-txt">2. <?php echo Yii::t('app','Order Summary'); ?></div>
						<div class="joysale-accordion-right-txt pull-right">
							<?php echo Yii::t('app','Order Total'); ?> : <?php $totalcost = ($reviewOrder['price'] * $reviewOrder['cartquantity']) + $reviewOrder['shippingCost'];
							echo '<span id="totalcost">'.round($totalcost,2)."</span> <span id='itemcurrency'>".$reviewOrder['currency'].'</span>'; ?></div>
					</a>
				  </div>
				</div>
				<div id="collapse2" class="panel-collapse collapse">
				  <div class="panel-body">
				    <div class="col-xs-12 col-sm-12 no-hor-padding">
						<div class="joysale-acc-order-summary-pic-cnt col-xs-12 col-sm-3 col-md-2">
							<div class="joysale-acc-order-summary-pic" style="background-image: url('<?php echo Yii::app( )->getBaseUrl( ).
								"/item/products/resized/200/".$reviewOrder['itemId']."/".$reviewOrder['productimage']; ?>');"></div>
						</div>
						<div class="joysale-acc-order-summary-info-cnt col-xs-12 col-sm-9 col-md-10">
							<div class="joysale-acc-order-name col-xs-12 col-sm-12"><?php echo $reviewOrder['name'];?></div>
							<div class="col-xs-12 col-sm-12 no-hor-padding">
								<div class="joysale-acc-order-rate col-xs-12 col-sm-8 col-md-9 col-lg-10 no-hor-padding">
									<div class="joysale-acc-order-summary-txt">
										<span class="accordion-seller-txt"><?php echo Yii::t('app','Seller'); ?>:</span>
										<a class="userNameLink" href="<?php echo Yii::app()->createAbsoluteUrl('user/profiles',array(
											'id' => Myclass::safe_b64encode($reviewOrder['sellerId'].'-'.rand(0,999)))); ?>" title="_blank">
											<?php echo $reviewOrder['sellername']; ?>
										</a>
									</div>
								</div>
								<div class="joysale-acc-order-shp-fee-details joysale-acc-order-rate col-xs-12 col-sm-4 col-md-3 col-lg-2 no-hor-padding">
									<span class="joysale-acc-item-fee-txt"><?php echo Yii::t('app','Item fee'); ?>:</span>
									<span class="joysale-acc-order-shp-fee">
										<?php echo $reviewOrder['price'] * $reviewOrder['cartquantity']." ".$reviewOrder['currency']; ?>
									</span>
									<div class="joysale-acc-order-shp-fee-cnt">
										<?php echo Yii::t('app','Shipping fee'); ?> :
										<span class="joysale-acc-order-shp-fee">
											<?php echo $reviewOrder['shippingCost']." ".$reviewOrder['currency']; ?>
										</span>
									</div>
								</div>
							</div>
						</div>
				    </div>
					<div class="joysale-acc-order-btn-cnt col-xs-12 col-sm-12 no-hor-padding">
						<div class="joysale-acc-order-btn pull-right">
							<a href="javascript:void(0);" class="joysale-acc-btn" data-toggle="collapse" data-target="#collapse3"
								data-parent="#accordion"><?php echo Yii::t('app','Continue'); ?></a>
							<a href="<?php echo $_SERVER['HTTP_REFERER']?>" class="joysale-acc-delete-btn"><?php echo Yii::t('app','Cancel'); ?></a>
						</div>
					</div>
					<input type="hidden" class="review-order-seller-id" value="<?php echo $reviewOrder['sellerId']; ?>">
					<input type="hidden" class="review-order-product-id" value="<?php echo $reviewOrder['itemId']; ?>">
					<input type="hidden" class="sub-total-hidden" value="<?php echo $reviewOrder['price'] * $reviewOrder['cartquantity']; ?>">
					<input type="hidden" class="coupon-value-hidden" value="">
					<input type="hidden" class="coupon-type-hidden" value="">
					<input type="hidden" class="coupon-code-hidden" value="">
					<input type="hidden" class="coupon-max-hidden" value="">
					<input type="hidden" class="product-option-hidden" value="">
					<input type="hidden" class="product-quantity-hidden" value="<?php echo $reviewOrder['cartquantity']; ?>">
					<?php $userId = Yii::app()->user->id; ?>
					<input type="hidden" id="loguserid" value="<?php echo $userId;?>">

				  </div>
				</div>
			  </div>
			  <div class="joysale-accordion panel panel-default">
				<div class="joysale-accordion-heading panel-heading">
				  <div class="joysale-accordion-title-cnt panel-title">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
						<div class="joysale-accordion-title-txt joysale-accordion-title-pay">3. <?php echo Yii::t('app','Payment method'); ?></div>
					</a>
				  </div>
				</div>
				<div id="collapse3" class="panel-collapse collapse">
				  <div class="panel-body">
					<div class="joysale-acc-payment-cnt col-xs-12 no-hor-padding">

							<!--ul class="joysale-acc-tab-ul col-xs-12 col-sm-3 col-md-2 nav nav-tabs">
							  <li class="active col-xs-12 no-hor-padding"><a data-toggle="tab" href="#home">Paypal Account</a></li>
							  <li class="col-xs-12 no-hor-padding"><a data-toggle="tab" href="#menu1">Credit Card</a></li>
							</ul-->

						<div class="joysale-acc-tab-pane-cnt col-xs-12 col-sm-9 col-md-10 tab-content">
						  <div id="home" class="tab-pane fade in active">
							<div class="col-xs-12 col-sm-12">
								<div class="joysale-acc-payment-icon-cnt">
									<img src="<?php echo Yii::app()->createAbsoluteUrl('images/paypal-icon.png')?>"><br/>
								</div>
								<div class="joysale-acc-payment-btn-cnt">
									<a href="javascript:void(0);" class="joysale-acc-paypal-btn" onclick="checkout();"><?php echo Yii::t('app','Checkout with paypal'); ?></a>
								</div>
								<div id="payerr" class="errorMessage" align="center"></div>
								<div class="payment-form"></div>
							</div>
						  </div>

							  <!--div id="menu1" class="tab-pane fade">
								<div class="joysale-acc-cdtcard-cnt col-xs-12 col-sm-12 no-hor-padding">
									<div class="joysale-acc-pane-heading">Pay with</div>
									<div class="joysale-acc-pane-content">

										<div class="joysale-acc-pane-left-cnt col-xs-12 col-sm-6 no-hor-padding">
											<form method="post" action="<?php echo Yii::app()->createAbsoluteUrl('/buynow/checkout/braintreepayment'); ?>" id="paymentForm">
											<div class="Category-input-box-row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
												<input class="col-xs-12 col-sm-12 col-md-11 col-lg-10 no-hor-padding" type="text" name="card_name" id="card_name" placeholder="Your Card Name"/>
											</div>
											<div class="Category-input-box-row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
												<input class="col-xs-12 col-sm-12 col-md-11 col-lg-10 no-hor-padding" type="text" name="card_number" id="card_number"  maxlength="19" placeholder="1234 5678 9012 3456"/>
											</div>
											<div class="Category-input-box-row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
												<input class="col-xs-12 col-sm-12 col-md-11 col-lg-5 no-hor-padding" type="text" name="expiry_month" id="expiry_month" maxlength="2" placeholder="MM" />
												<input class="col-xs-12 col-sm-12 col-md-11 col-lg-5 no-hor-padding" type="text" name="expiry_year" id="expiry_year" maxlength="2" placeholder="YY" />
											</div>
											<div class="Category-input-box-row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
												<input class="cvv-num-div cvv-num col-xs-12 col-sm-12 col-md-4 col-lg-3 no-hor-padding" type="text" name="cvv" id="cvv" maxlength="3" placeholder="123" />
												<input class="card-num col-xs-12 col-sm-12 col-md-7 col-lg-6 no-hor-padding" type="text" placeholder="Card Expiry date" name="fname">
											</div>
											<div class="joysale-acc-pane-btn col-xs-12 col-sm-12 no-hor-padding">
												<input type="button" class="joysale-acc-btn" value="Pay Now" onclick="braintree_payment();">
											</div>
											</form>
										</div>

										<div class="joysale-acc-pane-right-cnt col-xs-12 col-sm-6 no-hor-padding">
											<div class="sheild-icon"></div>
											<div class="sheild-icon-txt">100% safe and secure payments</div>
										</div>
									</div>
								</div>
							  </div-->

						</div>
					</div>
				  </div>
				</div>




			  </div>
			</div>

	</div>

</div>

<!--Address details popup-->

	<div class="modal fade" id="address-modal" role="dialog">
		<div class="modal-dialog modal-dialog-width">
			<div class="login-modal-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
					<div class="login-modal-header col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
							<h2 class="login-header-text"><?php echo Yii::t('app','Address details');?></h2>
							<button data-dismiss="modal" class="close login-close" type="button">×</button>
					</div>

						<div class="login-line col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"></div>

							<div class="login-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding ">
								<div class="login-box col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">


	<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'tempaddresses-addshipping-form',
    'action'=>Yii::app()->createUrl('/checkout/addshipping'),
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// See class documentation of CActiveForm for details on this,
	// you need to use the performAjaxValidation()-method described there.
 //   'enableAjaxValidation'=>true,
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
		'validateOnChange'=>false,
	),
	'htmlOptions' => array('onsubmit' => 'return validaddship()'),
	)); ?>
	<div class="login-text-box col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

		<input type="hidden" id="shippingId" name="shippingId" value="">

		<label class="Category-input-box-heading col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Nickname'); ?><span class="required">*</span></label>
		<input type="text" maxlength="20" id="Tempaddresses_nickname" name="Tempaddresses[nickname]" onkeypress="return validateNumeric(this,event)" class="popup-input">
		<div style="display:none" id="Tempaddresses_nickname_em_" class="errorMessage"></div>

		<label class="Category-input-box-heading col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Name'); ?><span class="required">*</span></label>
		<input type="text" maxlength="30" id="Tempaddresses_name" name="Tempaddresses[name]" onkeypress="return validateNumeric(this,event)" class="form-control">
		<div style="display:none" id="Tempaddresses_name_em_" class="errorMessage"></div>

		<label class="Category-input-box-heading col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Country'); ?><span class="required">*</span></label>
		<!--select id="Tempaddresses_country" class="form-control" name="Tempaddresses[country]">
			<?php
			foreach ($countriesList as $key => $value) {
				echo '<option value="'.$key.'">'.$value.'</option>';
			}
			?>
		</select-->
		<input type="text" value="<?php echo $countrydata->country;?>" disabled>
		<input type="hidden" name="Tempaddresses[country]" id="Tempaddresses_country" value="<?php echo $countrydata->countryId."-".Yii::t('app',$countrydata->country);?>">
		<div style="display:none" id="Tempaddresses_country_em_" class="errorMessage"></div>

		<label class="Category-input-box-heading col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Address 1'); ?><span class="required">*</span></label>
		<input type="text" maxlength="60" id="Tempaddresses_address1" name="Tempaddresses[address1]" class="form-control">
		<div style="display:none" id="Tempaddresses_address1_em_" class="errorMessage"></div>

		<label class="Category-input-box-heading col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Address 2'); ?></label>
		<input type="text" maxlength="60" id="Tempaddresses_address2" name="Tempaddresses[address2]" class="form-control">
		<div style="display:none" id="Tempaddresses_address2_em_" class="errorMessage"></div>

		<label class="Category-input-box-heading col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','City'); ?><span class="required">*</span></label>
		<input type="text" maxlength="40" id="Tempaddresses_city" name="Tempaddresses[city]" onkeypress="return validateNumeric(this,event)" class="form-control">
		<div style="display:none" id="Tempaddresses_city_em_" class="errorMessage"></div>

		<label class="Category-input-box-heading col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','State'); ?><span class="required">*</span></label>
		<input type="text" maxlength="40" id="Tempaddresses_state" name="Tempaddresses[state]" onkeypress="return validateNumeric(this,event)" class="form-control">
		<div style="display:none" id="Tempaddresses_state_em_" class="errorMessage"></div>

		<label class="Category-input-box-heading col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Zipcode'); ?><span class="required">*</span></label>
		<input type="text" maxlength="20" id="Tempaddresses_zipcode" name="Tempaddresses[zipcode]" onkeypress="return IsAlphaNumeric(event)" class="form-control">
		<div style="display:none" id="Tempaddresses_zipcode_em_" class="errorMessage"></div>

		<label class="Category-input-box-heading col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Phone'); ?><span class="required">*</span></label>
		<input type="text" maxlength="20" id="Tempaddresses_phone" name="Tempaddresses[phone]" class="form-control">
		<div style="display:none" id="Tempaddresses_phone_em_" class="errorMessage"></div>

		<input type="submit" value="<?php echo Yii::t('app','Save address'); ?>" name="yt0" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding login-btn">
</div>
	<?php $this->endWidget(); ?>



								</div>

							</div>





			</div>
		</div>
	</div>

<!--E O address details popup-->

</div>

<style>
.option-error {
	background-color: #FFFFFF;
	color: #DF2525;
	display: none;
	padding: 4px;
	position: absolute;
	z-index: 20;
	margin-top: 0;
}
.coupon-container button
{
	width:22%;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
<script type="text/javascript">
$(function () {
    $('#accordion').on('shown.bs.collapse', function (e) {
		$(window).scrollTop(0);
    	});
});
</script>