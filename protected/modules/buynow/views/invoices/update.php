<?php
/* @var $this OrdersController */
/* @var $model Orders */

$this->breadcrumbs=array(
	'Orders'=>array('index'),
	$model->orderId=>array('view','id'=>$model->orderId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Orders', 'url'=>array('index')),
	array('label'=>'Create Orders', 'url'=>array('create')),
	array('label'=>'View Orders', 'url'=>array('view', 'id'=>$model->orderId)),
	array('label'=>'Manage Orders', 'url'=>array('admin')),
);
?>

<h1> <?php echo Yii::t('app','Update Orders'); ?><?php echo $model->orderId; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>