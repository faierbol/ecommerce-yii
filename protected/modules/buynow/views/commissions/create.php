<?php
/* @var $this CommissionsController */
/* @var $model Commissions */

$this->breadcrumbs=array(
	'Commissions'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Commissions', 'url'=>array('index')),
	array('label'=>'Manage Commissions', 'url'=>array('admin')),
);
?>


<?php $this->renderPartial('_form', array('model'=>$model)); ?>