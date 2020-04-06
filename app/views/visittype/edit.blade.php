@extends("layout")
@section("content")
<div>
	<ol class="breadcrumb">
	  <li><a href="{{{URL::route('user.home')}}}">{{trans('messages.home')}}</a></li>
	  <li><a href="{{ URL::route('visittype.index') }}">{{ Lang::choice('messages.visit-type',1) }}</a></li>
	  <li class="active">{{trans('messages.edit-visit-type')}}</li>
	</ol>
</div>
<div class="panel panel-primary">
	<div class="panel-heading ">
		<span class="glyphicon glyphicon-edit"></span>
		{{trans('messages.edit-visit-type')}}
	</div>
	{{ Form::model($visittype, array(
			'route' => array('visittype.update', $visittype->id), 'method' => 'PUT',
			'id' => 'form-edit-visittype'
		)) }}
		<div class="panel-body">
			@if($errors->all())
				<div class="alert alert-danger">
					{{ HTML::ul($errors->all()) }}
				</div>
			@endif

			<div class="form-group">
				{{ Form::label('name', Lang::choice('messages.name',1)) }}
				{{ Form::text('name', Input::old('name'), array('class' => 'form-control', 'disabled')) }}
			</div>
			<div class="form-group">
				{{ Form::label('description', Lang::choice('messages.description',2)) }}
				{{ Form::text('description', Input::old('description'), array('class' => 'form-control', 'disabled')) }}
			</div>
			<div class="form-group">
				{{ Form::label('organisms', trans('messages.select-wards')) }}
				<div class="form-pane panel panel-default">
					<div class="container-fluid">
						<?php
							$counter = 0;
							$alternator = "";
						?>
						@foreach($wards as $val)
							{{ ($counter%4==0)?"<div class='row $alternator'>":"" }}
							<?php
								$counter++;
								$alternator = (((int)$counter/4)%2==1?"row-striped":"");
							?>
							<div class="col-md-3">
								<label  class="checkbox">
									<input type="checkbox" name="wards[]" value="{{ $val->id}}"
										{{ in_array($val->id, $visittype_wards->lists('ward_id'))?"checked":"" }} >
										{{ $val->name }}
								</label>
							</div>
							{{ ($counter%4==0)?"</div>":"" }}
						@endforeach
						</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<div class="form-group actions-row">
				{{ Form::button(
					'<span class="glyphicon glyphicon-save"></span> '.trans('messages.save'), 
					['class' => 'btn btn-primary', 'onclick' => 'submit()']
				) }}
				{{ Form::button(trans('messages.cancel'), 
					['class' => 'btn btn-default', 'onclick' => 'javascript:history.go(-1)']
				) }}
			</div>
		</div>
	{{ Form::close() }}
</div>

@stop
