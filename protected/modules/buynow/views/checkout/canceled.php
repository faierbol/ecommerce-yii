<div class="slider container container-1 section_container">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			  <div class="row product_align_cnt">
				<div class="display-flex modal-dialog modal-dialog-width">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
							<div class="payment-decline-status-info-txt">
								<?php echo Yii::t('app','Your payment has been');?> <span class="payment-red"><?php echo Yii::t('app','declined'); ?>.</span></div>
							<div class="cls_continue-shop col-lg-12 no-hor-padding">
								<a class="payment-promote-btn login-btn" href="<?php echo Yii::app()->createAbsoluteUrl('/'); ?>">
									<?php echo Yii::t('app','Continue Shopping'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			  </div>
		</div>
	</div>
</div>