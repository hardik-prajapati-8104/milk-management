@php
    $checked = $checked ?? [];
@endphp
<div class="table-responsive">
    <table class="table table-sm table-bordered align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Module</th>
                <th class="text-center">Select All</th>
                @foreach(['view', 'create', 'edit', 'delete', 'export', 'print', 'generate', 'lock-override'] as $action)
                    <th class="text-center small">{{ ucfirst(str_replace('-', ' ', $action)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($permissionGroups as $module => $permissions)
                <tr>
                    <td class="fw-semibold text-capitalize">{{ str_replace('-', ' ', $module) }}</td>
                    <td class="text-center"><input type="checkbox" class="form-check-input js-select-row"></td>
                    @foreach(['view', 'create', 'edit', 'delete', 'export', 'print', 'generate', 'lock-override'] as $action)
                        @php $permName = "{$module}.{$action}"; $exists = $permissions->firstWhere('name', $permName); @endphp
                        <td class="text-center">
                            @if($exists)
                                <input type="checkbox" class="form-check-input js-perm-checkbox" name="permissions[]" value="{{ $permName }}"
                                       {{ in_array($permName, $checked, true) ? 'checked' : '' }}>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.js-select-row').forEach(rowCheckbox => {
        rowCheckbox.addEventListener('change', function () {
            const row = this.closest('tr');
            row.querySelectorAll('.js-perm-checkbox').forEach(cb => { cb.checked = this.checked; });
        });
    });
</script>
@endpush
