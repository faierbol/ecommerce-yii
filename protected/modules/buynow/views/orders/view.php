
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
			<?php echo Yii::t('admin','Order'); ?>
			</h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
				<?php echo Yii::t('admin','View').' '.Yii::t('admin','Order'); ?>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<div class="containerdiv">
							<?php $seller = Myclass::getUserDetails($model->sellerId); ?>
								<span class="pay-status"><?php echo Yii::t('admin','Payment to')?>
								</span><br> <span class="pay-to"><b><?php echo $seller->username; ?>
								</b> </span><br> <span class="pay-status"><?php echo Yii::t('admin','Email')?>
									: <?php echo $seller->email; ?> </span>
								<div class="inv-clear"></div>
								<hr>
								<div class="buyerdiv" style="height: auto; overflow: hidden;">
									<div class="buyerper" style="width: 30%; float: left;">
										<span class="pay-status"><?php echo Yii::t('admin','Buyer Details')?>
										</span><br> <span class="pay-to"><b><?php echo $model['user']['username']; ?>
										</b> </span><br> <span class="pay-status"><?php echo Yii::t('admin','Email')?>
											: <?php echo $model['user']['email']; ?> </span>
									</div>


									<div class="inv-shipping" style="width: 35%; float: left;">
									<?php if(!empty($shipping)) { ?>
										<span class="pay-status"><?php echo Yii::t('admin','Shipping Address')?>
										</span><br> <b><?php echo $shipping->name; ?> </b>,<br>
										<?php echo $shipping->address1; ?>
										,<br>
										<?php echo $shipping->address2; ?>
										,<br>
										<?php echo $shipping->city; ?>
										-
										<?php echo $shipping->zipcode; ?>
										,<br>
										<?php echo $shipping->state; ?>
										,<br>
										<?php echo $shipping->country; ?>
										,<br>
										<?php echo Yii::t('admin','Phone no.')?>
										:
										<?php echo $shipping->phone; ?>
										<?php } ?>
									</div>
									<?php if(!empty($trackingDetails)) { ?>
									<div class="inv-shipping" style="width: 35%; float: left;">
										<span class="pay-status"><?php echo Yii::t('admin','Tracking Details'); ?>
										</span><br> <br>

										<?php if(!empty($trackingDetails->trackingid)) { echo Yii::t('app','Tracking ID'); ?>
										: <b><?php echo $trackingDetails->trackingid; ?> </b>
										<?php } ?>
										<br>
										<?php if(!empty($trackingDetails->shippingdate)) { echo Yii::t('app','Shipment Date'); ?>
										: <b><?php echo date("d-m-Y",$trackingDetails->shippingdate); ?>
										</b>
										<?php } ?>
										<br>
										<?php if(!empty($trackingDetails->couriername)) { echo Yii::t('admin','Logistic Name'); ?>
										: <b><?php echo $trackingDetails->couriername; ?> </b>
										<?php } ?>
										<br>
										<?php if(!empty($trackingDetails->courierservice)) { echo Yii::t('admin','Shipment Service'); ?>
										: <b><?php echo $trackingDetails->courierservice; ?> </b>
										<?php } ?>
										<br>
										<?php if(!empty($trackingDetails->notes)) { echo Yii::t('admin','Additional Notes'); ?>
										: <b><?php echo $trackingDetails->notes; ?> </b>
										<?php } ?>
										<br>

									</div>
									<?php } ?>
								</div>
								<hr>
								<div class="inv-clear"></div>
								<table
									class="tablesorter table table-striped table-bordered table-condensed">
									<thead>
										<tr>
											<th>Sl no.</th>
											<th><?php echo Yii::t('admin','Item Name'); ?></th>
											<th><?php echo Yii::t('admin','Item Quantity'); ?></th>
											<th><?php echo Yii::t('admin','Item Unitprice'); ?></th>
											<th><?php echo Yii::t('admin','Shipping fee'); ?></th>
											<th><?php echo Yii::t('admin','Total Price'); ?></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>1</td>
											<td><?php echo $model['orderitems'][0]['itemName']; ?></td>
											<td><?php echo $model['orderitems'][0]['itemQuantity']; ?></td>
											<td><?php echo $model['orderitems'][0]['itemunitPrice'].' '.$model->currency; ?>
											</td>
											<td><?php echo $model['orderitems'][0]['shippingPrice'].' '.$model->currency; ?>
											</td>
											<?php $totalCost =  ($model['orderitems'][0]['itemunitPrice'] * $model['orderitems'][0]['itemQuantity']) + $model['orderitems'][0]['shippingPrice']; ?>
											<td><?php echo $totalCost.' '.$model->currency; ?></td>
										</tr>
									</tbody>
								</table>
								<div style="margin-top: 12px; margin-left: 615px; width: 300px;">
									<table>
										<tbody>
											<tr>
												<td align="left" style="width: 200px;"><p class="gtotal">
												<?php echo Yii::t('admin','Item Total'); ?>
													</p>
												</td>
												<td style="width: 50px;"></td>
												<td align="right" style="width: 100px;"><p class="gtotal">
														<b><?php $value = $model['orderitems'][0]['itemunitPrice'] * $model['orderitems'][0]['itemQuantity'];
														echo $value.' '.$model->currency; ?> </b>
													</p></td>
											</tr>


											<tr>
												<td align="left"><p class="gtotal">
												<?php echo Yii::t('admin','Shipping fee'); ?>
													</p></td>
												<td style="width: 50px;"></td>
												<td align="right"><p class="gtotal">
														<b><?php 
														if ($model['orderitems'][0]['shippingPrice'] == "" || $model['orderitems'][0]['shippingPrice'] == 0){
															echo '0 '.$model->currency;
														}else{
															echo $model['orderitems'][0]['shippingPrice'].' '.$model->currency;
														} ?>
														</b>
													</p></td>
											</tr>
											<?php if(!empty($model->discount)) { ?>
											<tr>
												<td align="left"><p class="gtotal">
												<?php echo Yii::t('app','Discount Amount'); ?>
													</p>
												</td>
												<td style="width: 50px;"></td>
												<td align="right"><p class="gtotal invoice-amnt">
														<b>(-) <?php echo $model->discount.' '.$model->currency; ?>
														</b>
													</p>
												</td>
											</tr>
											<?php } ?>

											<tr>
												<td colspan="2"><div id="horizonal"
														style="border-top: 1px solid black; width: 300px; position: absolute;"></div>
												</td>
											</tr>
											<tr>
												<td align="left"><p class="gtotal">
												<?php echo Yii::t('admin','Grand Total'); ?>
													</p></td>
												<td style="width: 50px;"></td>
												<td align="right"><p class="gtotal">
														<b><?php echo $totalCost - $model->discount.' '.$model->currency; ?>
														</b>
													</p></td>
											</tr>
											<tr>
												<td colspan="2"><div id="horizonal"
														style="border-top: 1px solid black; width: 300px; position: absolute;"></div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>