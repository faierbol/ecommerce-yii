<?php
/* @var $this CommissionsController */
/* @var $data Commissions */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('percentage')); ?>:</b>
	<?php echo CHtml::encode($data->percentage); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('minRate')); ?>:</b>
	<?php echo CHtml::encode($data->minRate); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('maxRate')); ?>:</b>
	<?php echo CHtml::encode($data->maxRate); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('date')); ?>:</b>
	<?php echo CHtml::encode($data->date); ?>
	<br />


</div>