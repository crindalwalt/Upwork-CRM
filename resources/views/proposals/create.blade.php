@extends('layouts.app')

@section('title', 'Add proposal')

@section('content')
    @include('proposals.partials.form', [
        'formAction' => route('proposals.store'),
        'method' => 'POST',
        'submitLabel' => 'Save proposal',
    ])
@endsection
