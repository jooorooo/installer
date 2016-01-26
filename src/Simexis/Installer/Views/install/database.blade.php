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
			<div class="panel-group">
				<div class="panel-body">
					<div class="row">
						<div class="form-group col-md-12">
							<div class="col-md-2">{!! Form::label(Lang::get('installer::installer.database.host')) !!}</div>
							<div class="col-md-10">{!! Form::text("host", old('host', 'localhost'), [
															'class'=>'form-control',
															'placeholder'=> Lang::get('installer::installer.database.host') ]) !!}</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-md-12">
							<div class="col-md-2">{!! Form::label(Lang::get('installer::installer.database.database')) !!}</div>
							<div class="col-md-10">{!! Form::text("database", old('database'), [
															'class'=>'form-control',
															'placeholder'=> Lang::get('installer::installer.database.database') ]) !!}</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-md-12">
							<div class="col-md-2">{!! Form::label(Lang::get('installer::installer.database.username')) !!}</div>
							<div class="col-md-10">{!! Form::text("username", old('username'), [
															'class'=>'form-control',
															'placeholder'=> Lang::get('installer::installer.database.username') ]) !!}</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-md-12">
							<div class="col-md-2">{!! Form::label(Lang::get('installer::installer.database.password')) !!}</div>
							<div class="col-md-10">{!! Form::text("password", old('password'), [
															'class'=>'form-control',
															'placeholder'=> Lang::get('installer::installer.database.password') ]) !!}</div>
						</div>
					</div>
					
				</div>
			</div>
			<button class="btn btn-success" type="submit">
				@lang('installer::installer.next')
			</button>
			{!! Form::close() !!}
        </div>
    </div>
@stop