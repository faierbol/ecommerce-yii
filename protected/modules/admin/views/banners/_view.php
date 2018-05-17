<?php
/* @var $this BannersController */
/* @var $data Banners */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('bannerimage')); ?>:</b>
	<?php echo CHtml::encode($data->bannerimage); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('appbannerimage')); ?>:</b>
	<?php echo CHtml::encode($data->appbannerimage); ?>
	<br />	

	<b><?php echo CHtml::encode($data->getAttributeLabel('bannerurl')); ?>:</b>
	<?php echo CHtml::encode($data->bannerurl); ?>
	<br />


</div>