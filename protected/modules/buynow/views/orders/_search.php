<?php
/* @var $this OrdersController */
/* @var $model Orders */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'orderId'); ?>
		<?php echo $form->textField($model,'orderId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'userId'); ?>
		<?php echo $form->textField($model,'userId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'sellerId'); ?>
		<?php echo $form->textField($model,'sellerId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'totalCost'); ?>
		<?php echo $form->textField($model,'totalCost',array('size'=>18,'maxlength'=>18)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'totalShipping'); ?>
		<?php echo $form->textField($model,'totalShipping',array('size'=>7,'maxlength'=>7)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'orderDate'); ?>
		<?php echo $form->textField($model,'orderDate'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'shippingAddress'); ?>
		<?php echo $form->textField($model,'shippingAddress'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'currency'); ?>
		<?php echo $form->textField($model,'currency',array('size'=>3,'maxlength'=>3)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'status'); ?>
		<?php echo $form->textField($model,'status',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->