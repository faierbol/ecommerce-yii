<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo Yii::t('admin','Account').' '.Yii::t('admin','Settings'); ?></h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo Yii::t('admin','Site Transaction Settings'); ?></div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">

						<?php
						/* @var $this SitesettingsController */
						/* @var $model Sitesettings */
						/* @var $form CActiveForm */
						?>

							<div class="form">

							<?php
							$form=$this->beginWidget('CActiveForm', array(
	'id'=>'sitesettings-form',
							// Please note: When you enable ajax validation, make sure the corresponding
							// controller action is handling ajax validation correctly.
							// There is a call to performAjaxValidation() commented in generated controller code.
							// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
	'clientOptions'=>array(
		'validateOnSubmit'=>false,
		'validateOnChange' => false,
		),
							)); ?>
								<div class="form-group">
								<?php echo $form->labelEx($model,'Exchange Transaction Mode');
								$sitepaymentmodes = json_decode($model->sitepaymentmodes,true);
								$model->exchangePaymentMode = $sitepaymentmodes['exchangePaymentMode'];
								 ?>
									<br>
									<div class="radio radio-custom">
										<?php echo $form->radioButtonList($model,'exchangePaymentMode',array('0'=>'Disable','1'=>'Enable'),
												array('value' => $model->exchangePaymentMode,'style'=>'display:inline')); ?>
									</div>
									<?php echo $form->error($model,'exchangePaymentMode'); ?>
								</div>

								<div class="form-group">
								<?php echo $form->labelEx($settings,'Site Promotion Module'); ?>
									<br>
									<div class="radio radio-custom">
										<?php echo $form->radioButtonList($settings,'promotionStatus',array('0'=>'Disable','1'=>'Enable'),
												array('value' => $settings->promotionStatus,'style'=>'display:inline')); ?>
									</div>
									<?php echo $form->error($settings,'promotionStatus'); ?>
								</div>





								<div class="form-group">
								<?php echo CHtml::submitButton($settings->isNewRecord ? Yii::t('admin','Save') : Yii::t('admin','Save'),array('class' => 'btn btn-success')); ?>
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
