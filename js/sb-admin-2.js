$(function() {

    $('#side-menu').metisMenu();

});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse')
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse')
        }

        height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    })
})

/*$(document).on('change','#Banners_bannerimage', function(){
    var file, img;
    if ((file = this.files[0])) {
        img = new Image();
        img.onload = function () {
            if(this.width == "1140" && this.height == "325")
            {
            	$("#bannerimageerr").html("dfds");
            	$("#bannercreatebtn").removeAttr("disabled");
            	//$("#Banners_bannerimage_wrap_list").show();
            }
            else
            {
            	$("#bannerimageerr").html("Image size should be 1140 x 325");
						setTimeout(function() {
							$("#bannerimageerr").html("");
						}, 3000);
            	$("#Banners_bannerimage_wrap_list").html("");
            	$(".MultiFile").removeAttr("disabled");
            	$("#bannercreatebtn").attr("disabled","true");
            }
        };
    }
});*/


    $(".autoapprove").click(function() {

       var autoapprove=$('.autoapprove').val();
       if(autoapprove=='1'){
       	$('.autoapprove').val('0');
       }else
       {
       	$('.autoapprove').val('1');
       }
       var autoapprovestatus=$('.autoapprove').val();
       $.ajax({
				type : 'POST',
				url : yii.urls.base + '/admin/item/itemautoapprove',
				data : {autoapprovestatus : autoapprovestatus},
				success : function(data) {
					//alert(data);


			}});
  });

/*$(document).on('change','#Sitesettings_bannerstatus', function(){
	if ($('#Sitesettings_bannerstatus').is(':checked')) {
		enablestatus = "1";
	}
	else
	{
		enablestatus = "0";
	}
		$.ajax({
			url: yii.urls.base + '/admin/banners/bannerenable',
			type: "post",
			data: {'enablestatus':enablestatus},
			dataType: "html",
			success: function(responce){

			}
		});
}); */


   $(".bannerapprove").click(function(){
   var autoapprove=$('.bannerapprove').val();
       if(autoapprove=='1'){
       	$('.bannerapprove').val('0');
       }else
       {
       	$('.bannerapprove').val('1');
       }
       var enablestatus=parseInt($('.bannerapprove').val());
		$.ajax({
				type : 'POST',
				url: yii.urls.base + '/admin/banners/bannerenable',
				data: {'enablestatus':enablestatus},
				success : function(responce) {
					//alert(responce);
				}
			});
});

    $(".videobannerapprove").click(function(){
   var autoapprove=$('.videobannerapprove').val();
       if(autoapprove=='1'){
       	$('.videobannerapprove').val('0');
       }else
       {
       	$('.videobannerapprove').val('1');
       }
       var enablestatus=parseInt($('.videobannerapprove').val());
		$.ajax({
				type : 'POST',
				url: yii.urls.base + '/admin/banners/bannervideoenable',
				data: {'enablestatus':enablestatus},
				success : function(responce) {
					//alert(responce);
				}
			});
});
	$(document).on('click', '#adminpushnot', function() {
		adminData = $('#admin-textarea').val();
		if (adminData == ''){

			$('.adminpushnot-error').html(Yii.t('admin', 'Please enter text'));
			setTimeout(function() {
				  $('.adminpushnot-error').fadeOut('slow');
				}, 5000);
			return;

		}

		if (adminData != ''){
			$.ajax({
				type : 'POST',
				url : yii.urls.base + '/admin/action/sendpushnot/',
				data : {
					adminData : adminData
				},
				beforeSend : function() {
					$('#adminpushnot').html(Yii.t('app', 'Sending') + '...');
				},
				success : function(data) {
					$('#admin-textarea').val("");
					$('#adminpushnot').html(Yii.t('app', 'Sent'));
					setTimeout(function() {
						  $('#adminpushnot').html(Yii.t('app', 'Send'));
						}, 2000);

					 if (data == "error"){
						$(".adminpushnot-error").html(Yii.t('app', 'Message not sent') + '..!!');
						setTimeout(function() {
							  $('.adminpushnot-error').fadeOut('slow');
							}, 5000);
						return;
					}
				},
				failure: function(){
					$('#adminpushnot').html(Yii.t('app', 'Sent'));
				}
			});
		}


	});

	function validatebanner()
	{
		bannerimage = $("#Banners_bannerimage").val();
		appbannerimage = $("#Banners_appbannerimage").val();
		bannerurl = $("#Banners_bannerurl").val();
		bannerimage1 = $("#hiddenwebImage").val();
		appbannerimage1 = $("#hiddenappImage").val()
		if(bannerimage == "" && bannerimage1 == "")
		{
			$("#bannerimageerr").html("Please select image");
			setTimeout(function() {
			  $('#bannerimageerr').html("");
			}, 5000);
			return false;
		}
		if(appbannerimage == "" && appbannerimage1 == "")
		{
			$("#appbannerimageerr").html("Please select image");
			setTimeout(function() {
			  $('#appbannerimageerr').html("");
			}, 5000);
			return false;
		}
		if($.trim(bannerurl) == "")
		{
			$("#bannerurlerr").html("Please enter banner url");
			setTimeout(function() {
			  $('#bannerurlerr').html("");
			}, 5000);
			return false;
		}
	}

	function validatepromotion()
	{
		promotionname = $("#Promotions_name").val();
		promotiondays = $("#Promotions_days").val();
		promotionprice = $("#Promotions_price").val();

		if($.trim(promotionname) == "")
		{
			$("#nameerr").html("Please enter promotion name");
			setTimeout(function() {
			  $('#nameerr').html("");
			}, 5000);
			return false;
		}
		if($.trim(promotiondays) == "")
		{
			$("#dayserr").html("Please enter promotion days");
			setTimeout(function() {
			  $('#dayserr').html("");
			}, 5000);
			return false;
		}
		if(promotiondays <= 0)
		{
			$("#dayserr").html("Please enter promotion days greater than 0");
			setTimeout(function() {
			  $('#dayserr').html("");
			}, 5000);
			return false;
		}
		if($.trim(promotionprice) == "")
		{
			$("#priceerr").html("Please enter promotion price");
			setTimeout(function() {
			  $('#priceerr').html("");
			}, 5000);
			return false;
		}
		if(promotionprice <= 0)
		{
			$("#priceerr").html("Please enter promotion price greater than 0");
			setTimeout(function() {
			  $('#priceerr').html("");
			}, 5000);
			return false;
		}
	}


		function validatebannervideo()
	{
		video = $("#file").val();
		video1 = $("#hiddenBannerVideo").val();
		
		if(video == "" && video1 == "")
		{
			$("#bannervideoError").html(Yii.t('admin', 'Please select the video'));
					document.getElementById("beforeupload").style.display = "block"; // to undisplay
   					document.getElementById("upload").style.display = "none"; // to display
			setTimeout(function() {
			  $('#bannervideoError').html("");
			}, 5000);
			return false;
		}
		
		
	}