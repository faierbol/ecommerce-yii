<table class="table table-bordered">
	<thead>
		<tr>
			<th><?php echo Yii::t('app','Invoice No.'); ?></th>
			<th><?php echo Yii::t('app','Order Id'); ?></th>
			<th><?php echo Yii::t('app','Invoice Date'); ?></th>
			<th><?php echo Yii::t('app','Invoice Status'); ?></th>
			<th><?php echo Yii::t('app','Payment Method'); ?></th>
			<th><?php echo Yii::t('app','View Invoice'); ?></th>
	
	</thead>
	<tbody>
	<?php $i=0; foreach($invoices as $invoice): ?>
		<tr>
			<td><?php echo $invoice->invoiceNo; ?>
			</td>
			<td><?php echo $invoice->orderId; ?></td>
			<td><?php echo date("d-m-Y",$invoice->invoiceDate); ?>
			</td>
			<td><?php echo $invoice->invoiceStatus; ?>
			</td>
			<td align="center"><?php echo $invoice->paymentMethod; ?>
			</td>
			<td align="center"><?php
			echo CHtml::link('<i class="fa fa-search"></i>',Yii::app()->createAbsoluteUrl('admin/invoices/view',array('id'=>$invoice->invoiceId))).'&nbsp';

			?></td>
		</tr>
		<?php endforeach; ?>

	</tbody>


</table>
		<?php
		$this->widget('CLinkPager',array(
		'pages' => $pages,
		'currentPage'=>$pages->getCurrentPage(),
		));
		?>
<br>
