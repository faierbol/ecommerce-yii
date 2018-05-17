<?php
/* @var $this CommissionsController */
/* @var $model Commissions */

$this->breadcrumbs=array(
	'Commissions'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Commissions', 'url'=>array('index')),
	array('label'=>'Create Commissions', 'url'=>array('create')),
	array('label'=>'Update Commissions', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Commissions', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Commissions', 'url'=>array('admin')),
);
?>

<h1><?php echo Yii::t('app','View Commissions #'); ?><?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'percentage',
		'minRate',
		'maxRate',
		'status',
		'date',
	),
)); ?>
