/**
 * Custom javascript function
 * @author  (c) @iLabAfrica
 */

$(function(){
	/**	HEADER
	 *   Username display
	 */
	$('.user-link').click(function(){
		$('.user-settings').toggle();
		$('.loc-settings').hide();
	});

	$('.user-profile .user-settings a').click(function(){
		$('.user-settings').toggle();
	});

	$('.loc-link').click(function(){
		$('.loc-settings').toggle();
		$('.user-settings').hide();
	});

	$('.loc-profile .loc-settings a').click(function(){
		$('.loc-settings').toggle();
	});

	/*	LEFT SIDEBAR FUNCTIONS	*/
	
	/*  Click main menu */
	$('.main-menu').click(function(){

		$('.main-menu').removeClass('active');
		$(this).addClass('active');

		$('.main-menu').siblings().hide();
		$(this).siblings().show();
	});

	/**  USER 
	 *-  Load password reset input field
	 */

	$('a.reset-password').click(function() {
		if ( $('input.reset-password').hasClass('hidden')) {
				$('input.reset-password').removeClass('hidden');
		}else {
			$('input.reset-password').addClass('hidden');
		}
	});

	/*Submitting Profile edit, with password change validation*/
	$('.update-reset-password').click(function() {
			editUserProfile();
	});

	/** 
	 *	LAB CONFIGURATION 
	 */

	 /* Add another surveillance */
	$('.add-another-surveillance').click(function(){
		newSurveillanceNo = $(this).data('new-surveillance');
		var inputHtml = $('.addSurveillanceLoader').html();
		//Count new measures on the new measure button
		$('.surveillance-input').append(inputHtml);
		$('.surveillance-input .new').addClass('new-surveillance-'+newSurveillanceNo).removeClass('new');
		$(this).data('new-surveillance',  newSurveillanceNo+1).attr('data-new-surveillance',  newSurveillanceNo+1);
		addNewSurveillanceAttributes(newSurveillanceNo);
		delete newSurveillanceNo;
	});
	 
	 /* Add another disease */
	$('.add-another-disease').click(function(){
		newDiseaseNo = $(this).data('new-disease');
		var inputHtml = $('.addDiseaseLoader').html();
		//Count new measures on the new measure button
		$('.disease-input').append(inputHtml);
		$('.disease-input .new').addClass('new-disease-'+newDiseaseNo).removeClass('new');
		$(this).data('new-disease',  newDiseaseNo+1).attr('data-new-disease',  newDiseaseNo+1);
		addNewDiseaseAttributes(newDiseaseNo);
		delete newDiseaseNo;
	});

	/** 
	 *	MEASURES 
	 */

	 /* Add another measure */
	$('.add-another-measure').click(function(){
		newMeasureNo = $(this).data('new-measure');
		var inputHtml = $('.measureGenericLoader').html();
		//Count new measures on the new measure button
		$('.measure-container').append(inputHtml);
		$('.measure-container .new-measure-section').find(
			'.measuretype-input-trigger').addClass('new-measure-'+newMeasureNo);
		$('.measure-container .new-measure-section').find(
			'.measuretype-input-trigger').attr('data-new-measure-id',  newMeasureNo);
		$('.measure-container .new-measure-section').find(
			'.add-another-range').attr('data-new-measure-id',  newMeasureNo);
		$('.measure-container .new-measure-section').find(
			'.add-another-range').addClass('new-measure-'+newMeasureNo);
		$('.measure-container .new-measure-section').find(
			'.measurevalue').addClass('new-measure-'+newMeasureNo);
		$('.measure-container .new-measure-section').addClass(
			'measure-section new-'+newMeasureNo).removeClass('new-measure-section');
		$(this).data('new-measure',  newMeasureNo+1).attr('data-new-measure',  newMeasureNo+1);
		addNewMeasureAttributes(newMeasureNo);
		delete newMeasureNo;
	});

	 /* Add another measure range value */
	$('.measure-container').on('click', '.add-another-range', function(){
		var inputClass = [
			'.numericInputLoader',
			'.alphanumericInputLoader',
			'.alphanumericInputLoader',
			'.freetextInputLoader'
		]; 

		if ($(this).data('measure-id') === 0) {
			var newMeasureId = $(this).data('new-measure-id');
			var measureID = 'new-measure-'+newMeasureId;
		} else {
			var measureID = $(this).data('measure-id');
		}
		var measureTypeId = $('.measuretype-input-trigger.'+measureID).val() - 1;
		var inputHtml = $(inputClass[measureTypeId]).html();
		$(".measurevalue."+measureID).append(inputHtml);
		if ($(this).data('measure-id') != 0) {
			editMeasureRangeAttributes(measureTypeId,measureID);
		}else{
			addMeasureRangeAttributes(measureTypeId, newMeasureId);
		}
	});

	/*  load measure range input UI for the selected measure type */
	$( '.measure-container' ).on('change', '.measuretype-input-trigger', loadRangeFields);

	/*  re-load measure range input UI for the selected measure type on error */
	if ($('.measurevalue').is(':empty')){
		var measure_type = $( '.measuretype-input-trigger' ).val();
		if ( measure_type > 0 ) {
			loadRangeFields();
		}
	}

	/** GLOBAL DELETE	
	 *	Alert on irreversible delete
	 */
	$('.confirm-delete-modal').on('show.bs.modal', function(e) {
	    $('#delete-url').val($(e.relatedTarget).data('id'));
	});

	$('.btn-delete').click(function(){
		$('.confirm-delete-modal').modal('toggle');
		window.location.href = $('#delete-url').val();
	});

	UIComponents();

	/* Clicking the label of an radio/checkbox, checks the control*/
	$('span.input-tag').click(function(){
		$(this).siblings('input').trigger('click');
	});

	// Delete measure range

	$('body').on('click', '.measure-input .close', function(){
		$(this).parent().parent().remove();
	});

	// Delete measure

	$('.measure-container').on('click', '.close', function(){
		$(this).parent().parent().remove();
	});
	
	// Delete Surveillance entry

	$('.surveillance-input').on('click', '.close', function(){
		$(this).parent().parent().parent().remove();
	});

	// Delete Disease entry

	$('.disease-input').on('click', '.close', function(){
		$(this).parent().parent().parent().remove();
	});

	/** 
	 * Fetch Test results
	 */

	$('.fetch-test-data').click(function(){
		var testTypeID = $(this).data('test-type-id');
		var specimenId = $(this).data('accession-number');
		var url = $(this).data('url');
		$.post(url, { test_type_id: testTypeID, accession_number: specimenId}).done(function(data){
			$.each($.parseJSON(data), function (index, obj) {

				$('#'+index).val(obj);
			});
		});
	});

	/** 
	 * Search for patient from new test modal
	 * UI Rendering Logic here
	 */

	$('#new-test-modal .search-patient').click(function(){
		var searchText = $('#new-test-modal .search-text').val();
		var url = location.protocol+ "//"+location.host+ "/patient/search";
		var output = "";
		var cnt = 0;
		$.post(url, { text: searchText}).done(function(data){
			$.each($.parseJSON(data), function (index, obj) {
				output += "<tr>";
				output += "<td><input type='radio' value='" + obj.id + "' name='pat_id'></td>";
				output += "<td>" + obj.patient_number + "</td>";
				output += "<td>" + obj.name + "</td>";
				output += "</tr>";
				cnt++;
			});
			$('#new-test-modal .table tbody').html( output );
			if (cnt === 0) {
				$('#new-test-modal .table').hide();
			} else {
				$('#new-test-modal .table').removeClass('hide');
				$('#new-test-modal .table').show();
			}
		});
	});


	/* 
	* Prevent patient search modal form submit (default action) when the ENTER key is pressed
	*/

	$('#new-test-modal .search-text').keypress(function( event ) {
		if ( event.which == 13 ) {
			event.preventDefault();
			$('#new-test-modal .search-patient').click();
		}
	});

	/** - Get a Test->id from the button clicked,
	 *  - Fetch corresponding test and default specimen data
	 *  - Display all in the modal.
	 */
	$('#change-specimen-modal').on('show.bs.modal', function(e) {
	    //get data-id attribute of the clicked element
	    var id = $(e.relatedTarget).data('test-id');
		var url = $(e.relatedTarget).data('url');

	    $.post(url, { id: id}).done(function(data){
		    //Show it in the modal
		    $(e.currentTarget).find('.modal-body').html(data);
	    });
	});
  

	/** Receive Test Request button.
	 *  - Updates the Test status via an AJAX call
	 *  - Changes the UI to show the right status and buttons
	 */
	$('.tests-log').on( "click", ".receive-test", function(e) {

		var testID = $(this).data('test-id');
		var specID = $(this).data('specimen-id');

		var url = location.protocol+ "//"+location.host+ "/test/" + testID+ "/receive";
		$.post(url, { id: testID}).done(function(){
			window.location.reload()
		});

		/*
		var parent = $(e.currentTarget).parent();
		// First replace the status
		var newStatus = $('.pending-test-not-collected-specimen').html();
		parent.siblings('.test-status').html(newStatus);

		// Add the new buttons
		var newButtons = $('.accept-button').html();
		parent.append(newButtons);

		// Set properties for the new buttons
		parent.children('.accept-specimen').attr('data-test-id', testID);
		parent.children('.accept-specimen').attr('data-specimen-id', specID);

		// Now remove the unnecessary buttons
		$(this).siblings('.receive-test').remove();
		$(this).remove();
		*/
	});

	/** Accept Specimen button.
	 *  - Updates the Specimen status via an AJAX call
	 *  - Changes the UI to show the right status and buttons
	 */
	$('.tests-log').on( "click", ".accept-specimen", function(e) {

		var testID = $(this).data('test-id');
		var specID = $(this).data('specimen-id');
		var url = $(this).data('url');
		$.post(url, { id: specID}).done(function(){
			window.location.reload()
		});

		/*
		var parent = $(e.currentTarget).parent();
		// First replace the status
		var newStatus = $('.pending-test-accepted-specimen').html();
		parent.siblings('.test-status').html(newStatus);

		// Add the new buttons
		var newButtons = $('.reject-start-buttons').html();
		parent.append(newButtons);
		var referButton = $('.start-refer-button').html();
		parent.append(referButton);

		// Set properties for the new buttons
		var rejectURL = location.protocol+ "//"+location.host+ "/test/" + specID+ "/reject";
		parent.children('.reject-specimen').attr('id',"reject-" + testID + "-link");
		parent.children('.reject-specimen').attr('href', rejectURL);

		var referURL = location.protocol+ "//"+location.host+ "/test/" + specID+ "/refer";
		parent.children('.refer-button').attr('href', referURL);

		parent.children('.start-test').attr('data-test-id', testID);

		// Now remove the unnecessary buttons
		$(this).siblings('.change-specimen').remove();
		$(this).remove();
		*/
	});

	/**
	 * Automatic Results Interpretation
	 * Updates the test  result via ajax call
	 */
	 /*UNSTABLE!---TO BE RE-THOUGHT
	$(".result-interpretation-trigger").focusout(function() {
		var interpretation = "";
		var url = $(this).data('url');
		var measureid = $(this).data('measureid');
		var age = $(this).data('age');
		var gender = $(this).data('gender');
		var measurevalue = $(this).val();
		$.post(url, {
				measureid: measureid,
				age: age,
				measurevalue: measurevalue,
				gender: gender
			}).done( function( interpretation ){
			$( ".result-interpretation" ).val( interpretation );
		});
	});
	*/

	/** Start Test button.
	 *  - Updates the Test status via an AJAX call
	 *  - Changes the UI to show the right status and buttons
	 */
	$('.tests-log').on( "click", ".start-test", function(e) {

		var testID = $(this).data('test-id');
		var url = $(this).data('url');
		$.post(url, { id: testID}).done(function(response){

			if (response >= 0){
				window.location.reload();
				//window.location = window.location + (window.location.href.match(/\?/) ? "&" : '?') + "machine_test_id=" + testID;
			}else{
				window.location.reload();
			}
		});

		/*
		var parent = $(e.currentTarget).parent();
		// First replace the status
		var newStatus = $('.started-test-accepted-specimen').html();
		parent.siblings('.test-status').html(newStatus);

		// Add the new buttons
		var newButtons = $('.enter-result-buttons').html();
		parent.append(newButtons);

		// Set properties for the new buttons
		var resultURL = location.protocol+ "//"+location.host+ "/test/" + testID+ "/enterresults";
		parent.children('.enter-result').attr('id',"enter-results-" + testID + "-link");
		parent.children('.enter-result').attr('href',resultURL);

		// Now remove the unnecessary buttons
		$(this).siblings('.refer-button').remove();
		$(this).remove();
		*/
	});

	/**
	 *-----------------------------------
	 * REPORTS
	 *-----------------------------------
	 */

		/*Dynamic loading of select list options*/
		$('#section_id').change(function(){
			$.get("/reports/dropdown", 
				{ option: $(this).val() }, 
				function(data) {
					var test_type = $('#test_type');
					test_type.empty();
					test_type.append("<option value=''>Select Test Type</option>");
					$.each(data, function(index, element) {
			            test_type.append("<option value='"+ element.id +"'>" + element.name + "</option>");
			        });
				});
		});
		/*End dynamic select list options*/
				/*Dynamic loading of select list options*/
		$('#commodity-id').change(function(){
			$.get("/topup/"+$(this).val()+"/availableStock", 
				function(data) {
					$('#current_bal').val(data.availableStock);
				});
		});
		/*End dynamic select list options*/
		
		/*Toggle summary div for reports*/
		$('#reveal').click(function(){
			if ( $('#summary').hasClass('hidden')) {
					$('#summary').removeClass('hidden');
			}else {
				$('#summary').addClass('hidden');
			}
		});



});
	/**
	 *-----------------------------------
	 * Section for AJAX loaded components
	 *-----------------------------------
	 */
	$( document ).ajaxComplete(function() {
		/* - Identify the selected patient by setting the hidden input field
		   - Enable the 'Next' button on the modal
		*/
		$('#new-test-modal .table input[type=radio]').click(function(){
			$('#new-test-modal #patient_id').val($(this).val());
			$('#new-test-modal .modal-footer .next').prop('disabled', false);

		});
		/* - Check the radio button when the row is clicked
		   - Identify the selected patient by setting the hidden input field
		   - Enable the 'Next' button on the modal
		*/
		$('#new-test-modal .patient-search-result tr td').click(function(){
			var theRadio = $(this).parent().find('td input[type=radio]');
			theRadio.prop("checked", true);
			$('#new-test-modal #patient_id').val(theRadio.val());
			$('#new-test-modal .modal-footer .next').prop('disabled', false);
		});
	});

