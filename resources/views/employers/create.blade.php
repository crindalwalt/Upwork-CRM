@extends('layouts.app')

@section('title', 'Add Employer')

@section('subtitle', 'Build the client record before attaching jobs and proposals to it.')

@section('content')
    @include('employers.partials.form', [
        'employer' => null,
        'formAction' => route('employers.store'),
        'method' => 'POST',
        'submitLabel' => 'Create employer',
    ])
@endsection
