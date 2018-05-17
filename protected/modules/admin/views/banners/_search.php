<?php
/* @var $this BannersController */
/* @var $model Banners */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'bannerimage'); ?>
		<?php echo $form->textField($model,'bannerimage',array('size'=>60,'maxlength'=>60)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'appbannerimage'); ?>
		<?php echo $form->textField($model,'appbannerimage',array('size'=>60,'maxlength'=>60)); ?>
	</div>	

	<div class="row">
		<?php echo $form->label($model,'bannerurl'); ?>
		<?php echo $form->textArea($model,'bannerurl',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->