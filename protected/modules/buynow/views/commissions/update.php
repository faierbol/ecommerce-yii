<?php
/* @var $this CommissionsController */
/* @var $model Commissions */

$this->breadcrumbs=array(
	'Commissions'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Commissions', 'url'=>array('index')),
	array('label'=>'Create Commissions', 'url'=>array('create')),
	array('label'=>'View Commissions', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Commissions', 'url'=>array('admin')),
);
?>


<?php $this->renderPartial('_form', array('model'=>$model)); ?>