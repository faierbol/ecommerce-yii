<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
			<?php echo Yii::t('admin','Manage').' '.Yii::t('admin','Orders'); ?>
			</h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
				<?php echo Yii::t('admin','Orders'); ?>
				<?php
				if(Yii::app()->request->getParam('status') == 'pending') {
					$class = 'btn-primary';
				}else {
					$class = '';
				}
				if(Yii::app()->request->getParam('status') == 'shipped') {
					$sclass = 'btn-primary';
				}else {
					$sclass = '';
				}
				if(Yii::app()->request->getParam('status') == 'delivered') {
					$dclass = 'btn-primary';
				}else {
					$dclass = '';
				}
				if(Yii::app()->request->getParam('status') == '') {
					$oclass = 'btn-primary';
				}else {
					$oclass = '';
				}
				echo CHtml::link(Yii::t('admin','New Orders'), Yii::app()->createAbsoluteUrl('buynow/orders/admin',array('status'=>'pending')),array('class' => "btn btn-sm btn-default pull-right $class",'style'=>"cursor: pointer; font-size: 12px; line-height: 16px; margin-left:5px;")).'&nbsp;';
				echo CHtml::link(Yii::t('admin','Shipped'), Yii::app()->createAbsoluteUrl('buynow/orders/admin',array('status'=>'shipped')),array('class' => "btn btn-sm btn-default pull-right $sclass",'style'=>"cursor: pointer; font-size: 12px; line-height: 16px;margin-left:5px; ")
				).'&nbsp;';
				echo CHtml::link(Yii::t('admin','Delivered'),Yii::app()->createAbsoluteUrl('buynow/orders/admin',array('status'=>'delivered')),array('class' => "btn btn-sm btn-default pull-right $dclass",'style'=>"cursor: pointer; font-size: 12px; line-height: 16px;margin-left:5px;")
				).'&nbsp;';
				echo CHtml::link(Yii::t('admin','All Orders'),Yii::app()->createAbsoluteUrl('buynow/orders/admin'),array('class' => "btn btn-sm btn-default pull-right $oclass",'style'=>"cursor: pointer; font-size: 12px; line-height: 16px;margin-left:5px;")
				).'&nbsp;';
				?>
				</div>
				<!-- /.panel-heading -->
				<div class="panel-body">
				
				<?php
				if(Yii::app()->controller->action->id == 'scroworders') { ?>
				<?php echo $this->renderPartial('scroworders',compact('model','status'));
				} else {
					echo $this->renderPartial('admin',compact('model','status'));
				} ?>
				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
</div>
