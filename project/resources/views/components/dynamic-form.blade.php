@if($form)
    {{-- <form method="POST" action="{{ route('home', $form->slug) }}" enctype="multipart/form-data">
        @csrf --}}

        @foreach($fields as $field)
            <div class="mb-3">
                @php
                    $name = $field['name'] ?? '';
                    $oldValue = old($name);
                @endphp

                {{-- Text --}}
                @if($field['type'] === 'text')
                    <label class="form-label">{{ $field['label'] ?? 'Text Field' }}</label>
                    <input type="text" 
                           name="{{ $name }}" 
                           class="form-control"
                           placeholder="{{ $field['placeholder'] ?? '' }}"
                           value="{{ $oldValue ?? $field['value'] ?? '' }}"
                           autocomplete="{{ $field['autocomplete'] ?? 'on' }}">
                @endif

                {{-- Number --}}
                @if($field['type'] === 'number')
                    <label class="form-label">{{ $field['label'] ?? 'Number' }}</label>
                    <input type="number" 
                           name="{{ $name }}" 
                           class="form-control"
                           min="{{ $field['min'] ?? '' }}"
                           max="{{ $field['max'] ?? '' }}"
                           step="{{ $field['step'] ?? '1' }}"
                           value="{{ $oldValue ?? $field['value'] ?? '' }}">
                @endif

                {{-- Textarea --}}
                @if($field['type'] === 'textarea')
                    <label class="form-label">{{ $field['label'] ?? 'Textarea' }}</label>
                    <textarea name="{{ $name }}" 
                              class="form-control"
                              rows="{{ $field['rows'] ?? 3 }}"
                              placeholder="{{ $field['placeholder'] ?? '' }}">{{ $oldValue ?? $field['value'] ?? '' }}</textarea>
                @endif

                {{-- Select --}}
                @if($field['type'] === 'select')
                    <label class="form-label">{{ $field['label'] ?? 'Select' }}</label>
                    <select name="{{ $name }}" class="form-select">
                        @foreach($field['values'] ?? [] as $option)
                            @php
                                $value = $option['value'] ?? $option['label'];
                                $selected = ($oldValue !== null ? $oldValue : $field['value'] ?? '') == $value;
                            @endphp
                            <option value="{{ $value }}" {{ $selected ? 'selected' : '' }}>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>
                @endif

                {{-- Radio Group --}}
                @if($field['type'] === 'radio-group')
                    <label class="form-label">{{ $field['label'] ?? 'Choose Option' }}</label><br>
                    @foreach($field['values'] ?? [] as $option)
                        @php
                            $value = $option['value'] ?? $option['label'];
                            $checked = ($oldValue !== null ? $oldValue : $field['value'] ?? '') == $value;
                        @endphp
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="{{ $name }}" 
                                   value="{{ $value }}"
                                   {{ $checked ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $option['label'] }}</label>
                        </div>
                    @endforeach
                @endif

                {{-- Checkbox Group --}}
                @if($field['type'] === 'checkbox-group')
                    <label class="form-label">{{ $field['label'] ?? 'Select Options' }}</label><br>
                    @foreach($field['values'] ?? [] as $option)
                        @php
                            $value = $option['value'] ?? $option['label'];
                            $checked = is_array($oldValue) ? in_array($value, $oldValue) : false;
                        @endphp
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="{{ $name }}[]" 
                                   value="{{ $value }}"
                                   {{ $checked ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $option['label'] }}</label>
                        </div>
                    @endforeach
                @endif

                {{-- Date --}}
                @if($field['type'] === 'date')
                    <label class="form-label">{{ $field['label'] ?? 'Date' }}</label>
                    <input type="date" 
                           name="{{ $name }}" 
                           class="form-control"
                           value="{{ $oldValue ?? $field['value'] ?? '' }}">
                @endif

                {{-- File --}}
                @if($field['type'] === 'file')
                    <label class="form-label">{{ $field['label'] ?? 'Upload File' }}</label>
                    <input type="file" 
                           name="{{ $name }}" 
                           class="form-control"
                           {{ !empty($field['multiple']) ? 'multiple' : '' }}>
                @endif

                {{-- Hidden --}}
                @if($field['type'] === 'hidden')
                    <input type="hidden" name="{{ $name }}" value="{{ $oldValue ?? $field['value'] ?? '' }}">
                @endif

                {{-- Paragraph --}}
                @if($field['type'] === 'paragraph')
                    <p>{!! $field['label'] ?? '' !!}</p>
                @endif

                {{-- Header --}}
                @if($field['type'] === 'header')
                    <h4>{{ $field['label'] ?? '' }}</h4>
                @endif

                {{-- Button --}}
                @if($field['type'] === 'button')
                    <button type="{{ $field['subtype'] ?? 'button' }}" class="btn btn-secondary">
                        {{ $field['label'] ?? 'Button' }}
                    </button>
                @endif
            </div>
        @endforeach

        {{-- <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </form> --}}
@else
    {{-- <p class="text-muted">No form found for slug <strong>{{ $slug }}</strong>.</p> --}}
@endif
