<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo Yii::t('admin','Update').' '.Yii::t('admin','Users'); ?></h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php echo Yii::t('admin','Update').' '.Yii::t('admin','User'); ?>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">
	<?php $this->renderPartial('_form', array('model'=>$model, 'type'=>"update")); ?>
						</div>
					</div><!-- /.row (nested) -->
				</div><!-- /.panel-body -->
			</div><!-- /.panel -->
		</div><!-- /.col-lg-12 -->
	</div><!-- /.row -->
</div><!-- /#page-wrapper -->