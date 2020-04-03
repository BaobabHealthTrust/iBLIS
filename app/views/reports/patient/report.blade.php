@extends("layout")
@section("content")
<div>
	<ol class="breadcrumb">
	  <li><a href="{{{URL::route('user.home')}}}">{{ trans('messages.home') }}</a></li>
	  <li class="active"><a href="{{ URL::route('reports.patient.index') }}">{{ Lang::choice('messages.report', 2) }}</a></li>
	  <li class="active">{{ trans('messages.patient-report') }}</li>
	</ol>
</div>
<div class='container-fluid'>
	
		
	@if(empty($visit))
    	{{ Form::open(array('url' => 'patientreport/'.$patient->id, 'class' => 'form-inline', 'id' => 'form-patientreport-filter', 'method'=>'POST')) }}
	@else
		{{ Form::open(array('url' => 'patientreport/'.$patient->id.'/'.$visit, 'class' => 'form-inline', 'id' => 'form-patientreport-filter', 'method'=>'POST')) }}
	@endif

		{{ Form::hidden('patient', $patient->id, array('id' => 'patient')) }}
		{{ Form::hidden('printer_name', '', array('id' => 'printer_name')) }}
		{{ Form::hidden('pdf', '', array('id' => 'word')) }}

		<div class="row">
			<div class="col-sm-3">
				<label class="checkbox-inline">
	        		{{ Form::checkbox('pending', "1", isset($pending)) }}{{trans('messages.include-pending-tests')}}
				</label>
			</div>
			<div class="col-sm-3">
				<div class="row">
					<div class="col-sm-2">
						{{ Form::label('start', trans("messages.from")) }}</div><div class="col-sm-1">
			        	{{ Form::text('start', isset($input['start'])?$input['start']:null,
			                array('class' => 'form-control standard-datepicker')) }}
			        </div>
		        </div>
	        </div>
	        <div class="col-sm-3">
				<div class="row">
			        <div class="col-sm-2">
				        {{ Form::label('end', trans("messages.to")) }}
				    </div>
				    <div class="col-sm-1">
		                {{ Form::text('end', isset($input['end'])?$input['end']:null,
		                    array('class' => 'form-control standard-datepicker')) }}
		            </div>
	            </div>
            </div>
            <div class="col-sm-3">
				<div class="row">
		            <div class="col-sm-3">
			            {{ Form::button("<span class='glyphicon glyphicon-filter'></span> ".trans('messages.view'),
			                    array('class' => 'btn btn-primary', 'id' => 'filter', 'type' => 'submit')) }}
		            </div>


		            @if(count($verified) == count($tests))
		            <div class="col-sm-1">
				        {{ Form::button(trans('messages.print'), array('class' => 'btn btn-success',
				        	'onclick' => "selectPrinter()")) }}
				    </div>
					<div class="col-ms-1">
						<a class="btn btn-sm btn-primary pull-right"  href="#" onclick="window.history.back();return false;"
						   alt="{{trans('messages.back')}}" title="{{trans('messages.back')}}">
							<span class="glyphicon glyphicon-backward"></span></a>
					</div>
				    @endif
			    </div>
		    </div>


	    </div>
	    {{ Form::hidden('visit_id', $visit, array('id'=>'visit_id')) }}
	{{ Form::close() }}
