<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo Yii::t('admin','View').' '.Yii::t('admin','Category'); ?></h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo Yii::t('admin','Category').' '.Yii::t('admin','Details'); ?></div>
				<!-- /.panel-heading -->
				<div class="panel-body">
				<div class="table-responsive">
				<?php
				/* @var $this CategoriesController */
				/* @var $model Categories */

				$this->breadcrumbs=array(
	'Categories'=>array('index'),
				$model->name,
				);

				$this->menu=array(
				array('label'=>'List Categories', 'url'=>array('index')),
				array('label'=>'Create Categories', 'url'=>array('create')),
				array('label'=>'Update Categories', 'url'=>array('update', 'id'=>$model->categoryId)),
				array('label'=>'Delete Categories', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->categoryId),'confirm'=>'Are you sure you want to delete this Category?')),
				array('label'=>'Manage Categories', 'url'=>array('admin')),
				);
				?>
				<?php
	$sitepaymentmodes = Myclass::getSitePaymentModes();
	if($sitepaymentmodes['buynowPaymentMode'] == "1")
	{
		$visible = true;
	}
	else
	{
		$visible = false;
	}
	?>


				<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'htmlOptions' => array('class' => 'table table-striped table-bordered table-hover'),
	'attributes'=>array(
		'categoryId',
		'name',
		array(
            'label'=>Yii::t('admin','Parent Category'),
            'type'=>'raw',
            'value'=>$model->catName,
        ),
		array(
            'label'=>Yii::t('admin','Created Date'),
            'type'=>'raw',
            'value'=>$model->modDate,
        ),
		array(
				'label'=>Yii::t('admin','Item Condition'),
				'type'=>'raw',
				'value'=>function ($model)
				{
					if($model->parentCategory == 0)
					{
						return $model->itemCondition;
					}
					else
					{
						$categoryproperty = $model->getCategoryproperty();
						$properties = json_decode($categoryproperty,true);
						return ucfirst($properties['itemCondition']);
					}
				}
		),
		array(
				'label'=>Yii::t('admin','My Offer'),
				'type'=>'raw',
				'value'=>function ($model)
				{
					if($model->parentCategory == 0)
					{
						return $model->myOffer;
					}
					else
					{
						$categoryproperty = $model->getCategoryproperty();
						$properties = json_decode($categoryproperty,true);
						return ucfirst($properties['myOffer']);
					}
				}
		),
		array(
				'label'=>Yii::t('admin','Exchange Buy'),
				'type'=>'raw',
				'value'=>function ($model)
				{
					if($model->parentCategory == 0)
					{
						return $model->exchangetoBuy;
					}
					else
					{
						$categoryproperty = $model->getCategoryproperty();
						$properties = json_decode($categoryproperty,true);
						return ucfirst($properties['exchangetoBuy']);
					}
				}
		),
		array(
				'label'=>Yii::t('admin','Buy Now'),
				'type'=>'raw',
				'visible' => $visible,
				'value'=>function ($model)
				{
					if($model->parentCategory == 0)
					{
						return $model->buyNow;
					}
					else
					{
						$categoryproperty = $model->getCategoryproperty();
						$properties = json_decode($categoryproperty,true);
						return ucfirst($properties['buyNow']);
					}
				}
		),
		/*array(
				'label'=>Yii::t('admin','Contact Seller'),
				'type'=>'raw',
				'value'=>$model->contactSeller,
		)*/
	),
)); ?>

</div>
				</div>
				</div>
				</div>
				</div>
