@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="mb-8 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-violet-100 text-3xl text-violet-700 shadow-sm">
            <i class="ti ti-robot"></i>
        </div>
        <h1 class="mt-5 text-3xl font-semibold text-gray-900 font-display">ProposalCRM</h1>
        <p class="mt-2 text-sm text-gray-400">Freelance intelligence, automated</p>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-form-input name="email" label="Email" type="email" :value="old('email')" autocomplete="username" required autofocus />
        <x-form-input name="password" label="Password" type="password" autocomplete="current-password" required />

        <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
            <span>Remember me</span>
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-violet-700">
            <i class="ti ti-login-2"></i>
            <span>Log in</span>
        </button>
    </form>

    <div class="mt-6 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4 text-sm text-gray-500">
        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Default credentials</div>
        <div class="mt-2 font-mono text-xs text-gray-500">admin@crm.local / password</div>
    </div>
@endsection
