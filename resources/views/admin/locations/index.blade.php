@extends('layouts.app')

@section('title', 'Routes & Areas')

@section('content')
<h4 class="mb-3">Routes & Areas</h4>

<ul class="nav nav-pills mb-3">
    <li class="nav-item"><a class="nav-link {{ $activeTab === 'areas' ? 'active' : '' }}" href="{{ route('admin.routes.index', ['tab' => 'areas']) }}">Areas</a></li>
    <li class="nav-item"><a class="nav-link {{ $activeTab === 'villages' ? 'active' : '' }}" href="{{ route('admin.routes.index', ['tab' => 'villages']) }}">Villages</a></li>
    <li class="nav-item"><a class="nav-link {{ $activeTab === 'routes' ? 'active' : '' }}" href="{{ route('admin.routes.index', ['tab' => 'routes']) }}">Delivery Routes</a></li>
</ul>

@if($activeTab === 'areas')
    <div class="d-flex justify-content-end mb-2">
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addAreaModal"><i class="bi bi-plus-lg me-1"></i> New Area</button>
    </div>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Name</th><th>City</th><th>State</th><th>Customers</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($areas as $area)
                    <tr>
                        <td>{{ $area->name }}</td>
                        <td>{{ $area->city ?: '—' }}</td>
                        <td>{{ $area->state ?: '—' }}</td>
                        <td>{{ $area->customers_count }}</td>
                        <td><span class="badge text-bg-{{ $area->is_active ? 'success' : 'secondary' }}">{{ $area->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editAreaModal{{ $area->id }}"><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('admin.routes.areas.destroy', $area) }}" method="POST" class="d-inline js-delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="editAreaModal{{ $area->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form class="modal-content" action="{{ route('admin.routes.areas.update', $area) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header"><h6 class="modal-title">Edit Area</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body row g-3">
                                    <div class="col-12"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ $area->name }}" required></div>
                                    <div class="col-6"><label class="form-label">City</label><input type="text" name="city" class="form-control" value="{{ $area->city }}"></div>
                                    <div class="col-6"><label class="form-label">State</label><input type="text" name="state" class="form-control" value="{{ $area->state }}"></div>
                                    <div class="col-6"><label class="form-label">Pincode</label><input type="text" name="pincode" class="form-control" value="{{ $area->pincode }}"></div>
                                    <div class="col-6 d-flex align-items-end">
                                        <div class="form-check form-switch"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked($area->is_active)><label class="form-check-label">Active</label></div>
                                    </div>
                                </div>
                                <div class="modal-footer"><button type="submit" class="btn btn-success btn-sm">Save</button></div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No areas yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addAreaModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('admin.routes.areas.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h6 class="modal-title">New Area</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body row g-3">
                    <div class="col-12"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-6"><label class="form-label">City</label><input type="text" name="city" class="form-control"></div>
                    <div class="col-6"><label class="form-label">State</label><input type="text" name="state" class="form-control"></div>
                    <div class="col-6"><label class="form-label">Pincode</label><input type="text" name="pincode" class="form-control"></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-success btn-sm">Create</button></div>
            </form>
        </div>
    </div>

@elseif($activeTab === 'villages')
    <div class="d-flex justify-content-end mb-2">
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addVillageModal"><i class="bi bi-plus-lg me-1"></i> New Village</button>
    </div>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Name</th><th>Area</th><th>Customers</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($villages as $village)
                    <tr>
                        <td>{{ $village->name }}</td>
                        <td>{{ $village->area->name ?? '—' }}</td>
                        <td>{{ $village->customers_count }}</td>
                        <td><span class="badge text-bg-{{ $village->is_active ? 'success' : 'secondary' }}">{{ $village->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editVillageModal{{ $village->id }}"><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('admin.routes.villages.destroy', $village) }}" method="POST" class="d-inline js-delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="editVillageModal{{ $village->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form class="modal-content" action="{{ route('admin.routes.villages.update', $village) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header"><h6 class="modal-title">Edit Village</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body row g-3">
                                    <div class="col-12"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ $village->name }}" required></div>
                                    <div class="col-12">
                                        <label class="form-label">Area</label>
                                        <select name="area_id" class="form-select">
                                            <option value="">— None —</option>
                                            @foreach($areas as $area)
                                                <option value="{{ $area->id }}" @selected($village->area_id == $area->id)>{{ $area->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked($village->is_active)><label class="form-check-label">Active</label></div>
                                    </div>
                                </div>
                                <div class="modal-footer"><button type="submit" class="btn btn-success btn-sm">Save</button></div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No villages yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addVillageModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('admin.routes.villages.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h6 class="modal-title">New Village</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body row g-3">
                    <div class="col-12"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-12">
                        <label class="form-label">Area</label>
                        <select name="area_id" class="form-select">
                            <option value="">— None —</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-success btn-sm">Create</button></div>
            </form>
        </div>
    </div>

@else
    <div class="d-flex justify-content-end mb-2">
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addRouteModal"><i class="bi bi-plus-lg me-1"></i> New Route</button>
    </div>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Area</th><th>Delivery Boy</th><th>Customers</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($routes as $route)
                    <tr>
                        <td>{{ $route->code }}</td>
                        <td>{{ $route->name }}</td>
                        <td>{{ $route->area->name ?? '—' }}</td>
                        <td>{{ $route->deliveryBoy->name ?? '—' }}</td>
                        <td>{{ $route->customers_count }}</td>
                        <td><span class="badge text-bg-{{ $route->is_active ? 'success' : 'secondary' }}">{{ $route->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRouteModal{{ $route->id }}"><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('admin.routes.delivery-routes.destroy', $route) }}" method="POST" class="d-inline js-delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="editRouteModal{{ $route->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form class="modal-content" action="{{ route('admin.routes.delivery-routes.update', $route) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header"><h6 class="modal-title">Edit Route</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body row g-3">
                                    <div class="col-6"><label class="form-label">Code</label><input type="text" name="code" class="form-control" value="{{ $route->code }}" required></div>
                                    <div class="col-6"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ $route->name }}" required></div>
                                    <div class="col-6">
                                        <label class="form-label">Area</label>
                                        <select name="area_id" class="form-select">
                                            <option value="">— None —</option>
                                            @foreach($areas as $area)
                                                <option value="{{ $area->id }}" @selected($route->area_id == $area->id)>{{ $area->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Delivery Boy</label>
                                        <select name="delivery_boy_id" class="form-select">
                                            <option value="">— Unassigned —</option>
                                            @foreach($deliveryBoys as $boy)
                                                <option value="{{ $boy->id }}" @selected($route->delivery_boy_id == $boy->id)>{{ $boy->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2">{{ $route->description }}</textarea></div>
                                    <div class="col-12">
                                        <div class="form-check form-switch"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked($route->is_active)><label class="form-check-label">Active</label></div>
                                    </div>
                                </div>
                                <div class="modal-footer"><button type="submit" class="btn btn-success btn-sm">Save</button></div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No delivery routes yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addRouteModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('admin.routes.delivery-routes.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h6 class="modal-title">New Route</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body row g-3">
                    <div class="col-6"><label class="form-label">Code</label><input type="text" name="code" class="form-control" required></div>
                    <div class="col-6"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-6">
                        <label class="form-label">Area</label>
                        <select name="area_id" class="form-select">
                            <option value="">— None —</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Delivery Boy</label>
                        <select name="delivery_boy_id" class="form-select">
                            <option value="">— Unassigned —</option>
                            @foreach($deliveryBoys as $boy)
                                <option value="{{ $boy->id }}">{{ $boy->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-success btn-sm">Create</button></div>
            </form>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
$(document).on('submit', '.js-delete-form', function (e) {
    e.preventDefault();
    const form = this;
    Swal.fire({
        title: 'Delete this record?', icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Yes, delete', confirmButtonColor: '#dc3545',
    }).then((r) => { if (r.isConfirmed) form.submit(); });
});
</script>
@endpush
