@extends('layouts.app')

@section('title', 'Edit proposal — '.($proposal->job?->title ?? 'Proposal'))

@section('content')
    @include('proposals.partials.form', [
        'formAction' => route('proposals.update', $proposal),
        'method' => 'PUT',
        'submitLabel' => 'Update proposal',
    ])
@endsection
