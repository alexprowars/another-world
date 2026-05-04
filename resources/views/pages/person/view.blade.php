@extends('layouts.default')
@section('content')
	<table class="table np">
		<tr>
			<x-person-view :user="auth()->user()"/>
			<td valign="top">
				<? if (isset($message) && $message != ''): ?>
					<br>
					<center><font color=red><b><?= $message ?></b></font></center><br>
				<? endif; ?>
				@include('pages.person.menu')

				@yield('content')
			</td>
		</tr>
	</table>
@endsection