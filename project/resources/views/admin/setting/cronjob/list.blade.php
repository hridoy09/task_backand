@extends('admin.layouts.settings')

@section('panel')
    <div class="manage-section-card-form">
        <div class="card manage-section-card mb-32">
            <div class="table-wrapper table-responsive">
                <table class="table theme-tab-listle responsive-table-sm">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Method Name')</th>
                            <th>@lang('Schedule')</th>
                            <th>@lang('Last Run')</th>
                            <th>@lang('Running')</th>
                            <th class="text-end">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cronJobs as $cronJob)
                            <tr>
                                <td>{{ $cronJob->name }}</td>
                                <td>{{ $cronJob->method_name }}</td>
                                <td>{{ $cronJob->schedule->name }}</td>
                                <td>
                                    @if ($cronJob->last_run)
                                        <span>{{ software()->getDateTime($cronJob->last_run) }}</span>
                                        <br>
                                        <strong>{{ $cronJob->last_run->diffForHumans() }}</strong>
                                    @else
                                        {{ '-' }}
                                    @endif
                                </td>
                                <td>@php echo $cronJob->runningBadge @endphp</td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center gap-3 justify-content-end">
                                        @if ($cronJob->running == 1)
                                            <button confirm href="{{ route('admin.setting.cronjob.pause', $cronJob->id) }}"
                                                data-name="{{ $cronJob->name }}" class="btn btn-outline-warning btn-sm">
                                                <x-icons.circle-pause />
                                            </button>
                                        @else
                                            <button confirm href="{{ route('admin.setting.cronjob.running', $cronJob->id) }}"
                                                class="btn btn-outline-success btn-sm" data-name="{{ $cronJob->name }}">
                                                <x-icons.circle-play />
                                            </button>
                                        @endif

                                        <button data-cronJobSchedule="{{ $cronJob->cron_job_schedule_id }}"
                                            class="editBtn btn btn-outline-primary btn-sm"
                                            data-action="{{ route('admin.setting.cronjob.save', $cronJob->id) }}"
                                            data-name="{{ $cronJob->name }}">
                                            <x-icons.edit />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">
                                    <x-admin-no-data />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-admin-paginate :model="$cronJobs" />
        </div>
    </div>

    <x-modal id="setupModal" title="Setup Cron Job">
        <small>@lang('Just copy the following command & paste on your cPanel cron job setting')</small>

        <div class="input-group mt-2">

            <input     id="cronCommand" name="url" class="form-control" value="curl -s {{ route('cronjob') }} > /dev/null 2>&1" type="text">

        
            <button type="button" class="btn btn-outline-theme" id="copyCron">
                <i class="fas fa-copy"></i>
            </button>
        </div>
    </x-modal>
    
    <x-modal id="cronModal" title="Edit Cron Job" :form="true" method="POST">
        @csrf

        <x-form.group>
            <x-form.label>@lang('Name')</x-form.label>
            <x-form.input required name="name" :placeholder="__('Enter the cron job\'s name')" />
        </x-form.group>

        <x-form.group>
            <x-form.label>@lang('Cron Job Schedule')</x-form.label>
            <select name="cron_job_schedule_id" id="cron_job_schedule_id" class="form-control" required>
                @foreach($cronJobSchedules as $cronJobSchedule)
                    <option value="{{ $cronJobSchedule->id }}">{{ $cronJobSchedule->name }}</option>
                @endforeach
            </select>
        </x-form.group>

        <x-slot:footer>
            <button class="w-100" type="submit">
                <i class="fas fa-save"></i>
                @lang('Save')
            </button>
        </x-slot:footer>
    </x-modal>
@endsection

@push('breadcrumb')
    <button class="setup-btn btn btn-outline-theme">
        <i class="fas fa-cog"></i>
        @lang('Setup')
    </button>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.setup-btn').on('click', function(){
                $('#setupModal').modal('show');
            });

            $('#copyCron').on('click', function () {
                let cronCommand = document.getElementById('cronCommand');
                cronCommand.select();
                cronCommand.setSelectionRange(0, 99999); // for mobile devices
                navigator.clipboard.writeText(cronCommand.value).then(() => {
                    toastr.success("Cron command copied to clipboard!"); 
                }).catch(() => {
                    alert("Failed to copy. Please copy manually.");
                });
            });

            $('.editBtn').on('click', function() {
                const data = $(this).data();
                $('#cronModal').find('[name="cron_job_schedule_id"]').val(data.cronjobschedule);
                $('#cronModal').find('[name="name"]').val(data.name);
                $('#cronModal').find('form').first().attr('action', data.action);
                $('#cronModal').modal('show');
            });
        });
    </script>
@endpush
