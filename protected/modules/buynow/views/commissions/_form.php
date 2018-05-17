<?php
/* @var $this CommissionsController */
/* @var $model Commissions */
/* @var $form CActiveForm */
?>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
			<?php echo Yii::t('admin','Commission setup'); ?>
			</h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
				<?php echo Yii::t('admin','Add').' '.Yii::t('admin','Commission'); ?>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">
							<div class="form">

							<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'commissions-form',
							// Please note: When you enable ajax validation, make sure the corresponding
							// controller action is handling ajax validation correctly.
							// There is a call to performAjaxValidation() commented in generated controller code.
							// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
							     'htmlOptions' => array('onsubmit'=> 'return validateCommission()'),

							)); ?>

								<p class="note">
								<?php echo Yii::t('admin' , 'Fields with'); ?>
									<span class="required"> * </span>
									<?php echo Yii::t('admin', 'are required.'); ?>
								</p>


								<div class="form-group">
								<?php echo $form->labelEx($model,'percentage'); ?>
								<?php echo $form->textField($model,'percentage',array('id'=>'commission','class'=>'form-control','maxlength'=>3,'onkeypress' => 'return isNumber(event)')); ?>
								<?php echo $form->error($model,'percentage'); ?>
									<div id="commission-error" class="errorMessage"></div>
								</div>

								<div class="form-group">
								<?php echo $form->labelEx($model,'minRate'); ?>
								<?php echo $form->textField($model,'minRate',array('id'=>'minrange','class'=>'form-control','maxlength'=>9,'onkeypress' => 'return isNumber(event)')); ?>
								<?php echo $form->error($model,'minRate'); ?>
									<div id="minError" class="errorMessage"></div>
								</div>

								<div class="form-group">
								<?php echo $form->labelEx($model,'maxRate'); ?>
								<?php echo $form->textField($model,'maxRate',array('id'=>'maxrange','class'=>'form-control','maxlength'=>9,'onkeyup' => 'return isNumber(event)')); ?>
								<?php echo $form->error($model,'maxRate'); ?>
									<div id="maxError" class="errorMessage"></div>
								</div>

								<div class="btn-block">
								<?php echo CHtml::submitButton($model->isNewRecord ?  Yii::t('admin','Create') : Yii::t('admin','Save'),array('class' => 'btn btn-success')); ?>
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

