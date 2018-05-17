<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
			<?php echo Yii::t('admin','Adsense').' '.Yii::t('admin','Settings'); ?>
			</h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
				<?php echo Yii::t('admin','Adsense').' '.Yii::t('admin','Settings'); ?>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">

						<?php
						/* @var $this SitesettingsController */
						/* @var $model Sitesettings */
						/* @var $form CActiveForm */
						?>

							<div class="form">

							<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'sitesettings-form',
							// Please note: When you enable ajax validation, make sure the corresponding
							// controller action is handling ajax validation correctly.
							// There is a call to performAjaxValidation() commented in generated controller code.
							// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>true,
	'enableClientValidation'=>true,
	    'clientOptions' => array(
	         'validateOnSubmit'=>true,
	         'validateOnChange'=>true,
							)
							)); ?>

								<p class="note">

								
								<?php echo $form->labelEx($model,'<u>Google Ads Footer</u>'); ?>
								<div class="radio radio-custom">
									<?php echo $form->radioButtonList($model,'google_ads_footer',array('1'=>'Enable','0'=>'Disable'), 
											array('value' => $model->google_ads_footer,'style'=>'display:inline')); ?>
									<?php echo $form->error($model,'google_ads_footer'); ?>
								</div>

								<div class="form-group">
								<?php echo $form->labelEx($model,'google Ads client'); ?>
								<?php echo $form->textField($model,'google_ad_client_footer',array('class' => 'form-control')); ?>
								<?php echo $form->error($model,'google_ad_client_footer'); ?>
								</div>

								<div class="form-group">
								<?php echo $form->labelEx($model,'googleAdslot'); ?>
								<?php echo $form->textField($model,'google_ad_slot_footer',array('class' => 'form-control')); ?>
								<?php echo $form->error($model,'google_ad_slot_footer'); ?>
								</div>


								<?php echo $form->labelEx($model,'<u>Google Ads Profile</u>'); ?>
								<div class="radio radio-custom">
									<?php echo $form->radioButtonList($model,'google_ads_profile',array('1'=>'Enable','0'=>'Disable'), 
											array('value' => $model->google_ads_profile,'style'=>'display:inline')); ?>
									<?php echo $form->error($model,'google_ads_profile'); ?>
								</div>

								<div class="form-group">
								<?php echo $form->labelEx($model,'google Ads client'); ?>
								<?php echo $form->textField($model,'google_ad_client_profile',array('class' => 'form-control')); ?>
								<?php echo $form->error($model,'google_ad_client_profile'); ?>
								</div>

								<div class="form-group">
								<?php echo $form->labelEx($model,'googleAdslot'); ?>
								<?php echo $form->textField($model,'google_ad_slot_profile',array('class' => 'form-control')); ?>
								<?php echo $form->error($model,'google_ad_slot_profile'); ?>
								</div>

								<?php echo $form->labelEx($model,'<u>Google Ads Product</u>'); ?>
								<div class="radio radio-custom">
									<?php echo $form->radioButtonList($model,'google_ads_product',array('1'=>'Enable','0'=>'Disable'), 
											array('value' => $model->google_ads_product,'style'=>'display:inline')); ?>
									<?php echo $form->error($model,'google_ads_product'); ?>
								</div>

								<div class="form-group">
								<?php echo $form->labelEx($model,'google Ads client'); ?>
								<?php echo $form->textField($model,'google_ad_client_product',array('class' => 'form-control')); ?>
								<?php echo $form->error($model,'google_ad_client_product'); ?>
								</div>

								<div class="form-group">
								<?php echo $form->labelEx($model,'googleAdslot'); ?>
								<?php echo $form->textField($model,'google_ad_slot_product',array('class' => 'form-control')); ?>
								<?php echo $form->error($model,'google_ad_slot_product'); ?>
								</div>


								<?php echo $form->labelEx($model,'<u>Google Ads Product Right</u>'); ?>
								<div class="radio radio-custom">
									<?php echo $form->radioButtonList($model,'google_ads_productright',array('1'=>'Enable','0'=>'Disable'), 
											array('value' => $model->google_ads_productright,'style'=>'display:inline')); ?>
									<?php echo $form->error($model,'google_ads_productright'); ?>
								</div>

								<div class="form-group">
								<?php echo $form->labelEx($model,'google Ads client'); ?>
								<?php echo $form->textField($model,'google_ad_client_productright',array('class' => 'form-control')); ?>
								<?php echo $form->error($model,'google_ad_client_productright'); ?>
								</div>

								<div class="form-group">
								<?php echo $form->labelEx($model,'googleAdslot'); ?>
								<?php echo $form->textField($model,'google_ad_slot_productright',array('class' => 'form-control')); ?>
								<?php echo $form->error($model,'google_ad_slot_productright'); ?>
								</div>


								<p><b>Note:</b> <span style="color:red;">Google adsense setting has vertical ads size are 336x280 & 280x320 and Horizantal ads are size 780x90 & 1130x100 only</span>.</p>
								<br/>


								<div class="form-group">
								<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('admin','Save') : Yii::t('admin','Save'),array('class' => 'btn btn-success')); ?>
								</div>

								<?php $this->endWidget(); ?>

							</div>
							<!-- form -->
						</div>
					</div>
					<!-- /.row (nested) -->
				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
</div>
<!-- /#page-wrapper -->
