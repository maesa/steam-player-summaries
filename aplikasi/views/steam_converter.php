<?php
	$attributes = array('class' => 'form-horizontal', 'role' => 'form', 'id'  => 'steam_form');
	echo form_open('steamconverter/getplayersummary',$attributes);
?>
	<div class="form-group">
		<?php
			//Create a label
			$attributes = array(
			    'class' => 'col-md-3 control-label',
			);
			echo form_label('Steam URL', 'steam_url', $attributes);
		?>
		<div class="col-md-6">
			<?php
				//Create a text field
				$data = array(
			              'name'        	=> 'txtSteamUrl',
			              'id'          	=> 'txtSteamUrl',
			              'placeholder' 	=> 'http://steamcommunity.com/profiles/76561198060767239/',
			              'class'   		=> 'form-control',
			              'autocomplete' 	=> 'off',
			              'data-toggle' 	=> 'tooltip',
						  'data-placement' 	=> 'bottom',
						  'title' 	=> 'or http://steamcommunity.com/id/aya'
			            );

				echo form_input($data);
			?>
		</div> <!-- col-lg-6 -->
		<div class="col-md-3">
			<button type="submit" class="btn btn-primary" id="btn_submit">Submit</button>
		</div>
	</div> <!-- form-group -->
<?php echo form_close(); ?>
	<div class="row" id="message">
	</div>
