<?php
/* @var $this OrdersController */
/* @var $model Orders */
/* @var $form CActiveForm */
?>

<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo Yii::t('app','Commission Setup'); ?></h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo Yii::t('app','Add Commission'); ?></div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">
							<div class="form">

							<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'orders-form',
							// Please note: When you enable ajax validation, make sure the corresponding
							// controller action is handling ajax validation correctly.
							// There is a call to performAjaxValidation() commented in generated controller code.
							// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
							)); ?>

								<p class="note">
									<?php echo Yii::t('app','Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app','are required.'); ?>
								</p>

								<?php echo $form->errorSummary($model); ?>

								<div class="row">
								<?php echo $form->labelEx($model,'userId'); ?>
								<?php echo $form->textField($model,'userId'); ?>
								<?php echo $form->error($model,'userId'); ?>
								</div>

								<div class="row">
								<?php echo $form->labelEx($model,'sellerId'); ?>
								<?php echo $form->textField($model,'sellerId'); ?>
								<?php echo $form->error($model,'sellerId'); ?>
								</div>

								<div class="row">
								<?php echo $form->labelEx($model,'totalCost'); ?>
								<?php echo $form->textField($model,'totalCost',array('size'=>18,'maxlength'=>18)); ?>
								<?php echo $form->error($model,'totalCost'); ?>
								</div>

								<div class="row">
								<?php echo $form->labelEx($model,'totalShipping'); ?>
								<?php echo $form->textField($model,'totalShipping',array('size'=>7,'maxlength'=>7)); ?>
								<?php echo $form->error($model,'totalShipping'); ?>
								</div>

								<div class="row">
								<?php echo $form->labelEx($model,'orderDate'); ?>
								<?php echo $form->textField($model,'orderDate'); ?>
								<?php echo $form->error($model,'orderDate'); ?>
								</div>

								<div class="row">
								<?php echo $form->labelEx($model,'shippingAddress'); ?>
								<?php echo $form->textField($model,'shippingAddress'); ?>
								<?php echo $form->error($model,'shippingAddress'); ?>
								</div>

								<div class="row">
								<?php echo $form->labelEx($model,'currency'); ?>
								<?php echo $form->textField($model,'currency',array('size'=>3,'maxlength'=>3)); ?>
								<?php echo $form->error($model,'currency'); ?>
								</div>

								<div class="row">
								<?php echo $form->labelEx($model,'status'); ?>
								<?php echo $form->textField($model,'status',array('size'=>20,'maxlength'=>20)); ?>
								<?php echo $form->error($model,'status'); ?>
								</div>

								<div class="row buttons">
								<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
								</div>

								<?php $this->endWidget(); ?>

							</div>

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

<!-- form -->
