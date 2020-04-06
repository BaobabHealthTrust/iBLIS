@extends("layout")
@section("content")
<div>
	<ol class="breadcrumb">
	  <li><a href="{{{URL::route('user.home')}}}">{{trans('messages.home')}}</a></li>
	  <li class="active">{{ Lang::choice('messages.visit-type',1) }}</li>
	</ol>
</div>
@if (Session::has('message'))
	<div class="alert alert-info">{{ trans(Session::get('message')) }}</div>
@endif
<div class="panel panel-primary">
	<div class="panel-heading ">
		<span class="glyphicon glyphicon-cog"></span>
		{{trans('messages.list-visit-types')}}
		<!-- <div class="panel-btn">
			<a class="btn btn-sm btn-info" href="{{ URL::to("visittype/create") }}" >
				<span class="glyphicon glyphicon-plus-sign"></span>
				{{trans('messages.add-visit-type')}}
			</a>
		</div> -->
	</div>
	<div class="panel-body">
		<table class="table table-striped table-hover table-condensed search-table">
			<thead>
				<tr>
					<th>{{ Lang::choice('messages.name',1) }}</th>
					<th>{{ Lang::choice('messages.description',2) }}</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			@foreach($visittypes as $key => $value)
				<tr @if(Session::has('activevisittype'))
                            {{(Session::get('activevisittype') == $value->id)?"class='info'":""}}
                        @endif
                        >
					<td>{{ $value->name }}</td>
					<td>{{ $value->description }}</td>
					<td>
						<!-- show the visittype (uses the show method found at GET /visittype/{id} -->
						<a class="btn btn-sm btn-success" href="{{ URL::to("visittype/" . $value->id) }}">
							<span class="glyphicon glyphicon-eye-open"></span>
							{{trans('messages.view')}}
						</a>

						<!-- edit this visittype (uses the edit method found at GET /visittype/{id}/edit -->
						<a class="btn btn-sm btn-info" href="{{ URL::to("visittype/" . $value->id . "/edit") }}" >
							<span class="glyphicon glyphicon-edit"></span>
							{{trans('messages.edit')}}
						</a>
						<!-- delete this visittype (uses the delete method found at GET /visittype/{id}/delete -->
						<!-- <button class="btn btn-sm btn-danger delete-item-link"
							data-toggle="modal" data-target=".confirm-delete-modal"	
							data-id='{{ URL::to("visittype/" . $value->id . "/delete") }}'>
							<span class="glyphicon glyphicon-trash"></span>
							{{trans('messages.delete')}}
						</button> -->

					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		{{ Session::put('SOURCE_URL', URL::full()) }}
	</div>
</div>
@stop