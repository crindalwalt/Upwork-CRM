@extends('layouts.app')

@section('title', 'Edit Portfolio Piece')

@section('subtitle', 'Refine the proof point so it stays proposal-ready.')

@section('content')
    @include('portfolio.partials.form', [
        'formAction' => route('portfolio.update', $portfolio),
        'method' => 'PUT',
        'submitLabel' => 'Update case study',
    ])
@endsection