</div>
<br />
<div class="panel panel-primary" id="patientReport">
	<div class="panel-heading ">
		<span class="glyphicon glyphicon-user"></span>
		{{ trans('messages.patient-report') }}
	</div>
	<div class="panel-body">
		@if($error!='')
		<!-- if there are search errors, they will show here -->
			<div class="alert alert-info">{{ $error }}</div>
		@else

		<div id="report_content">
		@include("reportHeader")
		<strong>
			<p>
				{{trans('messages.patient-report').' - '.date('d-m-Y')}}

				<b style="padding-left: 10%;"><i> {{ 'No. Printed:  '. $print_status }}</i></b> 
			</p>

		</strong>
		<table class="table table-bordered">
			<tbody>
				<tr>
					<th>{{ trans('messages.patient-name')}}</th>
					@if(Entrust::can('view_names'))
						<td>{{ $patient->name }}</td>
					@else
						<td>N/A</td>
					@endif
					<th>{{ trans('messages.gender')}}</th>
					<td>{{ $patient->getGender(false) }}</td>
					<th>{{ trans('messages.age')}}</th>
					<td>{{ $patient->getAge()}}</td>
				</tr>

				<?php
				$test = array();
				if(!empty($tests[0])){
					$test = $tests[0];
				}
				?>


				<tr>
					<th>{{trans('messages.patient-id')}}</th>
					<td>{{ $patient->external_patient_number }}</td>

					<th colspan="2">Physical Address</th>
					<td colspan="2">{{ $patient->address }}</td>

				</tr>
			</tbody>
		</table>

		@if($patient_visits < 1)

			@if($checking_status == true)
				<p style="color:red;"> patient has no tests </p>
			@else
				<p style="color:red;"> patient tests not verified or not completed </p>
			@endif

		@else

			@forelse($data as $accession_number => $tests)
				<?php
				$rejected = Specimen::REJECTED;
				$specimen = Specimen::where('accession_number', '=', $accession_number)->first();
				$status = DB::select(DB::raw("SELECT specimens.id FROM specimens WHERE specimen_status_id='$rejected' AND specimens.accession_number='$accession_number'"));
				?>

	@if(count($status)==0)
				<div class="panel panel-success">
						<div class="panel-heading ">
							<span class="glyphicon glyphicon-tint"></span>
							<span><strong>{{Lang::choice('messages.specimen-id', 1)}}</strong>&nbsp;:&nbsp;  <strong> {{ $specimen->accession_number }}</strong></span>

							<span class="pull-right"><strong>Requested By </strong>&nbsp;:&nbsp;  <strong> {{ $test->requested_by }}
									({{$test->visit->ward_or_location or trans('messages.unknown') }})</strong></span>
						</div>
				<div class="panel-body">

			<table class="table table-bordered rspecimen">
				<tbody>

					<tr>
						<td><strong>{{Lang::choice('messages.specimen-type', 1)}}</strong></td>
						<td>{{ $specimen->specimenType->name }}</td>

						<td><strong>{{Lang::choice('messages.date-ordered', 1)}}</strong></td>
						<td>{{	$test ? $test->isExternal()?$test->external()->request_date:$test->time_created : ''}}</td>
					</tr>

					<tr>
						<td><strong>{{Lang::choice('messages.specimen-tests-ordered', 1)}}</strong></td>
						<td>{{ $specimen->testTypes() }}</td>

						<td><strong>{{Lang::choice('messages.test-category', 2)}}</strong></td>
						<td>{{ $specimen->labSections() }}</td>
					</tr>

					<tr>
						<td><strong>{{trans('messages.ordered-specimen-status')}}</strong></td>
						@if($specimen->specimen_status_id == Specimen::NOT_COLLECTED)
							<td>{{trans('messages.specimen-not-collected')}}</td>
						@elseif($specimen->specimen_status_id == Specimen::ACCEPTED)
							<td>{{trans('messages.specimen-accepted')}}</td>
						@elseif($specimen->specimen_status_id == Specimen::REJECTED)
							<td>{{trans('messages.specimen-rejected')}}</td>
						@endif

						@if($specimen->specimen_status_id == Specimen::ACCEPTED)
							<td><strong>{{ trans('messages.collected-by') }}</strong></td>
						@elseif($specimen->specimen_status_id == Specimen::REJECTED)
							<td><strong>{{ trans('messages.rejected-by') }}</strong></td>
						@endif

						@if($specimen->specimen_status_id == Specimen::NOT_COLLECTED)
							<td></td>
						@elseif($specimen->specimen_status_id == Specimen::ACCEPTED)
							<td>{{$specimen->getUserAcceptedSpecimen($specimen->id)}}</td>
						@elseif($specimen->specimen_status_id == Specimen::REJECTED)
							<td>{{$specimen->rejectedBy->name}}</td>
						@endif
					</tr>

					@if(count($verified) == count($tests))
						<tr>
							<td><strong>{{trans('messages.verified-by')}}</strong></td>
							<td>{{ $test->verifiedBy->name or trans('messages.verification-pending')}}</td>
							<td><strong> {{trans('messages.date-verified')}}</strong></td>
							<td>{{ $test->time_verified }}</td>
						</tr>
					@endif
				</tbody>
			</table>
		<table class="table table-bordered rtest">
			<tbody>
				<tr>
					<th colspan="8">{{trans('messages.test-results')}}
						{{(count($verified) != count($tests)) ?	('<span class="pull-right"><i>'.trans('messages.verification-pending')).'</i></span>' : ''}}</th>
				</tr>
				<tr>
					<th>{{Lang::choice('messages.test-type', 1)}}</th>
					<th>{{trans('messages.test-results')}}</th>
					<th>{{trans('messages.test-remarks')}}</th>					
					<th>{{trans('messages.tested-by')}}</th>
				</tr>
				<?php

				$sorted_tests = Array();
				$predefined_order = Array();
			

				if (in_array("CSF Analysis", explode(', ', $specimen->testTypes()))){
					$predefined_order = Array("Cell Count", "India Ink", "Gram Stain", "Differential", "Culture & Sensitivity");
				}

				if (in_array("Urinalysis", explode(', ', $specimen->testTypes()))){
					$predefined_order = Array("Urine Macroscopy", "Urine Microscopy", "Urine Chemistries");
				}

				if (in_array("Sterile Fluid Analysis", explode(', ', $specimen->testTypes()))){
					$predefined_order = Array("Cell Count", "Gram Stain", "ZN Stain", "Differential", "Culture & Sensitivity");
				}

				foreach($predefined_order AS $order){
					foreach($tests AS $t){
						if($order == $t->testType->name){
							array_push($sorted_tests, $t);
						}
					}
				}

				$tests = array_unique(array_merge($sorted_tests, $tests));

				?>
			
				@forelse($tests as $test)
						<tr>
							<td>{{ $test->testType->name }}</td>
							<td>
								@if(count($test->testResults) <= 1)
									@foreach($test->testResults as $result)

										@if($result->result)
											<p>
												{{ $result->result }}

												<?php $organism_names = ''?>
												@if(count($test->susceptibility)>0 && $result->result == "Growth")
													@foreach($test->organisms() AS $og)
														<?php
														$organism_name = Organism::find($og['organism_id'])->name;
														$organism_names = (!empty($organism_names))? ($organism_names.', '.$organism_name) : $organism_name;																?>
													@endforeach
													of {{$organism_names ? $organism_names : '---'}}
												@endif
												{{ Measure::find($result->measure_id)->unit }}
												<?php
												$measureRng = Measure::getRange($test->visit->patient, $result->measure_id);
												?>

												@if($measureRng && $test->testType->instruments->count() > 0)
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i><b>{{$measureRng}}</b></i>
												@endif
											</p>
										@else
											Not done
										@endif
									@endforeach
								@else
									<table style="margin: 0px;padding:0px;width:100%" class="table-bordered table-condensed">
										<tr>
											<td>
												<b>Measure</b>
											</td>
											<td><b>Result</b></td>
											@if($test->testType->instruments->count() > 0)

												<td style="width: 20%"><b>Range</b></td>
											@endif
										</tr>
										@foreach($test->testResults as $result)

											@if(Measure::find($result->measure_id)->name == "HIV Status")
												<?php
													continue;
												?>
											@endif
												

												<tr>
													<td style="width: 40%">
														{{ Measure::find($result->measure_id)->name }}
													</td>
													<td>
														@if(!empty($result->result))
															<?php $organism_names = ''?>
															{{ $result->result }}
															@if(count($test->susceptibility)>0 && $result->result == "Growth")
																@foreach($test->organisms() AS $og)
																	<?php
																		$organism_name = Organism::find($og['organism_id'])->name;
																		$organism_names = (!empty($organism_names))? ($organism_names.', '.$organism_name) : $organism_name;																?>
																@endforeach
															 	of {{$organism_names ? $organism_names : '---'}}
															@endif
															{{ Measure::find($result->measure_id)->unit }}


															@if( $test->testType->instruments->count() > 0)
																<?php

																$measureRng = Measure::getRange($test->visit->patient, $result->measure_id);
																?>
																<td>
																	@if($measureRng)
																		<b>{{$measureRng}}</b>&nbsp;
																	@else
																			N/A
																	@endif
																</td>

																@endif
														@else

															@if($test->testType->instruments->count() > 0)

																@if ($test->testType->name == 'Manual Differential & Cell Morphology')
																	&nbsp;
																	<td>&nbsp;</td>
																@else
																 Not done
																	<?php

																	$measureRng = Measure::getRange($test->visit->patient, $result->measure_id);
																	?>
																	<td>
																		@if($measureRng)
																			<b>{{$measureRng}}</b>&nbsp;
																		@else
																			N/A
																		@endif
																	</td>
																@endif
															@else
																@if ($test->testType->name == 'Manual Differential & Cell Morphology')
																	&nbsp;
																@else
																	Not done
																@endif
															@endif
														@endif
													</td>
												</tr>
										@endforeach
									</table>
								@endif
							</td>
							<td>{{ $test->interpretation == '' ? 'N/A' : $test->interpretation }}</td>
							
							@if($test->tested_by !=0)
								<td style="width: 20%;">{{ $test->testedBy->name}}<br />
									On {{ $test->time_completed }}
									@if($test->resultDevices())
										<br /><br />
										<b><i> {{ 'Using:  '.$test->resultDevices() }}</i></b>					
									@endif
									<br /><br />
									
								</td>
							@endif

						</tr>
						
				@empty
					<tr>
						<td colspan="8">{{trans("messages.no-records-found")}}</td>
					</tr>
					
				@endforelse
				
			</tbody>
		</table>
	
	@else
		<div class="panel panel-success">
						<div class="panel-heading ">
							<span class="glyphicon glyphicon-tint"></span>
							<span><strong>{{Lang::choice('messages.specimen-id', 1)}}</strong>&nbsp;:&nbsp;  <strong> {{ $specimen->accession_number }}</strong></span>

							<span class="pull-right"><strong>Requested By </strong>&nbsp;:&nbsp;  <strong> {{ $test->requested_by }}
									({{$test->visit->ward_or_location or trans('messages.unknown') }})</strong></span>
						</div>
				<div class="panel-body">

			<table class="table table-bordered rspecimen">
				<tbody>

					<tr>
						<td><strong>{{Lang::choice('messages.specimen-type', 1)}}</strong></td>
						<td>{{ $specimen->specimenType->name }}</td>

						<td><strong>{{Lang::choice('messages.date-ordered', 1)}}</strong></td>
						<td>{{	$test ? $test->isExternal()?$test->external()->request_date:$test->time_created : ''}}</td>
					</tr>

					<tr>
						<td><strong>{{Lang::choice('messages.specimen-tests-ordered', 1)}}</strong></td>
						<td>{{ $specimen->testTypes() }}</td>

						<td><strong>{{Lang::choice('messages.test-category', 2)}}</strong></td>
						<td>{{ $specimen->labSections() }}</td>
					</tr>

					<tr>
						<td><strong>{{trans('messages.ordered-specimen-status')}}</strong></td>
						@if($specimen->specimen_status_id == Specimen::NOT_COLLECTED)
							<td>{{trans('messages.specimen-not-collected')}}</td>
						@elseif($specimen->specimen_status_id == Specimen::ACCEPTED)
							<td>{{trans('messages.specimen-accepted')}}</td>
						@elseif($specimen->specimen_status_id == Specimen::REJECTED)
							<td style="background: black;color:white;">{{trans('messages.specimen-rejected')}}</td>
						@endif

						@if($specimen->specimen_status_id == Specimen::ACCEPTED)
							<td><strong>{{ trans('messages.collected-by') }}</strong></td>
						@elseif($specimen->specimen_status_id == Specimen::REJECTED)
							<td><strong>{{ trans('messages.rejected-by') }}</strong></td>
						@endif

						@if($specimen->specimen_status_id == Specimen::NOT_COLLECTED)
							<td></td>
						@elseif($specimen->specimen_status_id == Specimen::ACCEPTED)
							<td>{{$specimen->acceptedBy->name}}</td>
						@elseif($specimen->specimen_status_id == Specimen::REJECTED)
							<td>{{$specimen->rejectedBy->name}}</td>
						@endif
					</tr>

					
				</tbody>
			</table>
					<?php
						$number = $specimen->accession_number;
							$rej_reason = DB::select(DB::raw("SELECT rejection_reasons.reason AS reason FROM rejection_reasons INNER JOIN specimens ON specimens.rejection_reason_id = rejection_reasons.id WHERE specimens.accession_number='$number'"));
					?>
			<table class="table table-bordered rtest">
				<tbody>
					<tr>
						<th colspan="8">{{trans('messages.specimenrejected')}}
						</th>
					</tr>

					<tr>
						<td>{{$rej_reason[0]->reason}}</td>
					</tr>
				</tbody>
			</table>
	@endif
					<?php  
						$susc_available = false;
						foreach($tests as $test){
							if(count($test->susceptibility)>0){
								$susc_available = true;
							}
						}
					?>
						@if($susc_available == true)
							<p><strong>{{trans("messages.susceptibility-test-results")}}</strong></p>
						@endif
						
						<?php $interpretationText = array("R" => "Resistant", "I" => "Intermediate", "S" => "Sensitive")?>
						@foreach($tests as $test)
							@if(count($test->susceptibility)>0)
							<table class="table table-condensed rsusc" >


									<?php $i = 0 ?>
									@foreach($test->organisms() as $organism)
										<?php
										$organism = Organism::find($organism->organism_id);

										?>
										{{($i % 2 == 0) ? ('<tr class="row">') : ""}}
										<td>
											<table class="table table-bordered" >
												<tbody>
												<tr>
													<th colspan="3">{{ $organism->name }}</th>
												</tr>
												<tr>
													<th width="50%">{{ Lang::choice('messages.drug',1) }}</th>
													<th>{{ trans('messages.zone-size')}}</th>
													<th>{{ trans('messages.interp')}}</th>
												</tr>
												@foreach(Susceptibility::drugs_search($test->id, $organism->id) as $drug)
													<?php
													$drug = Drug::find($drug->drug_id);
													?>
													@if($drugSusceptibility = Susceptibility::getDrugSusceptibility($test->id, $organism->id, $drug->id))
														<tr>
															<td>{{ $drug->name }}</td>
															<td>{{ $drugSusceptibility->zone!=null?$drugSusceptibility->zone:'' }}</td>
															<td>{{ $drugSusceptibility->interpretation!=null?
																		($drugSusceptibility->interpretation.' - '.$interpretationText[$drugSusceptibility->interpretation]) :'' }}</td>														</tr>
													@else
														<tr>
															<td>{{ $drug->name }}</td>
															<td></td>
															<td></td>
														</tr>
													@endif
												@endforeach
												</tbody>
											</table>
										</td>
										{{($i % 2 == 1) ? ('</tr>') : ""}}
										<?php $i = $i + 1 ?>
									@endforeach

							</table>
							@endif
						@endforeach
				</div>
			</div>
		</div>
			@empty
				<tr>
					<td colspan="7">{{trans("messages.no-records-found")}}</td>
				</tr>
			@endforelse
		@endif
		</div>
		@endif

	</div>

