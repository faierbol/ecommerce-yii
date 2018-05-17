<table class="table table-bordered">
	<thead>
		<tr>
			<th><?php echo Yii::t('app','Order No.'); ?></th>
			<th><?php echo Yii::t('app','Merchant Name'); ?></th>
			<th><?php echo Yii::t('app','Order Status'); ?></th>
			<th><?php echo Yii::t('app','Currency'); ?></th>
			<th><?php echo Yii::t('app','Amount'); ?></th>
			
	
	</thead>
	<tbody>
	<?php $i=0; foreach($orders as $order): ?>
		<tr>
			<td><?php echo $order->orderId; ?></td>
			<td><?php echo Myclass::getUserDetails($order->userId)->username; ?>
			</td>
			<td><?php echo $order->status; ?></td>
			<td><?php echo $order->currency; ?></td>
			<td align="center"><?php echo $order->totalCost; ?></td>
		
		</tr>
		<?php endforeach; ?>

	</tbody>


</table>
		<?php
		$this->widget('CLinkPager',array('pages' => $pages,
		));
		?>
<br>
