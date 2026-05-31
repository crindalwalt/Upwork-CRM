@extends('layouts.app')

@section('title', 'Add Portfolio Piece')

@section('subtitle', 'Capture a reusable proof point that can support future proposals.')

@section('content')
    @include('portfolio.partials.form', [
        'portfolio' => null,
        'formAction' => route('portfolio.store'),
        'method' => 'POST',
        'submitLabel' => 'Create case study',
    ])
@endsection
