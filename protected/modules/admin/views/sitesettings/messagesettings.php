<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
			<?php echo Yii::t('admin','Mobile SMS').' '.Yii::t('admin','Settings'); ?>
			</h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
				<?php echo Yii::t('admin','SMS').' '.Yii::t('admin','Settings'); ?>
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
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,	
	    'clientOptions' => array(
	         'validateOnSubmit'=>true,
	         'validateOnChange'=>false,
							)
							)); ?>

								<!--<p class="note">
								<?php echo Yii::t('admin' , 'Fields with'); ?>
									<span class="required"> * </span>
									<?php echo Yii::t('admin', 'are required.'); ?>
								</p>

								<div class="checkbox checkbox-custom">
								
								<?php echo $form->checkBox($model,'smtpEnable', array('value'=>1, 'uncheckValue'=>0)); ?>
								<?php echo $form->labelEx($model,'smtpEnable'); ?>
								</div>-->

								<div class="form-group">
								<?php echo $form->labelEx($model,'account_sid'); ?>
								<?php echo $form->textField($model,'account_sid',array('class' => 'form-control')); ?>
								<?php echo $form->error($model,'account_sid'); ?>
								</div>

								<div class="form-group">
								<?php echo $form->labelEx($model,'auth_token'); ?>
								<?php echo $form->textField($model,'auth_token',array('class' => 'form-control')); ?>
								<?php echo $form->error($model,'auth_token'); ?>
								</div>

								<div class="form-group">
								<?php echo $form->labelEx($model,'sms_number'); ?>
								<?php echo $form->textField($model,'sms_number',array('class' => 'form-control')); ?>
								<?php echo $form->error($model,'sms_number'); ?>
								</div>

								

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
