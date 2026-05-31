@extends('layouts.app')

@section('title', 'Edit Employer')

@section('subtitle', 'Refresh trust signals and internal notes without losing history.')

@section('content')
    @include('employers.partials.form', [
        'formAction' => route('employers.update', $employer),
        'method' => 'PUT',
        'submitLabel' => 'Update employer',
    ])
@endsection