/**
 *
 **/
 	function __$(id){
		return document.getElementById(id);
	}

	/**
	 *	Lab Configurations Functions
	 */
	function addNewSurveillanceAttributes (newSurveillanceNo) {
		$('.new-surveillance-'+newSurveillanceNo).find('select.test-type').attr(
			'name', 'new-surveillance['+newSurveillanceNo+'][test-type]');
		$('.new-surveillance-'+newSurveillanceNo).find('select.disease').attr(
			'name', 'new-surveillance['+newSurveillanceNo+'][disease]');
	}

	function addNewDiseaseAttributes (newDiseaseNo) {
		$('.new-disease-'+newDiseaseNo).find('input.disease').attr(
			'name', 'new-diseases['+newDiseaseNo+'][disease]');
	}

	/**
	 *	Measure Functions
	 */
	function loadRangeFields () {
		var headerClass = [
			'.numericHeaderLoader',
			'.alphanumericHeaderLoader',
			'.alphanumericHeaderLoader',
			'.freetextHeaderLoader'
		]; 
		var inputClass = [
			'.numericInputLoader',
			'.alphanumericInputLoader',
			'.alphanumericInputLoader',
			'.freetextInputLoader'
		];

		if ($(this).data('measure-id') === 0) {
			var newMeasureId = $(this).data('new-measure-id');
			var measureID = 'new-measure-'+newMeasureId;
		} else {
			var measureID = $(this).data('measure-id');
		}

			measureTypeId = $('.measuretype-input-trigger.'+measureID).val() - 1;
			var headerHtml = $(headerClass[measureTypeId]).html();
			var inputHtml = $(inputClass[measureTypeId]).html();
		//if (measureTypeId == 0) {
		//	$('.measurevalue.'+measureID).removeClass('col-md-12');
		//	$('.measurevalue.'+measureID).addClass('col-md-12');
		//} else{
		//	$('.measurevalue.'+measureID).removeClass('col-md-12');
		//	$('.measurevalue.'+measureID).addClass('col-md-12');
		//}
		if (measureTypeId == 3) {
			$('.measurevalue.'+measureID).siblings('.actions-row').addClass('hidden')
		}else{
			$('.measurevalue.'+measureID).siblings('.actions-row').removeClass('hidden')
		}
		$('.measurevalue.'+measureID).empty();
		$('.measurevalue.'+measureID).append(headerHtml);
		$('.measurevalue.'+measureID).append(inputHtml);
		if ($(this).data('measure-id') != 0) {
			editMeasureRangeAttributes(measureTypeId,measureID);
		}else{
			addMeasureRangeAttributes(measureTypeId, newMeasureId);
		}
	}

	function addNewMeasureAttributes (measureID) {
		$('.measure-section.new-'+measureID+' input.name').attr(
			'name', 'new-measures['+measureID+'][name]');
		$('.measure-section.new-'+measureID+' select.measure_type_id').attr(
			'name', 'new-measures['+measureID+'][measure_type_id]');
		$('.measure-section.new-'+measureID+' input.unit').attr(
			'name', 'new-measures['+measureID+'][unit]');
		$('.measure-section.new-'+measureID+' input.expected').attr(
			'name', 'new-measures['+measureID+'][expected]');
		$('.measure-section.new-'+measureID+' textarea.description').attr(
			'name', 'new-measures['+measureID+'][description]');
	}

	function addMeasureRangeAttributes (measureTypeId,measureID) {
		if (measureTypeId == 0) {
			$('.measurevalue.new-measure-'+measureID+' input.agemin').attr(
				'name', 'new-measures['+measureID+'][agemin][]');
			$('.measurevalue.new-measure-'+measureID+' input.agemax').attr(
				'name', 'new-measures['+measureID+'][agemax][]');
			$('.measurevalue.new-measure-'+measureID+' select.gender').attr(
				'name', 'new-measures['+measureID+'][gender][]');
			$('.measurevalue.new-measure-'+measureID+' input.rangemin').attr(
				'name', 'new-measures['+measureID+'][rangemin][]');
			$('.measurevalue.new-measure-'+measureID+' input.rangemax').attr(
				'name', 'new-measures['+measureID+'][rangemax][]');
			$('.measurevalue.new-measure-'+measureID+' input.interpretation').attr(
				'name', 'new-measures['+measureID+'][interpretation][]');
			$('.measurevalue.new-measure-'+measureID+' input.measurerangeid').attr(
				'name', 'new-measures['+measureID+'][measurerangeid][]');
		} else{
			console.log("Am in here");
			$('.measurevalue.new-measure-'+measureID+' input.val').attr(
				'name', 'new-measures['+measureID+'][val][]');
			$('.measurevalue.new-measure-'+measureID+' input.interpretation').attr(
				'name', 'new-measures['+measureID+'][interpretation][]');
			$('.measurevalue.new-measure-'+measureID+' input.measurerangeid').attr(
				'name', 'new-measures['+measureID+'][measurerangeid][]');
            $('.measurevalue.new-measure-'+measureID+' input.hl7_identifier').attr(
							'name', 'new-measures['+measureID+'][hl7_identifier][]');
			$('.measurevalue.new-measure-'+measureID+' input.hl7_text').attr(
							'name', 'new-measures['+measureID+'][hl7_text][]');
			$('.measurevalue.new-measure-'+measureID+' input.hl7_coding_system').attr(
							'name', 'new-measures['+measureID+'][hl7_coding_system][]');
		}
	}

	function editMeasureRangeAttributes (measureTypeId,measureID) {
		if (measureTypeId == 0) {
			$('.measurevalue.'+measureID+' input.agemin').attr(
				'name', 'measures['+measureID+'][agemin][]');
			$('.measurevalue.'+measureID+' input.agemax').attr(
				'name', 'measures['+measureID+'][agemax][]');
			$('.measurevalue.'+measureID+' select.gender').attr(
				'name', 'measures['+measureID+'][gender][]');
			$('.measurevalue.'+measureID+' input.rangemin').attr(
				'name', 'measures['+measureID+'][rangemin][]');
			$('.measurevalue.'+measureID+' input.rangemax').attr(
				'name', 'measures['+measureID+'][rangemax][]');
			$('.measurevalue.'+measureID+' input.interpretation').attr(
				'name', 'measures['+measureID+'][interpretation][]');
			$('.measurevalue.'+measureID+' input.measurerangeid').attr(
				'name', 'measures['+measureID+'][measurerangeid][]');
		} else{
			$('.measurevalue.'+measureID+' input.val').attr(
				'name', 'measures['+measureID+'][val][]');
			$('.measurevalue.'+measureID+' input.interpretation').attr(
				'name', 'measures['+measureID+'][interpretation][]');
			$('.measurevalue.'+measureID+' input.hl7_identifier').attr(
							'name', 'measures['+measureID+'][hl7_identifier][]');
			$('.measurevalue.'+measureID+' input.hl7_text').attr(
							'name', 'measures['+measureID+'][hl7_text][]');
			$('.measurevalue.'+measureID+' input.hl7_coding_system').attr(
							'name', 'measures['+measureID+'][hl7_coding_system][]');
			$('.measurevalue.'+measureID+' input.measurerangeid').attr(
				'name', 'measures['+measureID+'][measurerangeid][]');
		}
	}

	function UIComponents(){
		/* Datepicker */
		$( '.standard-datepicker').datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			maxDate: new Date()
		});

		//$(".datepicker").attr('readonly', 'readonly');
		/* Datepicker */
		$( '.datepicker').datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true
		});
	}

	function editUserProfile()
	{
		/*If Password-Change Validation*/
	    var currpwd = $('#current_password').val();
	    var newpwd1 = $('#new_password').val();
	    var newpwd2= $('#new_password_confirmation').val();
	    var newpwd1_len = newpwd1.length;
	    var newpwd2_len = newpwd2.length;
	    var error_flag = false;
	    if(currpwd == '')
	    {
	        $('.curr-pwd-empty').removeClass('hidden');
	        error_flag = true;
	    }
	    else
	    {
	        $('.curr-pwd-empty').addClass('hidden');
	    }

	    if(newpwd1 == '')
	    {
	        $('.new-pwd-empty').removeClass('hidden');
	        error_flag = true;
	    }
	    else
	    {
	        $('.new-pwd-empty').addClass('hidden');
	    }
	    if(newpwd2 == '')
	    {
	        $('.new-pwdrepeat-empty').removeClass('hidden');
	        error_flag = true;
	    }
	    else
	    {
	        $('.new-pwdrepeat-empty').addClass('hidden');
	    }
	    
	    if(!error_flag)
	    {
	        if(newpwd1_len != newpwd2_len || newpwd1 != newpwd2)
	        {
	            $('.new-pwdmatch-error').removeClass('hidden');
	            error_flag = true;
	        }
	        else
	        {
	            $('.new-pwdmatch-error').addClass('hidden');
	        }
	    }
	    if(!error_flag)
	    {
	        $('#form-edit-password').submit();
	    }
	}

	//DataTables search functionality
	$(document).ready( function () {
		$('.search-table').DataTable({
        	'bStateSave': true,
        	'fnStateSave': function (oSettings, oData) {
            	localStorage.setItem('.search-table', JSON.stringify(oData));
        	},
        	'fnStateLoad': function (oSettings) {
            	return JSON.parse(localStorage.getItem('.search-table'));
        	}
   		});

		$('.pre-select:first').click();

	});

	//Make sure all input fields are entered before submission
	function authenticate (form) {
    	var empty = false;
		$('form :input:not(button)').each(function() {

            if ($(this).val() == '') {
                empty = true;
	            $('.error-div').removeClass('hidden');
            }
	        if (empty) return false;
	    });
        if (empty) return;
	    $(form).submit();
	}

	function saveObservation(tid, user, username){
		txtarea = "observation_"+tid;
		observation = $("#"+txtarea).val();

		$.ajax({
			type: 'POST',
			url:  '/culture/storeObservation',
			data: {obs: observation, testId: tid, userId: user, action: "add"},
			success: function(){
				drawCultureWorksheet(tid , user, username);
			}
		});
	}
	/**
	 * Request a json string from the server containing contents of the culture_worksheet table for this test
	 * and then draws a table based on this data.
	 * @param  {int} tid      Test Id of the test
	 * @param  {string} username Current user
	 * @return {void}          No return
	 */
	function drawCultureWorksheet(tid, user, username){
		c
		$.getJSON('/culture/storeObservation', { testId: tid, userId: user, action: "draw"}, 
			function(data){
				var tableBody ="";
				$.each(data, function(index, elem){
					tableBody += "<tr>"
					+" <td>"+elem.timeStamp+" </td>"
					+" <td>"+elem.user+"</td>"
					+" <td>"+elem.observation+"</td>"
					+" <td> </td>"
					+"</tr>";
				});
				tableBody += "<tr>"
					+"<td>0 seconds ago</td>"
					+"<td>"+username+"</td>"
					+"<td><textarea id='observation_"+tid+"' class='form-control result-interpretation' rows='2'></textarea></td>"
					+"<td><a class='btn btn-xs btn-success' href='javascript:void(0)' onclick='saveObservation("+tid+", &quot;"+user+"&quot;, &quot;"+username+"&quot;)'>Save</a></td>"
					+"</tr>";
				$("#tbbody_"+tid).html(tableBody);
			}
		);
	}

	/*Begin save drug susceptibility*/	
	function saveDrugSusceptibility(tid, oid){

		var dataString = $("#drugSusceptibilityForm_"+oid).serialize();
		dataString = dataString + "&organism=" + oid;

		$.ajax({
			type: 'POST',
			url:  '/susceptibility/saveSusceptibility',
			data: dataString,
			success: function(){
				drawSusceptibility(tid, oid);
			}
		});
	}
	/*End save drug susceptibility*/
	/*Function to render drug susceptibility table after successfully saving the results*/
	 function drawSusceptibility(tid, oid){
		$.getJSON('/susceptibility/saveSusceptibility', { testId: tid, organismId: oid, action: "results"}, 
			function(data){

				var tableRow ="";
				var tableBody ="";
				$.each(data, function(index, elem){
					tableRow += "<tr>"
					+" <td>"+elem.drugName+" </td>"
					+" <td>"+elem.zone+"</td>"
					+" <td>"+elem.interpretation+"</td>"
					+"</tr>";
					$(".sense"+tid).val($(".sense"+tid).val()+elem.drugName+" - "+elem.sensitivity+", ");
				});
				//tableBody +="<tbody>"+tableRow+"</tbody>";
				$( "#enteredResults_"+oid).html(tableRow);
				$("#submit_drug_susceptibility_"+oid).hide();
			}
		);
	}

	function cancelSelectedOrganism(id,test_id)
	{  
		var url = '/cancelSelectedOrganims?organismId=' + id + '&test_id=' + test_id;
		jQuery.ajax({ async: true,
					  url : url,
					  success : function()
					  {
					  		$("#organism"+id).hide();
					  },
					  error : function()
					  {

					  }
		})
		
	}
	function deleteDrugSusceptibility(tid, oid){
		$.getJSON('/susceptibility/saveSusceptibility', { testId: tid, organismId: oid, action: "delete"},
			function(data){
				var form = document.getElementById("drugSusceptibilityForm_"+oid);
				form.reset();
				form.style.display = "none";
				$( "#organism"+oid+" input").prop("checked", false);
				$( "#organism"+oid).hide();
			}
		);
	}
	/*End drug susceptibility table rendering script*/
	/*Function to toggle possible isolates*/
	function toggle(className, obj){
		var $input = $(obj);
		if($input.prop('checked'))
			$(className).show();
		else
			$(className).hide();
	}
	/*End toggle function*/
	/*Toggle susceptibility tables*/
	function showSusceptibility(id){
		$('#drugSusceptibilityForm_'+id).toggle(this.checked);
	}
	/*End toggle susceptibility*/

	function addDrugSusceptibility(tid, oid){

		$.getJSON('/susceptibility/saveSusceptibility', { testId: tid, organismId: oid, action: "row-values"},
			function(data){
				var drugs = data['drugs'];
				var oid = data['oid'];
				var tr = document.createElement("tr");
				var td1 = document.createElement('td');
				var dSelect = document.createElement('select');
				td1.appendChild(dSelect);
				tr.appendChild(td1);
				dSelect.setAttribute('name', 'new_drug[]');
				dSelect.className = 'form-control';
				dSelect.style.width = 'auto';
				var option = document.createElement('option');
				option.setAttribute('value', '');
				option.innerHTML = '';
				dSelect.appendChild(option);
				for(var i in drugs){
					var option = document.createElement('option');
					option.setAttribute('value', i);
					option.innerHTML = drugs[i];
					dSelect.appendChild(option);
				}

				var td2 = document.createElement('td');
				var zSelect = document.createElement('select');
				zSelect.className = 'form-control';
				td2.appendChild(zSelect);
				tr.appendChild(td2);
				zSelect.setAttribute('name', 'new_zone[]');
				zSelect.style.width = 'auto';
				for(var i = 0; i < 50; i++){
					var option = document.createElement('option');
					option.setAttribute('value', i);
					option.innerHTML = i;
					zSelect.appendChild(option);
				}

				var td3 = document.createElement('td');
				var iSelect = document.createElement('select');
				td3.appendChild(iSelect);
				tr.appendChild(td3);
				iSelect.setAttribute('name', 'new_interp[]');
				iSelect.className = 'form-control';
				iSelect.style.width = 'auto';
				var interp = ['', 'S', 'I', 'R'];

				for(var i = 0; i < interp.length; i++){
					var option = document.createElement('option');
					option.setAttribute('value', interp[i]);
					option.innerHTML = interp[i];
					iSelect.appendChild(option);
				}

				var nodes = document.getElementById('enteredResults_' + oid).childNodes;

				document.getElementById('enteredResults_' + oid).insertBefore(tr, nodes[nodes.length - 2]);
			}
		);

	}

	/*Get valid wards/locations to populate select tag on test ordering page*/
	function loadWards($visit_type){
		$.getJSON('/visittype/getWards', { visittype: $visit_type},
			function(data){

				$("#ward").empty();
				var option = document.createElement("option");
				option.setAttribute('value', ' ');
				option.innerHTML = "--- Select ward/location ---";
				$("#ward").append(option);

				for (var i = 0; i < data.length; i++){

					if (data[i]['name'].match(/facilities/i)){
						continue;
					}

					var option = document.createElement("option");
					option.setAttribute('value', data[i]['name']);
					option.innerHTML = data[i]['name'];
					$("#ward").append(option);
				}
			}
		);
	}

	function loadTestTypes($specimen_id){

		$.getJSON('/specimentype/getTestTypes', { specimentype: $specimen_id},
			function(data){

				var t = $('#testtypes').DataTable();
				t.clear().draw();

				for (var i in data){
					var text = data[i];
					var value = i;

					if (data[i]['name']){
						text = data[i]['name'];
						value = data[i]['name'];
					}

					t.row.add(
						[
							text,
							("<input type='checkbox' name='testtypes[]' value='" + value + "' />")
						]
					);
					t.draw();
				}

			}
		);
	}

	function flipPanelRows(id){

		$(".panel-header"+id).toggleClass('panel-selected');
		$(".panel-row:not(.panel"+ id +")").hide();
		$(".panel"+id).slideToggle(100);
		$(".panel"+id).css({
			'border-right' : '5px solid orange',
			'border-left' : '2px solid #428bca'
		});
		$(".panel"+id+":last").css({
			'border-bottom' : '2px solid #428bca'
		});
		$(".panel"+id+":first").css({
			'border-top' : '2px solid #428bca'
		})
	}

	function showCultureOrganisms(){
		$('#organismsModel').modal("show");
	}

	function hideCultureOrganisms(){
		$('#organismsModel').modal("hide");
	}

	function searchOrganisms(text){

		jQuery("#organismsModel .checkbox").hide();
		jQuery("#organismsModel .checkbox:Contains('" +text + "')").show();
	}

	function sendOrganism(test_id)
	{ 
		var values =[];
		var counter=0;
		$(".organism-option:checkbox:checked").each(function() {
			values[counter] = $(this).val();
			counter++;				
		});

		
		if (values.length >0)
		{   var url = '/postOrganisms?ids=' + values +'&test_id=' + test_id;
		
			jQuery.ajax({
						async: true,
						url: url,
						success : function(res)
						{
							displayOrganisms();
						},
						error : function(err)
						{

						}

				});
		}

	}


	function editOrganism(test_id)
	{
		var values =[];
		var counter=0;
		$(".organism-option:checkbox:checked").each(function() {
			values[counter] = $(this).val();
			counter++;				
		});
		
		if (values.length >0)
		{   var url = '/editOrganisms?ids=' + values +'&test_id=' + test_id;
		
			jQuery.ajax({
						async: true,
						url: url,
						success : function(res)
						{
							displayOrganisms();
						},
						error : function(err)
						{

						}

				});
		}


	}

	function displayOrganisms(){
		
		var nodes = $(".organism-option:checkbox:checked").each(function() {
			$('#organism'+$(this).val()).show();
		});
	}

	function submitPrintForm(){
		if(document.getElementById("word")) {
			document.getElementById("word").value = "true";
		}

		var pdf = document.createElement("input");
		pdf.setAttribute("type", "hidden");
		pdf.setAttribute("name", "pdf");
		pdf.setAttribute("value", "true");
		document.getElementById('form-patientreport-filter').appendChild(pdf);

		document.getElementById('form-patientreport-filter').submit();
		$('#myModal').modal('hide');
	}

	function updateValue(obj){
		obj.getElementsByTagName('input')[0].checked = true
		document.getElementById("printer_name").value = obj.getAttribute('value');
	}

	function selectPrinter(){
		$("#printer_name").value = '';
		$(".printer_radio_button").prop( "checked", false );//Uncheck printer
		$('#myModal').modal('show');
	}

	function unsetPrinterValue()
	{
		document.getElementById("printer_name").value = '';
	}

	function toggleMainOpt(test_id){
			jQuery('.opt-view-' + test_id).toggle();
			jQuery('.main-view-' + test_id).toggle();
	}

	function printPackDetails(tid){
		window.location = "/test/" + tid + "/print_pack_details";
	}


	var spinner;

	function showSpinner(action, clickOnClose, shieldOn) {

		console.log(window.location);
		if(window.location.href.match(/viewdetails|checkResult|patientreport|verify/i) || window.location.href.match(/print|machine\_test\_id\=\d+$/)) {
			hideSpinner();
			if ($('.modal')){
				$('.modal').modal('hide');
			}
			return;
		}

		if (!document.getElementById('spin')) {
			var div = document.createElement("div");
			div.id = "spin";
			div.style.position = "absolute";
			div.style.top = ((window.innerHeight / 2) - 80) + "px";
			div.style.left = ((window.innerWidth / 2)) + "px";

			div.innerHTML = '<img src="data:image/gif;base64,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"/>';

			document.body.appendChild(div);
		}

		if (shieldOn == true) {
			showShield(action, clickOnClose);
		}
	}


	function showShield(action, clickCloses) {

		if (clickCloses == undefined) {

			clickCloses = true;

		}

		if (__$("shield")) {

			if (__$("popup")) {

				document.body.removeChild(__$("popup"));

			}

			if (__$("popupkeyboard")) {

				if (action != undefined) {

					eval(action);

				}

				document.body.removeChild(__$('popupkeyboard'));

			}

			document.body.removeChild(__$("shield"));

		} else {

			var shield = document.createElement("div");
			shield.style.position = "absolute";
			shield.style.backgroundColor = "rgba(0,0,0,0.1)";
			shield.style.top = "0px";
			shield.style.left = "0px";
			shield.style.width = "100%";
			shield.style.height = "100%";
			shield.id = "shield";

			if (action != undefined) {

				shield.setAttribute("action", action);

			}

			if (clickCloses) {

				shield.onmousedown = function () {

					if (this.getAttribute("action") != null) {
						showShield(this.getAttribute("action"));
					} else {
						showShield();
					}

				}

			}

			document.body.appendChild(shield);

		}

	}

	function hideSpinner() {


		if (__$("spin")) {

			document.body.removeChild(__$("spin"));

		}

		if (__$("shield")) {

			document.body.removeChild(__$("shield"));

		}

	}

	jQuery.expr[":"].Contains = jQuery.expr.createPseudo(function(arg) {
		return function( elem ) {
			return jQuery(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
		};
	});

	(function(open) {

		XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {
			if (!url.match(/checkresult|print|checkMachineResult/i)) {
				setTimeout(function () {
					showSpinner(null, false, false)
				}, 10);
			}
			
			this.addEventListener("load", function() {
				hideSpinner();
			}, false);

			open.call(this, method, url, async, user, pass);
		};

	})(XMLHttpRequest.prototype.open);

	window.onbeforeunload = function(){

		setTimeout(function(){showSpinner(null, false, true)}, 10);

	};

	document.addEventListener('DOMContentLoaded', function() {

		hideSpinner();

		if(window.location.href.match(/test\/\d+\/enterresults|test\/\d+\/edit/i)){
			checkMachineOutput();
		}

		if(window.location.href.match(/test/)){

			checkingMachineResults();
		}

	});

	function checkBarcode(){
		var node = document.getElementsByClassName('barcode')[0];
		if(node){

			if (node.value.match(/\$$/)){
				node.value =node.value.replace(/\$$/, "");
				node.form.submit();
			}else{
				if($(".force-open").size() == 0) {
					//node.focus();
				}
				setTimeout(function(){
					checkBarcode();
				}, 200);
			}
		}
	}
	
	setTimeout(function(){
		checkBarcode();
	}, 1000);

	function checkingMachineResults()
	{ 
		var buttons = __$('hider');	
		var info = buttons.getAttribute('data').split(',');		
		var id_number = "";
		var splited_data=[];
		var availableResults = [];
		var url = '/checkMachineResults';
		jQuery.ajax({
			async : true,
			url : url,
			success : function(results)
			{	
				availableResults = results;

				for(var counter=0; counter<info.length;counter++)
				{	var va = info[counter]; 
					
					splited_data = va.split('_');
					id_number = splited_data[0]+".json";
					if(availableResults.includes(id_number))
					{	
						var elmnt = __$(va);
						var lab = 'sp'+ va;
						var lbl = __$(lab);						
						elmnt.style.display = 'block';
						elmnt.style.color='green';
						lbl.style.display = 'none';
					}
				 	else
				 	{	var elmnt = __$(ele);
						elmnt.style.display = 'none';
				 	}					 	
				}		
			},
			error : function(err)
			{

			}
		})

		/*
		$.get('/checkMachineResults').done(function(result){
			availableResults = result;

			for(var counter=0; counter<info.length;counter++)
			{	var va = info[counter]+".json"; 
				var ele = info[counter];
				if(availableResults.includes(va))
				{
					var elmnt = __$(ele);
					elmnt.style.display = 'block';
					elmnt.style.color='green';
				}
			 	else
			 	{
					var elmnt = __$(ele);
					elmnt.style.display = 'none';
			 	}					 	
			}		
		});
		*/

		setTimeout("checkingMachineResults()",300);
	}

	function checkMachineOutput(){
		var button = __$('fetch-link');
		var accessionNumber = button.getAttribute('data-accession-number');
		if(accessionNumber){
			$.get("/instrument/checkresult?accession_number="+accessionNumber).done(function(result){

					if(result.match(/true/i)) {
						button.className = "btn btn-sm btn-success fetch-test-data";
						button.removeAttribute('disabled');

						//__$('machine-status').innerHTML = 'Machine results available';
					}else{
						button.className = "btn btn-sm btn-default fetch-test-data";
						button.setAttribute('disabled', true);
						//__$('machine-status').innerHTML = 'Waiting for machine results....';
					}
			});
		}
		setTimeout("checkMachineOutput()",300);
	}