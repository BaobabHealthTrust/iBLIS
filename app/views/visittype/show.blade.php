@extends("layout")
@section("content")
	<div>
		<ol class="breadcrumb">
		  <li><a href="{{{URL::route('user.home')}}}">{{trans('messages.home')}}</a></li>
		  <li><a href="{{ URL::route('visittype.index') }}">{{ Lang::choice('messages.visit-type',1) }}</a></li>
		  <li class="active">{{trans('messages.visit-type-details')}}</li>
		</ol>
	</div>
	<div class="panel panel-primary">
		<div class="panel-heading ">
			<span class="glyphicon glyphicon-cog"></span>
			{{trans('messages.visit-type-details')}}
			<div class="panel-btn">
				<a class="btn btn-sm btn-info" href="{{ URL::to("visittype/". $visittype->id ."/edit") }}">
					<span class="glyphicon glyphicon-edit"></span>
					{{trans('messages.edit')}}
				</a>
			</div>
		</div>
		<div class="panel-body">
			<div class="display-details">
				<h3 class="view"><strong>{{ Lang::choice('messages.name',1) }}</strong>{{ $visittype->name }} </h3>
				<h4 class="view"><strong>{{ Lang::choice('messages.description',2) }}</strong>{{ $visittype->description }} </h4>
				<p class="view"><strong>{{ Lang::choice('messages.wards',1) }}</strong>
					{{ implode(", ", $wards->lists('name') )}}</p>
			</div>
		</div>
	</div>
@stop