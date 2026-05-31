@extends('layouts.app')

@section('title', 'Settings')

@section('subtitle', 'Manage operational thresholds, AI configuration, and the active team roster.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-6 xl:grid-cols-[minmax(0,3fr)_minmax(320px,2fr)]">
            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="font-display text-lg font-medium text-gray-800">Operational settings</h2>
                        <p class="mt-1 text-sm text-gray-400">These values shape scoring thresholds, pacing, and AI configuration.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('settings.update') }}" class="mt-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-5 md:grid-cols-2">
                        @foreach ($fields as $key => $field)
                            <div class="{{ in_array($key, ['openai_api_key', 'app_timezone'], true) ? 'md:col-span-2' : '' }}">
                                <x-form-input :name="$key" :label="$field['label']" :type="$field['input']" :value="old($key, $settings->get($key)?->typedValue())" :placeholder="$field['description']" />
                                <p class="mt-2 text-xs text-gray-400">{{ $field['description'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white hover:bg-violet-700">
                        <i class="ti ti-device-floppy"></i>
                        <span>Save settings</span>
                    </button>
                </form>
            </article>

            <aside class="space-y-6">
                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Status snapshot</div>
                    <div class="mt-4 grid gap-4">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-sm text-gray-500">Connects remaining this week</div>
                            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $connectsRemaining }}</div>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-sm text-gray-500">Configured model</div>
                            <div class="mt-1 text-lg font-semibold text-gray-900">{{ $settings->get('openai_model')?->typedValue() ?? 'Not set' }}</div>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Admin note</div>
                    <p class="mt-3 text-sm leading-6 text-gray-600">The team controls below are intentionally conservative. The page prevents you from demoting or deactivating the last active admin account.</p>
                </section>
            </aside>
        </section>

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="font-display text-lg font-medium text-gray-800">Team management</h2>
                <p class="mt-1 text-sm text-gray-400">Adjust roles and activation status for the internal team.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-4 align-top">
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="mt-1 text-sm text-gray-400">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <select name="role" form="team-form-{{ $user->id }}" class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->value }}" @selected($user->role === $role)>{{ ucfirst($role->value) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <label class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-2 text-xs font-semibold text-gray-700">
                                        <input type="hidden" name="is_active" value="0" form="team-form-{{ $user->id }}">
                                        <input type="checkbox" name="is_active" value="1" form="team-form-{{ $user->id }}" @checked($user->is_active) class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                                        <span>{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                                    </label>
                                </td>
                                <td class="px-6 py-4 align-top text-right">
                                    <form id="team-form-{{ $user->id }}" method="POST" action="{{ route('settings.users.update', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                    </form>
                                    <button type="submit" form="team-form-{{ $user->id }}" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                                        <i class="ti ti-device-floppy"></i>
                                        <span>Update</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
