@extends('layouts.app')

@section('title', 'Edit Follow-up')

@section('subtitle', 'Adjust the schedule or context without losing the proposal link.')

@section('content')
    @include('follow-ups.partials.form', [
        'formAction' => route('follow-ups.update', $followUp),
        'method' => 'PUT',
        'submitLabel' => 'Update follow-up',
    ])
@endsection
