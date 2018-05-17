<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">

			<h1 class="page-header"><?php echo Yii::t('app','Manage Invoices'); ?></h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php echo Yii::t('app','Invoices'); ?>
					<form class="pull-right" method="post">
						<input type="text" name="invoiceNo"
							value="<?php echo Yii::app()->request->getParam('invoiceNo'); ?>"></input>
						<button type="submit">Search<?php echo Yii::t('app','Create Orders'); ?></button>
					</form>


				</div>

				<!-- /.panel-heading -->
				<div class="panel-body" id="orders">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th><?php echo Yii::t('admin','Invoice No.'); ?></th>
								<th><?php echo Yii::t('admin','Order ID'); ?></th>
								<th><?php echo Yii::t('app','Invoice Date'); ?></th>
								<th><?php echo Yii::t('app','Invoice Status'); ?></th>
								<th><?php echo Yii::t('app','Payment Method'); ?></th>
								<th><?php echo Yii::t('app','View Invoice'); ?></th>
						
						</thead>
						<tbody>
						<?php $i=0; foreach($invoices as $invoice): ?>
							<tr>
								<td><?php echo $invoice->invoiceNo; ?></td>
								<td><?php echo $invoice->orderId; ?>
								</td>
								<td><?php echo date("d-m-Y",$invoice->invoiceDate); ?></td>
								<td><?php echo $invoice->invoiceStatus; ?></td>
								<td align="center"><?php echo $invoice->paymentMethod; ?></td>
								<td align="center"><a href='javascript:void(0)'
									onclick="showinvoicepopup(<?php echo $invoice->orderId; ?>)"><i
										class="fa fa-search"></i> </a></td>
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


				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div id="popup_container">
		<div id="show-exchange-popup"
			style="display: none; height: 700px; width: 800px; overflow-y: hidden"
			class="popup ly-title update show-invoice-popup"></div>
	</div>
</div>


<script>

function showinvoicepopup(invoiceId) {
	$('#popup_container').show();
	$('#popup_container').css({
		"opacity" : "1"
	});
	getInvoiceData(invoiceId);
	$('#show-exchange-popup').show();
}

function getInvoiceData(invoiceId) {
	$.ajax({
		type : 'POST',
		url: '<?php echo Yii::app()->createAbsoluteUrl('admin/invoices/view'); ?>',
	    data : {invoiceId : invoiceId},
	    success : function(data) {
	    	$('#show-exchange-popup').html(data);
	    }
	});
}
</script>
</script>
