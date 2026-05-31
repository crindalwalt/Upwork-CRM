<?php

use App\Enums\UserRole;
use App\Models\Setting;
use App\Models\User;

test('admins can render the settings page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/settings')
        ->assertOk();
});

test('non admins cannot access the settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings')
        ->assertForbidden();
});

test('admins can update operational settings', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->put('/settings', [
            'openai_api_key' => 'test-key',
            'openai_model' => 'gpt-4.1-mini',
            'weekly_connect_budget' => 140,
            'daily_proposal_target' => 4,
            'min_ai_score_to_propose' => 8,
            'app_timezone' => 'UTC',
        ]);

    $response->assertSessionHasNoErrors()->assertRedirect();

    expect(Setting::getValue('openai_api_key'))->toBe('test-key')
        ->and(Setting::getValue('openai_model'))->toBe('gpt-4.1-mini')
        ->and(Setting::getValue('weekly_connect_budget'))->toBe(140)
        ->and(Setting::getValue('daily_proposal_target'))->toBe(4)
        ->and(Setting::getValue('min_ai_score_to_propose'))->toBe(8)
        ->and(Setting::getValue('app_timezone'))->toBe('UTC');
});

test('admins can update team members from settings', function () {
    $admin = User::factory()->admin()->create();
    $intern = User::factory()->create();

    $response = $this->actingAs($admin)
        ->patch(route('settings.users.update', $intern), [
            'role' => UserRole::Admin->value,
            'is_active' => 0,
        ]);

    $response->assertSessionHasNoErrors()->assertRedirect();

    expect($intern->fresh()->role)->toBe(UserRole::Admin)
        ->and($intern->fresh()->is_active)->toBeFalse();
});

test('settings keep the last active admin protected', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->patch(route('settings.users.update', $admin), [
            'role' => UserRole::Intern->value,
            'is_active' => 0,
        ]);

    $response->assertRedirect()->assertSessionHas('error', 'Keep at least one active admin account.');

    expect($admin->fresh()->role)->toBe(UserRole::Admin)
        ->and($admin->fresh()->is_active)->toBeTrue();
});
