@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="mb-8 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-gray-200 bg-gray-100 text-2xl text-gray-800 shadow-sm">
            <i class="ti ti-robot"></i>
        </div>
        <h1 class="mt-5 text-3xl font-semibold text-gray-900 font-display">Upwork CRM</h1>
        <p class="mt-2 text-sm text-gray-500">Freelance intelligence, automated</p>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-form-input name="email" label="Email" type="email" :value="old('email')" autocomplete="username" required autofocus />
        <x-form-input name="password" label="Password" type="password" autocomplete="current-password" required />

        <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
            <span>Remember me</span>
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-black">
            <i class="ti ti-login-2"></i>
            <span>Log in</span>
        </button>
    </form>

    {{-- <div class="mt-6 rounded-xl border border-gray-200 bg-white px-4 py-4 text-sm text-gray-500 shadow-sm">
        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Default credentials</div>
        <div class="mt-2 font-mono text-xs text-gray-500">admin@crm.local / password</div>
    </div> --}}
@endsection
