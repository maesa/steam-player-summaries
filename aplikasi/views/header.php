<!DOCTYPE html>
<html>
	<head>
	    <meta charset="utf-8">
	    <title>Steam Player Summary</title>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <!-- Bootstrap -->
	    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
	    <link href="<?php echo base_url('css/custom.css'); ?>" rel="stylesheet">
	    <script src="<?php echo base_url('js/respond.min.js'); ?>"></script>
	    <script src="http://code.jquery.com/jquery-latest.min.js"></script>
	    <script type="application/javascript">
			$(document).ready(function() {
				$('#message').hide();
				$('#txtSteamUrl').tooltip('toggle');
				$('#txtSteamUrl').focus();

		        // prevents the overlay from closing if user clicks inside the popup overlay
			    $('.overlay-bg').click(function(){
			        return false;
			    });

			    // prevents the overlay from closing if user clicks inside the popup overlay
			    $('.spinner').click(function(){
			        return false;
			    });

				$('#btn_submit').click(function(e) {
					e.preventDefault();

					var docHeight = $(document).height(); //grab the height of the page
			        var scrollTop = $(window).scrollTop(); //grab the px value from the top of the page to where you're scrolling      
			        //$('.overlay-bg').show(100).css({'height' : docHeight}); //display your popup and set height to the page height
			        $('.overlay-bg').fadeIn();

					if($('#message').is(":visible")) {
						$('#message').hide();
					}

					var form_data = {
						txtSteamUrl : $('#txtSteamUrl').val(),
						ajax : '1'
					};
					$.ajax({
						url: "<?php echo site_url('steamconverter/getplayersummary'); ?>",
						type: 'POST',
						async : false,
						data: form_data,
						success: function(msg) {
							
							$('#message').html(msg);
							$('#message').show();
							$('#txtSteamUrl').val('');
							$('.overlay-bg').fadeOut(1000);
						}
					});
					return false;
				});
			});
		</script>
	</head>

	<body>
	<div class="overlay-bg" id="biji">
		<div class="spinner">
			<div class="bar1"></div>
			<div class="bar2"></div>
			<div class="bar3"></div>
			<div class="bar4"></div>
			<div class="bar5"></div>
			<div class="bar6"></div>
			<div class="bar7"></div>
			<div class="bar8"></div>
			<div class="bar9"></div>
			<div class="bar10"></div>
			<div class="bar11"></div>
			<div class="bar12"></div>
		</div>
	</div>
		<div class="container">
			<header class="row">
				<a href="<?php echo base_url(); ?>"><h1>Steam Player Summary</h1></a>
			</header>