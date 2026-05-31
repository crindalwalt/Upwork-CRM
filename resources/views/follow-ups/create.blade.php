@extends('layouts.app')

@section('title', 'Add Follow-up')

@section('subtitle', 'Schedule the next concrete action for a proposal.')

@section('content')
    @include('follow-ups.partials.form', [
        'followUp' => null,
        'formAction' => route('follow-ups.store'),
        'method' => 'POST',
        'submitLabel' => 'Create follow-up',
    ])
@endsection
