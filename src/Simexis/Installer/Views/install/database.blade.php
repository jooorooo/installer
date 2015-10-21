@extends('installer::layouts.master')

@section('container')
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="glyphicon glyphicon-folder-close"></i>
                @lang('installer::installer.database.title')
            </h3>
        </div>
        <div class="panel-body">

		@if(!$errors->isEmpty())
		<div class="row">
			<div class="alert alert-error">
				<ul class="alert alert-danger">
					@foreach($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		</div>
		@endif
		{!! Form::open(['route' => 'installer::database', 'method' => 'post']) !!}
			<div class="panel-group" id="accordion">
				@foreach($connections AS $connection)
				<div class="panel panel-default">
					@include('installer::install.database_driver.' . $connection['driver'], ['connection' => $connection])
				</div>
				@endforeach
			</div>
			<button class="btn btn-success" type="submit">
				@lang('installer::installer.next')
			</button>
			{!! Form::close() !!}
        </div>
    </div>
	<script type="text/javascript">
	$('#accordion .panel-title a').on('click', function(){
		var acc = $('#accordion')
			.find('input[type=radio]')
			.removeAttr('checked').end()
			.find('.panel-title a')
			.filter(this)
			.closest('.panel')
			.find('.panel-heading input[type=radio]')
			.attr('checked', true);
    });
	</script>
@stop