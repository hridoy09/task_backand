@extends('admin.layouts.master')

@section('content')
    @php
        $selectedAbilities = isset($role) ? $role->abilities->pluck('id')->all() : [];

        $knownActions = [
            'view',
            'save',
            'delete',
            'manage',
            'create',
            'edit',
            'update',
            'store',
            'list',
            'read',
            'export',
            'archive',
            'approve',
            'reject',
        ];

        $matrix = [];
        $actionNames = collect();

        foreach ($abilities as $ability) {
            $name = $ability->name;

            if (str_contains($name, '-')) {
                [$action, $resource] = explode('-', $name, 2);

                $resourceFirstSegment = explode('.', $resource, 2)[0];
                if (!in_array($action, $knownActions, true) && in_array($resourceFirstSegment, $knownActions, true)) {
                    $originalAction = $action;
                    $action = $resourceFirstSegment;

                    $resourceRemainder = '';
                    if (strlen($resource) > strlen($resourceFirstSegment)) {
                        $resourceRemainder = substr($resource, strlen($resourceFirstSegment));
                    }

                    $resource = $originalAction . $resourceRemainder;
                }
            } else {
                $action = 'custom';
                $resource = $name;
            }

            $resourceKey = $resource;
            $resourceSlug = \Illuminate\Support\Str::slug($resourceKey, '_');

            $matrix[$resourceKey]['label'] = \Illuminate\Support\Str::headline(
                str_replace(['.', '_'], ' ', $resourceKey),
            );
            $matrix[$resourceKey]['slug'] = $resourceSlug;
            $matrix[$resourceKey]['abilities'][$action] = $ability;

            $actionNames->push($action);
        }

        $actionPriority = [
            'all',
            'team',
            'only',
            'view',
            'read',
            'save',
            'create',
            'edit',
            'update',
            'delete',
            'manage',
            'archive',
            'export',
        ];
        $priorityLookup = array_flip($actionPriority);
        ksort($matrix);

        $actions = $actionNames
            ->unique()
            ->sort(function ($a, $b) use ($priorityLookup) {
                $pa = $priorityLookup[$a] ?? 100 + ord(substr($a, 0, 1));
                $pb = $priorityLookup[$b] ?? 100 + ord(substr($b, 0, 1));

                if ($pa === $pb) {
                    return strcmp($a, $b);
                }

                return $pa <=> $pb;
            })
            ->values();
    @endphp

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('admin.acl.role.store', @$role->id) }}" id="role-form">
                @csrf

                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-header__title mb-0">@lang('Role Details')</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label>@lang('Role Name')</label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="@lang('Enter a unique role name')" value="{{ old('name', @$role->name) }}" />
                                </div>
                                <small class="text-muted d-block mt-2">
                                    @lang('Use a short, unique system key such as super-admin or editor.')
                                </small>
                            </div>

                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label>@lang('Title')</label>
                                    <input type="text" name="title" class="form-control"
                                        placeholder="@lang('Enter display title')" value="{{ old('title', @$role->title) }}" />
                                </div>
                                <small class="text-muted d-block mt-2">
                                    @lang('Shown everywhere this role is referenced in the UI.')
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

        
                <div class="card mt-3">
                    <div class="card-body p-0">
                        <div class="table-wrapper table-responsive">
                            <table class="table theme-tab-listle responsive-table-sm">
                                <thead>
                                    <tr>
                                        <th>@lang('Module')</th>
                                        <th>
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                <span>@lang('All')</span>
                                                <label class="permission-checkbox small">
                                                    <input type="checkbox" class="action-toggle" data-action="row-all">
                                                    <span></span>
                                                </label>
                                            </div>
                                        </th>
                                        @foreach ($actions as $action)
                                            <th>
                                                <div class="d-flex flex-column align-items-center gap-1">
                                                    <span>{{ \Illuminate\Support\Str::headline($action) }}</span>
                                                    <label class="permission-checkbox small">
                                                        <input type="checkbox" class="action-toggle"
                                                            data-action="{{ $action }}">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($matrix as $resourceKey => $resourceData)
                                        @php
                                            $slug = $resourceData['slug'];
                                            $rowAbilities = $resourceData['abilities'] ?? [];
                                        @endphp
                                        <tr class="permission-row" data-resource="{{ $slug }}">
                                            <td class="fw-semibold text-nowrap">{{ $resourceData['label'] }}</td>
                                            <td class="text-center">
                                                <label class="permission-checkbox">
                                                    <input type="checkbox" class="row-toggle"
                                                        data-resource="{{ $slug }}">
                                                    <span></span>
                                                </label>
                                            </td>
                                            @foreach ($actions as $action)
                                                @php
                                                    $ability = $rowAbilities[$action] ?? null;
                                                @endphp
                                                <td class="text-center">
                                                    @if ($ability)
                                                        <label class="permission-checkbox">
                                                            <input type="checkbox" class="permission-action"
                                                                data-resource="{{ $slug }}"
                                                                data-action="{{ $action }}" name="abilities[]"
                                                                value="{{ $ability->id }}"
                                                                {{ in_array($ability->id, $selectedAbilities, true) ? 'checked' : '' }}>
                                                            <span></span>
                                                        </label>
                                                    @else
                                                        <span class="text-muted">&mdash;</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-outline-theme px-4">
                        <i class="fas fa-save"></i>
                        @lang('Save Role')
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('breadcrumb')
    <x-back :link="route('admin.acl.role.list')" />
