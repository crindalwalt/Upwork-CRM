@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="grid gap-6 lg:grid-cols-2">
            <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-800 font-display">Profile information</h2>
                <p class="mt-1 text-sm text-gray-400">Update your name and email address.</p>

                <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <x-form-input name="name" label="Name" :value="old('name', $user->name)" required />
                    <x-form-input name="email" label="Email" type="email" :value="old('email', $user->email)" required />

                    <button type="submit" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Save profile</button>
                </form>
            </section>

            <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-800 font-display">Update password</h2>
                <p class="mt-1 text-sm text-gray-400">Choose a strong password for your account.</p>

                <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <label class="block">
                        <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Current password</span>
                        <input type="password" name="current_password" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        @if ($errors->updatePassword->first('current_password'))
                            <span class="mt-2 block text-sm text-red-600">{{ $errors->updatePassword->first('current_password') }}</span>
                        @endif
                    </label>

                    <label class="block">
                        <span class="text-xs font-medium uppercase tracking-wide text-gray-500">New password</span>
                        <input type="password" name="password" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        @if ($errors->updatePassword->first('password'))
                            <span class="mt-2 block text-sm text-red-600">{{ $errors->updatePassword->first('password') }}</span>
                        @endif
                    </label>

                    <label class="block">
                        <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Confirm password</span>
                        <input type="password" name="password_confirmation" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                    </label>

                    <button type="submit" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Update password</button>
                </form>
            </section>
        </div>

        <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-medium text-gray-800 font-display">Delete account</h2>
            <p class="mt-1 text-sm text-gray-400">This permanently removes your account and profile data.</p>

            <form method="POST" action="{{ route('profile.destroy') }}" class="mt-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                @csrf
                @method('DELETE')

                <label class="block flex-1">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Confirm with your password</span>
                    <input type="password" name="password" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200">
                    @if ($errors->userDeletion->first('password'))
                        <span class="mt-2 block text-sm text-red-600">{{ $errors->userDeletion->first('password') }}</span>
                    @endif
                </label>

                <button type="submit" class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Delete account</button>
            </form>
        </section>
    </div>
@endsection
