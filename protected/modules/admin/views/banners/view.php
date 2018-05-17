<?php
/* @var $this BannersController */
/* @var $model Banners */

?>


<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo Yii::t('admin','View').' '.Yii::t('admin','Banner'); ?></h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo Yii::t('admin','Banner').' '.Yii::t('admin','Details'); ?></div>
				<!-- /.panel-heading -->
				<div class="panel-body">
				<div class="table-responsive">
				<?php
/* @var $this CurrenciesController */
/* @var $model Currencies */

$this->breadcrumbs=array(
	'Banners'=>array('index'),
	$model->bannerimage,
);

$this->menu=array(
	array('label'=>'List Banners', 'url'=>array('index')),
	array('label'=>'Create Banner', 'url'=>array('create')),
	array('label'=>'Update Banner', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Banner', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Banners', 'url'=>array('admin')),
);
?>


<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'htmlOptions' => array('class' => 'table table-striped table-bordered table-hover'),
	'attributes'=>array(
		'id',
		'bannerimage',
		'appbannerimage',
		'bannerurl',
	),
)); ?>
</div>
				</div>
				</div>
				</div>
				</div>