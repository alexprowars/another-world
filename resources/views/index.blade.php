@extends('layouts.default')
@section('title', 'Title')
@section('content')
	<div style="display: flex;align-items: center;justify-content: center;height: 100%">
		<a href="{{ route('login.social', ['vkid']) }}">VK</a>
	</div>
@endsection