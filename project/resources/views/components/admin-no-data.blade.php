@props(['title' => __('No Data Available')])

<div class="admin-no-data d-flex flex-column align-items-center justify-content-center text-center">
    <div class="no-data-icon mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="110" height="110" viewBox="0 0 200 80">
            <defs>
                <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:var(--primary-accent);stop-opacity:0.9" />
                    <stop offset="100%" style="stop-color:#6610f2;stop-opacity:0.9" />
                </linearGradient>
            </defs>
            <g fill="url(#grad1)">
                <path d="M50 60 L100 30 L150 60 L150 130 L50 130 Z" opacity="0.15" />
                <rect x="55" y="65" width="90" height="60" rx="8" ry="8" opacity="0.25" />
                <path d="M65 80 h70 v4 h-70z M65 95 h50 v4 h-50z M65 110 h40 v4 h-40z" opacity="0.5" />
                <circle cx="100" cy="45" r="8" opacity="0.3" />
            </g>
        </svg>
    </div>
    <h5 class="fw-semibold mb-2 text-dark">{{ $title }}</h5>
    <p class="text-muted mb-0">@lang('There’s nothing to show here right now!')</p>
</div>

@once
    @push('styles')
        <style>
            .admin-no-data {
                min-height: 60vh;
                background: var(--bs-body-bg);
                border-radius: 16px;
                border: 1px dashed rgba(0, 0, 0, 0.08);
                transition: 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .admin-no-data::after {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: radial-gradient(circle at 50% 0%, rgba(0, 123, 255, 0.04), transparent 70%);
                pointer-events: none;
            }

            .admin-no-data:hover {
                background: var(--bs-light);
                border-color: rgba(0, 0, 0, 0.1);
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
                transform: translateY(-2px);
            }

            .admin-no-data .no-data-icon {
                animation: floatIcon 3s ease-in-out infinite;
            }

            @keyframes floatIcon {
                0% {
                    transform: translateY(0);
                }

                50% {
                    transform: translateY(-8px);
                }

                100% {
                    transform: translateY(0);
                }
            }

            [data-bs-theme="dark"] .admin-no-data {
                background: #1e1e1e;
                border-color: rgba(255, 255, 255, 0.1);
            }

            [data-bs-theme="dark"] .admin-no-data::after {
                background: radial-gradient(circle at 50% 0%, rgba(0, 123, 255, 0.07), transparent 70%);
            }

            [data-bs-theme="dark"] .admin-no-data h5 {
                color: #f8f9fa;
            }

            [data-bs-theme="dark"] .admin-no-data p {
                color: #b5b5b5;
            }
        </style>
    @endpush
@endonce
