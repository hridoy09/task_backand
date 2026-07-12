@extends('theme::user.layouts.main')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">{{ $title }}</h2>

    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <form method="POST" action="{{ route('user.support.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf

                {{-- Department --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">@lang('Department')</label>
                    <select name="department_id" class="form-select" required>
                        <option value="">@lang('Select Department')</option>
                        @foreach($departments as $dep)
                            <option value="{{ $dep->id }}" @selected(old('department_id') == $dep->id)>
                                {{ $dep->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Priority --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">@lang('Priority')</label>
                    <select name="priority" class="form-select" required>
                        @foreach ([0 => 'Low', 1 => 'Normal', 2 => 'High', 3 => 'Urgent'] as $k => $v)
                            <option value="{{ $k }}" @selected(old('priority') == $k)>
                                @lang($v)
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Title --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">@lang('Subject')</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                {{-- Message --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">@lang('Message')</label>
                    <textarea name="body" rows="6" class="form-control" placeholder="@lang('Describe your issue in detail')" required>{{ old('body') }}</textarea>
                </div>

                {{-- Attachments --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">@lang('Attachments')</label>
                    <input type="file" name="attachments[]" multiple class="form-control" />
                    <div class="form-text">@lang('You can upload multiple files (max 5MB each).')</div>
                </div>

                {{-- Submit --}}
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        🎫 @lang('Submit Ticket')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
