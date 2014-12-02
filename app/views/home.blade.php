@extends('master')

@section('style')
    {{ HTML::style("/css/home.css") }}
@stop

@section('content')

	<div>
		<h3>Most Rated Players</h3>
	</div>
	<div align="center">
		<div class="row">
			@foreach($players as $player)
				<div class="col-xs-12 col-sm-6 col-lg-4">
					<a href="{{ $player->url }}">
						<span class="name">{{ $player->name }}</span>
						<img class="thumbnail" src="{{ $player->image_url }}" alt="Profile Image">
					</a>
				</div>
			@endforeach
		</div> <!-- close row -->
	</div> <!-- close most rated players div -->
	@if(1==2)
		<div>
			<h3>All Teams</h3>
		</div>
		<div align="center">
			<div class="row">
				@foreach($teams as $team)
					<div class="col-xs-12 col-sm-6 col-lg-4">
						<p>{{ $team->name }}</p>
					</div>
				@endforeach
			</div> <!-- close row -->
		</div> <!-- close most rated players div -->
	@endif
@stop