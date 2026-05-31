<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Setting;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * @var array<string, array{label: string, type: string, input: string, description: string}>
     */
    private const SETTING_FIELDS = [
        'openai_api_key' => [
            'label' => 'OpenAI API key',
            'type' => 'string',
            'input' => 'password',
            'description' => 'API key used for AI-assisted tooling.',
        ],
        'openai_model' => [
            'label' => 'OpenAI model',
            'type' => 'string',
            'input' => 'text',
            'description' => 'Model identifier used for AI tooling.',
        ],
        'weekly_connect_budget' => [
            'label' => 'Weekly connect budget',
            'type' => 'integer',
            'input' => 'number',
            'description' => 'Maximum connects to spend in the current week.',
        ],
        'daily_proposal_target' => [
            'label' => 'Daily proposal target',
            'type' => 'integer',
            'input' => 'number',
            'description' => 'Expected proposal volume per day.',
        ],
        'min_ai_score_to_propose' => [
            'label' => 'Minimum AI score',
            'type' => 'integer',
            'input' => 'number',
            'description' => 'Threshold under which jobs should be treated as weak fits.',
        ],
        'app_timezone' => [
            'label' => 'App timezone',
            'type' => 'string',
            'input' => 'text',
            'description' => 'Local timezone used for scheduling and reporting.',
        ],
    ];

    public function __construct(private readonly SettingsService $settingsService)
    {
        $this->middleware(function (Request $request, $next) {
            abort_unless($request->user()?->isAdmin(), 403);

            return $next($request);
        });
    }

    public function index(): View
    {
        return view('settings.index', [
            'settings' => Setting::query()->whereIn('key', array_keys(self::SETTING_FIELDS))->get()->keyBy('key'),
            'fields' => self::SETTING_FIELDS,
            'users' => User::query()->orderByDesc('role')->orderBy('name')->get(),
            'roles' => UserRole::cases(),
            'connectsRemaining' => $this->settingsService->getConnectsRemainingThisWeek(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'openai_api_key' => ['nullable', 'string'],
            'openai_model' => ['nullable', 'string'],
            'weekly_connect_budget' => ['required', 'integer', 'min:0'],
            'daily_proposal_target' => ['required', 'integer', 'min:0'],
            'min_ai_score_to_propose' => ['required', 'integer', 'min:1', 'max:10'],
            'app_timezone' => ['required', 'string'],
        ]);

        foreach (self::SETTING_FIELDS as $key => $meta) {
            Setting::set($key, $validated[$key] ?? null, $meta['type'], $meta['description']);
        }

        return back()->with('success', 'Settings updated.');
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:'.implode(',', array_map(static fn (UserRole $role) => $role->value, UserRole::cases()))],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $nextRole = UserRole::from($validated['role']);
        $nextActive = (bool) ($validated['is_active'] ?? false);
        $activeAdminCount = User::query()
            ->where('role', UserRole::Admin->value)
            ->where('is_active', true)
            ->count();

        if ($user->isAdmin() && $activeAdminCount === 1 && ($nextRole !== UserRole::Admin || ! $nextActive)) {
            return back()->with('error', 'Keep at least one active admin account.');
        }

        $user->update([
            'role' => $nextRole,
            'is_active' => $nextActive,
        ]);

        return back()->with('success', 'Team member updated.');
    }
}
