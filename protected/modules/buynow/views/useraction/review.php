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
				<li><a href="#"><?php echo Yii::t('app','Rating & Review'); ?></a></li>					 
			 </ol>			
		</div>	
	</div>
	<div class="row">
		<div class="profile-vertical-tab-section col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<?php $this->renderPartial('application.modules.useractivity.views.useraction.profilemenu',array('user'=>$user, 
				 			'followerIds'=>$followerIds)); ?> 

			<div class="tab-content col-xs-12 col-sm-9 col-md-9 col-lg-9">
				<div id="user-review" class="profile-tab-content tab-pane fade col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding active in">				
					<div class="profile-tab-content-heading col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
						<span class="user-name-review"><?php echo ucfirst($user->name);?></span><?php echo Yii::t('app','review'); ?>
					</div>
						
					<?php if(!empty($reviewsModel)){ ?>
						<!----------------------Review------------------->
					<div class="recent-activities-tab-content tab-content">
						<div id="my-review" class="tab-pane fade in active">
							<div class="profile-listing-product-cnt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
								<!--row 1-->
								<?php foreach ($reviewsModel as $review){ ?>
								<div class="exchange-row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">												
									<div class="exchange-content-cnt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
										<div class="exchange-left-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
											<div class="prof-pic-container col-xs-12 col-sm-3 col-md-4 col-lg-2 no-hor-padding">
												<a href="<?php echo Yii::app()->createAbsoluteUrl('user/profiles',array('id'=>Myclass::safe_b64encode($review['user']->userId.'-'.rand(0,999)))) ?>">
													<div class="exchange-prof-pic" id="user-pic-2" style="background-image:url('<?php if(!empty($review['user']->userImage)) {  echo Yii::app()->createAbsoluteUrl('user/resized/150/'.$review['user']->userImage); }else{ echo Yii::app()->createAbsoluteUrl('user/resized/150/default/'.Myclass::getDefaultUser()); }?>')"></div>
												</a>															
											</div>
											<div class="reviewer-content col-xs-12 col-sm-7 col-md-7 col-lg-10 no-hor-padding ">
													
												<a href="<?php echo Yii::app()->createAbsoluteUrl('user/profiles',array('id'=>Myclass::safe_b64encode($review['user']->userId.'-'.rand(0,999)))) ?>">
													<div class="reviwer-name bold col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
														<?php echo $review['user']->name; ?>
													</div>
												</a>
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
													<?php
													$orderRatting = $review->rating;
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
												<a href="javascript:void(0);">
													<div class="g-color col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
														<span class="exchange-place">
															<?php echo $review['user']->name." ".Yii::t('app','has been bought')." ".$review['orders']['orderitems'][0]->itemName; ?>
														</span>
													</div>
												</a>
																								
											</div>
											<div class="review-content col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding ">
												<div class="review-content-bg">
													<div class="bold review-content-heading"><?php echo $review->reviewTitle ?></div>
													<div class="review-content-description"><?php echo $review->review ?></div>
														<div class="review-date"><span><?php echo Yii::t('app','on'); ?></span> <?php echo date('dS M Y', $review->createdDate); ?></div>
													</div>
														
											</div>
										</div>													
									</div>
								</div>
								<?php } ?>
							</div>
						</div>					
														
					</div>
							
					<?php
					}else{
						echo '<div style="margin: 8% auto 0;" class="payment-decline-status-info-txt">
												<img src="'.Yii::app()->createAbsoluteUrl('images/empty-tap.jpg').'"><br>
												<span class="payment-red">'.Yii::t('app','Sorry...').'</span> 
												'.Yii::t('app','No Reviews Yet').'</div>';
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