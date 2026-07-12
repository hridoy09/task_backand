@extends('admin.layouts.master')
@section('content')
    @php
        $statusBadge = $model->statusBadge ?? '<span class="badge badge-secondary">—</span>';
        $priorityBadge = $model->priorityBadge ?? '<span class="badge badge-primary">Normal</span>';
    @endphp


        {{-- HEADER --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-header__title">{{ __('Support Ticket Details') }}</h5>
            </div>
            <div class="card-body">

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        {{-- Nice pills for status & priority --}}
                        <div>{!! $statusBadge !!}</div>
                        <div>{!! $priorityBadge !!}</div>
                        <span class="text-muted">•</span>
                        <div class="meta-small">
                            <strong class="text-dark">{{ __('Department') }}:</strong>
                            {{ @$model->department?->name ?? __('N/A') }}
                        </div>
                    </div>

                    <div class="d-flex align-items-center  flex-wrap gap-2 header-actions">
                        {{-- Priority update --}}
                        <form action="{{ route('admin.support_ticket.priority', @$model?->id ?? null) }}" method="POST"
                            class="d-flex align-items-center  gap-2">
                            @csrf
                            <div class="form-group m-0">
                                <select data-search="false" class="js-select2" name="priority">
                                    @foreach ([0 => 'Low', 1 => 'Normal', 2 => 'High', 3 => 'Urgent'] as $k => $v)
                                        <option value="{{ $k }}" @selected($model?->priority == $k)>@lang($v)</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-sm btn-outline-theme" type="submit">
                                <i class="fas fa-sync"></i>{{ __('Update') }}
                            </button>
                        </form>

                        @if ($model->status !== 0)
                            <button data-action="{{ route('admin.support_ticket.close', $model->id) }}" data-question="{{ __('Are you sure you want to close this ticket?') }}" class="btn btn-sm btn-outline-danger confirmBtn">
                                <i class="fas fa-times"></i>
                                {{ __('Close') }}
                            </button>
                        @else
                            <button data-action="{{ route('admin.support_ticket.reopen', $model->id) }}" data-question="{{ __('Are you sure you want to reopen this ticket?') }}"
                                class="btn btn-sm btn-outline-success confirmBtn">
                                <i class="fas fa-redo"></i>
                                {{ __('Reopen') }}
                            </button>
                        @endif
                    </div>
                </div>

                <div class="mt-3">
                    <h3 class="mb-1">{{ $model->title }}</h3>
                    <div class="meta-small">
                        <strong class="text-dark">{{ __('Created') }}</strong>:
                        {{ System::getDateTime($model->created_at) }}
                        • <strong class="text-dark">{{ __('Updated') }}</strong>:
                        {{ System::getDateTime($model->updated_at) }}
                        @if ($model->last_replied_at)
                            • <strong class="text-dark">{{ __('Last reply') }}</strong>:
                            {{ $model->last_replied_at->diffForHumans() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- CONVERSATION CARD --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-header__title" >{{ __('Conversation') }}</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    {{-- initial ticket --}}
                    <div class="timeline-item ticket">
                        <div class="dot"></div>
                        <div class="bubble">
                            <div class="meta-small mb-1">
                                <span class="badge bg-secondary me-2">{{ __('Ticket') }}</span>
                                {{ System::getDateTime($model->created_at) }}
                            </div>
                            <div class="text-dark">{!! nl2br(e($model->body)) !!}</div>

                            @if (!empty($model->attachments))
                                <div class="mt-2 d-flex flex-wrap gap-2">
                                    @foreach ($model->attachments as $path)
                                        @php $href = support_attachment_url($path); @endphp
                                        @if ($href)
                                            <a class="attach-pill" href="{{ $href }}" target="_blank">
                                                <i class="fas fa-paperclip"></i> {{ __('Attachment') }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- replies oldest -> newest --}}
                    @foreach ($model->replies()->oldest('id')->get() as $r)
                        <div class="timeline-item {{ $r->is_admin ? 'admin' : '' }}">
                            <div class="dot"></div>
                            <div class="bubble {{ $r->is_admin ? 'admin' : '' }}">
                                <div class="d-flex flex-wrap align-items-center gap-2 meta-small mb-1">
                                    <strong class="text-dark">{{ $r->replierName() }}</strong>
                                    <span>• {{ System::getDateTime($r->created_at) }}</span>
                                    @if ($r->is_admin)
                                        <span class="badge badge-theme">{{ __('Admin Reply') }}</span>
                                    @endif
                                </div>

                                <div class="text-dark">{!! nl2br(e($r->message)) !!}</div>

                                @if ($r->attachments)
                                    <div class="mt-2 d-flex flex-wrap gap-2">
                                        @foreach ($r->attachments as $path)
                                            @php $href = support_attachment_url($path); @endphp
                                            @if ($href)
                                                <a class="attach-pill" href="{{ $href }}" target="_blank">
                                                    <i class="bi bi-paperclip"></i> {{ __('Attachment') }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- REPLY CARD --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-header__title">{{ __('Post a Reply') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.support_ticket.reply', $model->id) }}" method="POST"
                    enctype="multipart/form-data" class="row g-3">
                    @csrf

                    <div class="col-12">
                        <label class="form-label fw-semibold">{{ __('Your Reply') }}</label>
                        <textarea name="message" rows="5" required class="form-control" placeholder="{{ __('Write your reply...') }}"></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">{{ __('Attachments') }}</label>
                        <input type="file" name="attachments[]" multiple class="form-control" />
                        <div class="form-text">{{ __('You can upload multiple files (max 5MB each).') }}</div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-outline-theme">
                            <i class="fas fa-paper-plane"></i>
                            {{ __('Send Reply') }}
                        </button>


                    </div>
                </form>
            </div>
        </div>
  
    <x-confirm-modal />
@endsection
    @push('styles')
        <style>
            .card-soft {
                border-radius: 1rem;
                box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
            }

            .badge-pill {
                border-radius: 2rem;
                padding: .4rem .7rem;
            }

            .timeline {
                position: relative;
            }

            .timeline::before {
                content: "";
                position: absolute;
                left: 28px;
                top: 0;
                bottom: 0;
                width: 2px;
                background: rgba(0, 0, 0, .06);
            }

            .timeline-item {
                position: relative;
                padding-left: 70px;
                margin-bottom: 1rem;
            }

            .timeline-item .dot {
                position: absolute;
                left: 20px;
                top: .45rem;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                background: #914CFF;
                box-shadow: 0 0 0 4px rgba(13, 110, 253, .15);
            }

            .timeline-item.admin .dot {
                background: EDE2FF;
                box-shadow: 0 0 0 4px rgba(13, 202, 240, .2);
            }

            .timeline-item.ticket .dot {
                background: #adb5bd;
                box-shadow: 0 0 0 4px rgba(173, 181, 189, .25);
            }

            .bubble {
                background: #fff;
                border: 1px solid rgba(0, 0, 0, .07);
                border-radius: .8rem;
                padding: 1rem 1.1rem;
            }

            .bubble.admin {
                background: #EDE2FF;
                border-color: #914CFF;
            }

            .attach-pill {
                display: inline-flex;
                align-items: center;
                gap: .35rem;
                font-size: .825rem;
                padding: .35rem .6rem;
                border: 1px solid rgba(0, 0, 0, .08);
                border-radius: .6rem;
                background: #fff;
                text-decoration: none;
            }

            .attach-pill:hover {
                background: #f8f9fa;
            }

            .meta-small {
                color: #6c757d;
                font-size: .85rem;
            }

            .header-actions .btn,
            .header-actions select {
                height: 40px;
            }
        </style>
    @endpush