@endpush

@push('styles')
    <style>
        .permissions-table thead th {
            border-bottom: 1px solid #eef2f8;
            background: #f9fbff;
        }

        .permissions-table tbody tr+tr td {
            border-top: 1px solid rgba(230, 234, 243, 0.7);
        }

        .permissions-table td,
        .permissions-table th {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .permission-checkbox {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 6px;
            border: 1px solid #d6dcf3;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .permission-checkbox.small {
            width: 20px;
            height: 20px;
        }

        .permission-checkbox input {
            position: absolute;
            opacity: 0;
            inset: 0;
            cursor: pointer;
        }

        .permission-checkbox span {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            background: transparent;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .permission-checkbox input:checked+span {
            background:  hsl(var(--theme-color));
            box-shadow: inset 0 0 0 3px #fff;
        }

        .permission-checkbox input:indeterminate+span {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.55), rgba(79, 70, 229, 0.55));
        }

        .permission-checkbox:hover {
            border-color:  hsl(var(--theme-color));
        }

        .permission-row:hover td:first-child {
            color:  hsl(var(--theme-color));
        }

        @media (max-width: 991px) {

            .permissions-table thead th:first-child,
            .permissions-table tbody td:first-child {
                position: sticky;
                left: 0;
                background: #fff;
                z-index: 1;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            const rowToggles = document.querySelectorAll('.row-toggle');
            const actionToggles = document.querySelectorAll('.action-toggle');
            const abilityCheckboxes = document.querySelectorAll('.permission-action');
            const allToggle = document.querySelector('.action-toggle[data-action="row-all"]');

            function updateAllState() {
                if (!allToggle || !abilityCheckboxes.length) {
                    return;
                }

                const checked = Array.from(abilityCheckboxes).filter(cb => cb.checked);
                allToggle.checked = checked.length === abilityCheckboxes.length;
                allToggle.indeterminate = checked.length > 0 && checked.length < abilityCheckboxes.length;
            }

            function updateRowState(resource) {
                const rowCheckboxes = document.querySelectorAll(`.permission-action[data-resource="${resource}"]`);
                const rowToggle = document.querySelector(`.row-toggle[data-resource="${resource}"]`);

                if (!rowToggle || !rowCheckboxes.length) {
                    return;
                }

                const checked = Array.from(rowCheckboxes).filter(cb => cb.checked);
                rowToggle.checked = checked.length === rowCheckboxes.length;
                rowToggle.indeterminate = checked.length > 0 && checked.length < rowCheckboxes.length;
            }

            function updateActionState(action) {
                if (action === 'row-all') {
                    return;
                }

                const actionCheckboxes = document.querySelectorAll(`.permission-action[data-action="${action}"]`);
                const actionToggle = document.querySelector(`.action-toggle[data-action="${action}"]`);

                if (!actionToggle || !actionCheckboxes.length) {
                    return;
                }

                const checked = Array.from(actionCheckboxes).filter(cb => cb.checked);
                actionToggle.checked = checked.length === actionCheckboxes.length;
                actionToggle.indeterminate = checked.length > 0 && checked.length < actionCheckboxes.length;
            }

            rowToggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const resource = this.dataset.resource;
                    const rowCheckboxes = document.querySelectorAll(
                        `.permission-action[data-resource="${resource}"]`);
                    rowCheckboxes.forEach(cb => {
                        cb.checked = toggle.checked;
                        updateActionState(cb.dataset.action);
                    });
                    updateAllState();
                });
            });

            actionToggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const action = this.dataset.action;

                    if (action === 'row-all') {
                        rowToggles.forEach(rowToggle => {
                            rowToggle.checked = toggle.checked;
                            rowToggle.dispatchEvent(new Event('change'));
                        });
                        updateAllState();
                        return;
                    }

                    const actionCheckboxes = document.querySelectorAll(
                        `.permission-action[data-action="${action}"]`);
                    actionCheckboxes.forEach(cb => {
                        cb.checked = toggle.checked;
                        updateRowState(cb.dataset.resource);
                    });
                    updateAllState();
                });
            });

            abilityCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateRowState(this.dataset.resource);
                    updateActionState(this.dataset.action);
                    updateAllState();
                });
            });

            rowToggles.forEach(toggle => updateRowState(toggle.dataset.resource));
            actionToggles.forEach(toggle => {
                if (toggle.dataset.action !== 'row-all') {
                    updateActionState(toggle.dataset.action);
                }
            });
            updateAllState();
        })();
    </script>
@endpush
