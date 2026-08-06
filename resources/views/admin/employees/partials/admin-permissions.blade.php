@php
    $selectedPermissions = old('permissions', $selectedPermissions ?? []);
@endphp
<div id="adminPermissionsSection" class="col-12 mt-2" style="display:none;">
    <div class="border rounded-3 p-4 bg-light">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h5 class="mb-1 fw-bold">Permission Matrix</h5>
                <small class="text-muted">Select exactly what this admin can access and manage.</small>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllPermissions">Select All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllPermissions">Clear All</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Module</th>
                        @foreach(config('admin_permissions.actions') as $actionLabel)
                            <th class="text-center">{{ $actionLabel }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach(config('admin_permissions.modules') as $module => $moduleLabel)
                        <tr>
                            <td class="fw-semibold">{{ $moduleLabel }}</td>
                            @foreach(config('admin_permissions.actions') as $action => $actionLabel)
                                <td class="text-center">
                                    <input class="form-check-input admin-permission-checkbox" type="checkbox"
                                        name="permissions[{{ $module }}][{{ $action }}]" value="1"
                                        data-module="{{ $module }}" data-action="{{ $action }}"
                                        @checked(data_get($selectedPermissions, "$module.$action"))>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@once
    <script>
            document.addEventListener('DOMContentLoaded', function () {
                const role = document.querySelector('select[name="role"]');
                const section = document.getElementById('adminPermissionsSection');
                const tabButton = document.getElementById('permissions-tab');
                const boxes = document.querySelectorAll('.admin-permission-checkbox');
                const toggle = () => {
                    const isAdmin = role && role.value === 'Admin';
                    section.style.display = isAdmin ? '' : 'none';
                    if (tabButton) tabButton.style.display = isAdmin ? '' : 'none';
                    if (!isAdmin && tabButton?.classList.contains('active')) {
                        bootstrap.Tab.getOrCreateInstance(document.getElementById('part1-tab')).show();
                    }
                };

                role?.addEventListener('change', toggle);
                document.getElementById('selectAllPermissions')?.addEventListener('click', () => boxes.forEach(box => box.checked = true));
                document.getElementById('clearAllPermissions')?.addEventListener('click', () => boxes.forEach(box => box.checked = false));
                toggle();
            });
    </script>
@endonce
