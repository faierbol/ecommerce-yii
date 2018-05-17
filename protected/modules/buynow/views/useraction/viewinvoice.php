    <div class="sale-info col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">                                                   
        <div class="sale-initiate-date"><span class="bold"><?php echo Yii::t('app','Order');?> # <?php echo $model['invoices'][0]['invoiceNo'];?> on 
            <?php echo date('m/d/Y',$model['invoices'][0]['invoiceDate']);?></span>
        	<a class="pull-right no-print" href="javascript:void(0);"><div class="invoice-print-btn" id="printbtn" ><?php echo Yii::t('app','Print Invoice');?></div></a></div>
    </div>
    <div class="invoice-popup-heading margin-top-15 invoice-popup-line-padding col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
        <?php echo Yii::t('app','Payment method');?> : <?php echo $model['invoices'][0]['paymentMethod']; ?>
    </div>
    <div class="invoice-popup-heading invoice-popup-line-padding col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
        <?php echo Yii::t('app','Payment status');?> : <?php echo ucfirst($model['invoices'][0]['invoiceStatus']); ?>
    </div>
    <div class="margin-top-15 invoice-popup-addr-cnt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 no-hor-padding">
            <div class="invoice-popup-heading bold padding-bottom-10 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Buyers Details');?></div>
            <div class="bold col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Buyers Name');?></div>
            <?php $seller = Myclass::getUserDetails($model->userId); ?>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">Email : <?php echo $seller->email; ?></div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="invoice-popup-heading bold padding-bottom-10 col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Shipping Address');?></div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
                <?php if(!empty($shipping)) { ?>
                    <b><?php echo $shipping->name; ?> </b>,<br>
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
                    ,<br>Phone no. :
                    <?php echo $shipping->phone; ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>   
    <div class="invoice-popup-item-details-cnt col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="bold col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo Yii::t('app','Item Name');?></div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding"><?php echo $model['orderitems'][0]['itemName']; ?></div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="bold padding-bottom-10 col-xs-12 col-sm-12 col-md-6 col-lg-6 no-hor-padding"><?php echo Yii::t('app','Item Unitprice');?></div>
            <div class="padding-bottom-10 col-xs-12 col-sm-12 col-md-6 col-lg-6 no-hor-padding"><?php echo $model['orderitems'][0]['itemunitPrice'].' '.$model->currency; ?></div>
            <div class="bold col-xs-12 col-sm-12 col-md-6 col-lg-6 no-hor-padding"><?php echo Yii::t('app','Shipping Fee');?></div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 no-hor-padding"><?php echo (int)$model->totalShipping.' '.$model->currency; ?></div>
            <div class="invoice-popup-total margin-top-15 padding-top-10 padding-bottom-10 bold col-xs-6 col-sm-6 col-md-6 col-lg-6 no-hor-padding"><?php echo Yii::t('app','Total Price');?></div>
            <div class="invoice-popup-total margin-top-15 padding-top-10 padding-bottom-10 col-xs-6 col-sm-6 col-md-6 col-lg-6 no-hor-padding">
                <?php
                                    echo $model->getTotalAmount();
                                    ?>
            </div>
        </div>
    </div> 

    <style type="text/css">
    @media print {
  body * {
    visibility: hidden;
  }
    #printbtn,.no-print, no-print *
    {
        display: none !important;
    }  
  #invoice_content, #invoice_content * {
    visibility: visible;
  }
  #invoice_content {
    position: absolute;
    left: 0px;
    top: -60px;
  }
}
    </style>
    <script type="text/javascript">
    $("#printbtn").click(function(){
        window.print();
    });
    </script>