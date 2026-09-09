<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Settings are grouped into tabs; each group is saved independently so a
     * mistake in one tab (e.g. SMS credentials) never risks the others.
     */
    protected array $groups = ['company', 'invoice', 'general', 'sms', 'whatsapp', 'backup'];

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('settings.view'), 403);

        $activeGroup = $request->get('tab', 'company');
        if (! in_array($activeGroup, $this->groups, true)) {
            $activeGroup = 'company';
        }

        $settings = Setting::query()->get()->groupBy('group');

        return view('admin.settings.index', [
            'settings' => $settings,
            'activeGroup' => $activeGroup,
            'groups' => $this->groups,
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Settings' => null],
        ]);
    }

    public function update(Request $request, string $group): RedirectResponse
    {
        abort_unless($request->user()->can('settings.edit'), 403);
        abort_unless(in_array($group, $this->groups, true), 404);

        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            if ($key === 'company_logo' && $request->hasFile('company_logo')) {
                continue; // handled separately below
            }

            Setting::setValue($key, is_array($value) ? json_encode($value) : $value, $group);
        }

        if ($group === 'company' && $request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->store('settings', 'public');
            Setting::setValue('company_logo', $path, 'company', 'image');
        }

        // Checkbox-style boolean settings that were left unchecked won't appear in
        // the request at all, so explicitly zero them out for this group.
        foreach ($this->booleanKeysFor($group) as $boolKey) {
            if (! $request->has($boolKey)) {
                Setting::setValue($boolKey, '0', $group, 'boolean');
            }
        }

        ActivityLog::record('updated', description: "Settings group '{$group}' updated");

        return redirect()->route('admin.settings.index', ['tab' => $group])->with('success', ucfirst($group) . ' settings updated.');
    }

    protected function booleanKeysFor(string $group): array
    {
        return match ($group) {
            'whatsapp' => ['whatsapp_enabled'],
            default => [],
        };
    }
}
