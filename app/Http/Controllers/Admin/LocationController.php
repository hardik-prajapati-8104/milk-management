<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Area;
use App\Models\Route as DeliveryRoute;
use App\Models\User;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('routes.view'), 403);

        return view('admin.locations.index', [
            'areas' => Area::withCount('customers')->orderBy('name')->get(),
            'villages' => Village::with('area')->withCount('customers')->orderBy('name')->get(),
            'routes' => DeliveryRoute::with(['area', 'deliveryBoy'])->withCount('customers')->orderBy('name')->get(),
            'deliveryBoys' => User::role('Delivery Boy')->orderBy('name')->get(),
            'activeTab' => $request->get('tab', 'areas'),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Routes & Areas' => null],
        ]);
    }

    // ---------- Areas ----------

    public function storeArea(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('routes.create'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
        ]);

        $area = Area::create($data);
        ActivityLog::record('created', $area, new: $area->toArray(), description: "Area {$area->name} created");

        return back()->with('success', "Area \"{$area->name}\" created.");
    }

    public function updateArea(Request $request, Area $area): RedirectResponse
    {
        abort_unless($request->user()->can('routes.edit'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        $area->update($data);

        return back()->with('success', "Area \"{$area->name}\" updated.");
    }

    public function destroyArea(Request $request, Area $area): RedirectResponse
    {
        abort_unless($request->user()->can('routes.delete'), 403);

        if ($area->customers()->exists()) {
            return back()->with('error', "Can't delete \"{$area->name}\" — customers are still assigned to it.");
        }

        $area->delete();

        return back()->with('success', 'Area deleted.');
    }

    // ---------- Villages ----------

    public function storeVillage(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('routes.create'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'area_id' => ['nullable', 'exists:areas,id'],
        ]);

        $village = Village::create($data);

        return back()->with('success', "Village \"{$village->name}\" created.");
    }

    public function updateVillage(Request $request, Village $village): RedirectResponse
    {
        abort_unless($request->user()->can('routes.edit'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        $village->update($data);

        return back()->with('success', "Village \"{$village->name}\" updated.");
    }

    public function destroyVillage(Request $request, Village $village): RedirectResponse
    {
        abort_unless($request->user()->can('routes.delete'), 403);

        if ($village->customers()->exists()) {
            return back()->with('error', "Can't delete \"{$village->name}\" — customers are still assigned to it.");
        }

        $village->delete();

        return back()->with('success', 'Village deleted.');
    }

    // ---------- Delivery Routes ----------

    public function storeRoute(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('routes.create'), 403);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:routes,code'],
            'name' => ['required', 'string', 'max:150'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'delivery_boy_id' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string'],
        ]);

        $route = DeliveryRoute::create($data);
        ActivityLog::record('created', $route, new: $route->toArray(), description: "Route {$route->name} created");

        return back()->with('success', "Route \"{$route->name}\" created.");
    }

    public function updateRoute(Request $request, DeliveryRoute $route): RedirectResponse
    {
        abort_unless($request->user()->can('routes.edit'), 403);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('routes', 'code')->ignore($route->id)],
            'name' => ['required', 'string', 'max:150'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'delivery_boy_id' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        $route->update($data);

        return back()->with('success', "Route \"{$route->name}\" updated.");
    }

    public function destroyRoute(Request $request, DeliveryRoute $route): RedirectResponse
    {
        abort_unless($request->user()->can('routes.delete'), 403);

        if ($route->customers()->exists()) {
            return back()->with('error', "Can't delete \"{$route->name}\" — customers are still assigned to it.");
        }

        $route->delete();

        return back()->with('success', 'Route deleted.');
    }
}
