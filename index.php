<!DOCTYPE html>
<html>
<head>
	<title>Export Module</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap-select.css">
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
	
	<link href="assets/select2/css/select2.min.css" rel="stylesheet" />
	<script src="assets//select2/js/select2.min.js"></script>
	
	<link rel="stylesheet" href="assets/jquery-ui-1.10.4/development-bundle/themes/base/jquery.ui.all.css">
	<script src="assets/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.core.js"></script>
	<script src="assets/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.widget.js"></script>
	<script src="assets/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.datepicker.js"></script>
	<style>
		body {
		  padding-top: 70px;
		}
		.bootstrap-select:not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn){width:100%;}
		.bootstrap-select.btn-group .dropdown-menu{width:100%;}
		
		.select2-container--default .select2-selection--multiple .select2-selection__choice {
			margin-top: 6px;
			font-size: 12px;
		}
		.select2-container--default .select2-search--inline .select2-search__field{width:100%!important;}
	</style>
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">Export Module</a>
    </div>
  </div>
</nav>

<div class="container">
	<hr />
	<form method="POST" action="export.php"  class="form-inline">
	<?php
		include "connect.php";
		$now=date('m-d-Y');
		$a=date('Y-m-d');
		$prevDate= strtotime($a.' -6 months');
		$prevDate=date('m-d-Y', $prevDate);
	?>
		<div class="row">

			
			<div class="col-md-2">
				<label class="control-label">Module </label>
			</div>
			<div class="col-md-10">
				<select name="module_id" id="module_id" class="form-control" title="Select a module" required style="width:100%">
					<option value="1">MODULE #1: MOC Colonoscopy</option>
					<option value="2">MODULE #2: MOC Upper Endoscopy</option>
					<option value="3">MODULE #3: MOC Failure to Thrive</option>
					<option value="4">MODULE #4: MOC Informed Consent</option>
					<option value="5">MODULE #5: MOC Constipation</option>
					<option value="6">MODULE #6: MOC Transition</option>
					<option value="7">MOC Enteral Nutrition</option>
					<option value="8">MOC Hepatitis B</option>
				</select>
			</div>
		</div>
		
		<div class="row" style="margin-top:8px;">
			<div class="col-md-2">
				<label class="control-label">User</label>
			</div>
			<div class="col-md-10" style="padding-top:6px;padding-bottom:6px;">
				<label class="radio-inline">
				  <input type="radio" class="select_user" name="select_user" id="all_users" value="all_user" checked > All users
				</label>
				<label class="radio-inline">
				  <input type="radio" class="select_user" name="select_user" id="specific_users" value="specific_user"> Specific user(s)
				</label>
			 
				<div class="specific_users_section" style="display:none;margin-top:10px;">
					<select name="email[]" id="email" class="js-data-ajax fom-control" placeHolder="Type name, email, or APN" multiple style="width:100%;">
					
					</select>
				</div>
			</div>
			
		</div>
		<div class="row" style="margin-top:8px;">
			<div class="col-md-2">
				<label class="control-label">Date Range</label>
			</div>
			<div class="col-md-10">
				<input type="text" style="max-width:170px;display:inline-block;" class="form-control datepicker" name="startDate" id="startDate"  value="<?php echo $prevDate;?>"/>
				<span style="display:inline-block;"> &nbsp; to &nbsp; </span> 
				<input type="text" style="max-width:170px;display:inline-block;" class="form-control datepicker" name="endDate" id="endDate" value="<?php echo $now;?>"/>
				
			</div>
		</div>
		<div class="row" style="margin-top:8px;">
			<div class="col-md-2"></div>
			<div class="col-md-10">
				<input type="submit" name="submit" value="Generate Report" class="btn btn-md btn-primary" />
			</div>
		</div>
  </form>
</div>

<script>
	$(document).ready(function () {
		
		
		
		$('.js-data-ajax').select2({
			placeholder: 'Type name, email, or APN',
			minimumInputLength: 2,
			language: {
				inputTooShort: function() {
					return "Enter 2 characters or more";
				}
			},
			ajax: {
				url: "search_user.php",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						term: params.term, // search term
						page: params.page
					};
				},
				processResults: function (data) {
					return {
						results: $.map(data, function (item) {
							return {
								//text: unescape(encodeURIComponent(item.name))+' APN: '+item.business+' '+item.email,
								text: unescape(encodeURIComponent(item.name))+' ('+item.business+') '+item.email,
								id: item.email
							}
						})
					};
				}
			}
		});
		
		$('.select_user').on('change', function () {
			if($('#specific_users').is(':checked')) { 
				$('.specific_users_section').show();
			} else {
				$('.specific_users_section').hide();
			}
		});
		
		$('.select_user').on('click', function () {
			if($('#specific_users').is(':checked')) { 
				$('.specific_users_section').show();
			} else {
				$('.specific_users_section').hide();
			}
		});
		
		
		$(".datepicker").datepicker({
			dateFormat: 'mm-dd-yy'
		}); 
	});
</script>
</body>
</html>