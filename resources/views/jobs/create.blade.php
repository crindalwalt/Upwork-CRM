@extends('layouts.app')

@section('title', 'Add Job')

@section('subtitle', 'Capture the opportunity details before you draft the proposal.')

@section('content')
    @include('jobs.partials.form', [
        'formAction' => route('jobs.store'),
        'method' => 'POST',
        'submitLabel' => 'Create job',
    ])
@endsection
