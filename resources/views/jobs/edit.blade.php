@extends('layouts.app')

@section('title', 'Edit Job')

@section('subtitle', 'Keep the opportunity record current before scoring or proposing against it.')

@section('content')
    @include('jobs.partials.form', [
        'formAction' => route('jobs.update', $job),
        'method' => 'PUT',
        'submitLabel' => 'Update job',
    ])
@endsection
