<?php
/* @var $this PromotionController */
/* @var $model Promotions */


?>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo Yii::t('admin','Add').' '.Yii::t('admin','Promotion'); ?> </h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php echo Yii::t('admin','Add').' '.Yii::t('admin','Promotion'); ?>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">
						
						<?php $placeholder = explode("-",$siteSettings); ?>
	              <?php $this->renderPartial('_form', array('model'=>$model, 'type'=>"create",'placeholders'=>$placeholder[1])); ?>
		</div>
					</div><!-- /.row (nested) -->
				</div><!-- /.panel-body -->
			</div><!-- /.panel -->
		</div><!-- /.col-lg-12 -->
	</div><!-- /.row -->
</div><!-- /#page-wrapper -->