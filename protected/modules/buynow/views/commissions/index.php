<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">

		<?php echo CHtml::link('<i class="fa fa-plus"></i> '.Yii::t('admin','Add').' '.Yii::t('admin','Commission'),Yii::app()->createAbsoluteUrl('buynow/commissions/create'),array('class' => 'btn btn-success','style' => 'float:right;')); ?>
			<h1 class="page-header"><?php echo Yii::t('admin','Manage').' '.Yii::t('admin','Commissions'); ?></h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php echo Yii::t('admin','Commission setup'); ?>
					<?php if($commissionSetting == 1) {
						echo CHtml::link('<label class="label label-bg label-success pull-right"  style="cursor:pointer;font-size:15px;line-height:18px;">'.Yii::t('admin','Enabled').'</label>',Yii::app()->createAbsoluteUrl('buynow/commissions/status'));
					} else { ?>
					<?php echo CHtml::link('<label class="label label-bg label-danger pull-right"  style="cursor:pointer;font-size:15px;line-height:18px;">'.Yii::t('admin','Disabled').'</label>',Yii::app()->createAbsoluteUrl('buynow/commissions/status'));}?>
				</div>

				<!-- /.panel-heading -->
				<div class="panel-body">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>#</th>
								<th><?php echo Yii::t('admin','PERCENTAGE'); ?></th>
								<th><?php echo Yii::t('admin','MINIMUM RATE'); ?></th>
								<th><?php echo Yii::t('admin','MAXIMUM RATE'); ?></th>
								<th><?php echo Yii::t('admin','DATE'); ?></th>
								<th><?php echo Yii::t('admin','STATUS'); ?></th>
								<th><?php echo Yii::t('admin','ACTION'); ?></th>
							</tr>
							
						</thead>
						<tbody>
						<?php $i=0; foreach($commissions as $commission): ?>
							<tr>
								<td><?php echo ++$i; ?>
								</td>
								<td><?php echo $commission->percentage; ?>
								</td>
								<td><?php echo $commission->minRate; ?>
								</td>
								<td><?php echo $commission->maxRate; ?>
								</td>
								<td><?php echo date("d-m-Y",$commission->date); ?>
								</td>
								<td align="center"><?php if($commission->status == 1) { $icon = '<i class="fa fa-check-circle" style="color:#2FDAB8; font-size:20px;"></i>'; } else  { $icon = '<i class="fa fa-times-circle"  style="color:red; font-size:20px;"></i>'; }
								echo CHtml::link($icon,Yii::app()->createAbsoluteUrl('buynow/commissions/changeStatus',array('id'=>$commission->id))).'&nbsp'; ?>
								</td>
								<td align="center"><?php
								echo CHtml::link('<i class="fa fa-pencil"></i>',Yii::app()->createAbsoluteUrl('buynow/commissions/update',array('id'=>$commission->id))).'&nbsp';
								echo CHtml::link('<i class="fa fa-times"></i>',Yii::app()->createAbsoluteUrl('buynow/commissions/delete',array('id'=>$commission->id)),array('onclick' => 'return confirm("'.Yii::t('admin','Are you sure you want to delete?').'");'));
								//echo '<span class="modal-link" onclick=\'confirmModal("link", "admin/commissions/delete/id/", "'.
								//		$commission->id.'")\'><i class="fa fa-times"></i></span>';
							?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<?php unset($_GET[Yii::app()->request->url]);
					$this->widget('CLinkPager',array('pages' => $pages,
					));
					?>
				</div>
				<!-- /.panel-body -->

			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
</div>
