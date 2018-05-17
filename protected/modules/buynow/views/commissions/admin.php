<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">

		<?php echo CHtml::link('<i class="fa fa-plus"></i> Add Commission',Yii::app()->createAbsoluteUrl('admin/commissions/create'),array('class' => 'btn btn-success','style' => 'float:right;  margin-top:43px')); ?>
			<h1 class="page-header"><?php echo Yii::t('admin','Manage Commisions'); ?></h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php echo Yii::t('admin','Commission Setup'); ?>
					<?php if($commissionSetting == 1) {
						echo CHtml::link('<label class="label label-bg label-success pull-right"  style="cursor:pointer;font-size:15px;line-height:18px;">Enabled</label>',Yii::app()->createAbsoluteUrl('admin/commissions/status'));
					} else { ?>
					<?php echo CHtml::link('<label class="label label-bg label-danger pull-right"  style="cursor:pointer;font-size:15px;line-height:18px;">Disabled</label>',Yii::app()->createAbsoluteUrl('admin/commissions/status'));}?>
				</div>
				<!-- /.panel-heading -->
				<div class="panel-body">

				<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'commissions-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'itemsCssClass' => 'table table-striped table-bordered table-hover',
	'columns'=>array(
		'id',
		'percentage',
		'minRate',
		'maxRate',

				array(
			'class'=>'CButtonColumn',
				'header' => 'Status',
			'template' => '{status}',
				'buttons' => array(
				'status' => array('label'=> 'Status',
				 'options' => array('rel' => 'tooltip', 'data-toggle' => 'tooltip', 'title' => 'Status'),
                'label' => ($model->status == 1) ? '<i class="fa fa fa-check-circle" style="color:green; font-size:20px;"></i>' : '<i class="fa fa fa-times-circle" style="color:red; font-size:20px;"></i>' ,
                'imageUrl' => false,
				'url' => 'Yii::app()->createAbsoluteUrl("admin/commissions/changeStatus",array("id"=>"'.$model->status.'."))',

				),
				),
				),
				//'date',
				array(
			'class'=>'CButtonColumn',
			'header' => 'Action',
				),
				),
				));
				?>
				</div>
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
</div>
