<div class="panel-heading">
	<h4 class="panel-title">
		{!! Form::radio('driver', $connection['driver'], old('driver', $connection['default']) == $connection['driver'], ['class' => 'hide']) !!}
		<a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $connection['driver'] }}">
			{{ $connection['driver'] }}
		</a>
	</h4>
</div>
<div id="collapse{{ $connection['driver'] }}" class="panel-collapse collapse{{ $connection['default'] == $connection['driver'] ? ' in' : '' }}">
	<div class="panel-body">
		<div class="row">
			<div class="form-group col-md-12">
				<div class="col-md-2">{!! Form::label(Lang::get('installer::installer.database.host')) !!}</div>
				<div class="col-md-10">{!! Form::text($connection['driver']."[host]", old($connection['driver'].'.host', $connection['host']), [
                                                'class'=>'form-control',
                                                'placeholder'=> Lang::get('installer::installer.database.host') ]) !!}</div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<div class="col-md-2">{!! Form::label(Lang::get('installer::installer.database.database')) !!}</div>
				<div class="col-md-10">{!! Form::text($connection['driver']."[database]", old($connection['driver'].'.database'), [
                                                'class'=>'form-control',
                                                'placeholder'=> Lang::get('installer::installer.database.database') ]) !!}</div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<div class="col-md-2">{!! Form::label(Lang::get('installer::installer.database.username')) !!}</div>
				<div class="col-md-10">{!! Form::text($connection['driver']."[username]", old($connection['driver'].'.username'), [
                                                'class'=>'form-control',
                                                'placeholder'=> Lang::get('installer::installer.database.username') ]) !!}</div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<div class="col-md-2">{!! Form::label(Lang::get('installer::installer.database.password')) !!}</div>
				<div class="col-md-10">{!! Form::text($connection['driver']."[password]", old($connection['driver'].'.password'), [
                                                'class'=>'form-control',
                                                'placeholder'=> Lang::get('installer::installer.database.password') ]) !!}</div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<div class="col-md-2">{!! Form::label(Lang::get('installer::installer.database.prefix')) !!}</div>
				<div class="col-md-10">{!! Form::text($connection['driver']."[prefix]", old($connection['driver'].'.prefix'), [
                                                'class'=>'form-control',
                                                'placeholder'=> Lang::get('installer::installer.database.prefix') ]) !!}</div>
			</div>
		</div>
		
	</div>
</div>