</div>

<!--PRINT CONFIRMATION POPUP BEGIN -->
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">

		<!-- - content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel" style="text-align: left;">
					Select Printer
				</h4>
			</div>
			<div class="modal-body">
        <span style="text-align:center;">
          <table align="center" id="printers">
			   @foreach($available_printers AS $printer)
			  <tr onmousedown="updateValue(this)" value="{{$printer}}">
				  <td><input type="radio" class="printer_radio_button" value="{{$printer}}" name="printer_name"/></td>
				  <td style="text-align: left; padding-left:50px;">{{$printer}}</td>
			  </tr>
			  @endforeach
		  </table>
        </span>
				<div class="modal-footer">
				@if($patient_visits>0)
					@if($test->specimen->printSmallLabels())
						<a class="btn btn-success pull-left"
						   href="{{URL::route('reports.print_zebra_report', array($test->specimen_id))}}"
						   data-toggle="modal" >
							<span class="glyphicon glyphicon-print"></span>
							Print On Small Label
						</a>
					@endif
				@endif

					<button type="button" class="btn btn-primary" 
						onclick="submitPrintForm(), track_printing('<?php echo $specimen_id; ?>')">Okay</button>
					<button type="button" class="btn" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
		function track_printing(specimen_id)		
		{	
			var url = "/track_patient_report_printing?specimen_id=" + specimen_id;
			jQuery.ajax({ async: true,
					  url : url,
					  success : function()
					  {	
					  		
					  },
					  error : function()
					  {
					  
					  }
			})
		}
</script>


<!--CONFIRMATION POPUP END -->
@stop
