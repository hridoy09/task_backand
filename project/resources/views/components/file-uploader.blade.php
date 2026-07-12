@props(['name', 'label' => '', 'accept' => 'image/*', 'is_dark' => false, 'preview' => null])

<div class="form-group file-uploader-wrapper position-relative">
    @if ($label)
        <label for="{{ $name }}" class="form-label fw-semibold">{{ $label }}</label>
    @endif

    <div class="file-drop-zone text-center {{ $is_dark ? 'dark' : '' }}" id="drop-zone-{{ $name }}">
        <div class="preview-wrapper {{ !$preview ? 'd-none' : '' }}">
            <img 
                src="{{ $preview ? imageSrc($preview) : '' }}" 
                class="img-fluid uploaded-image" 
                style="max-height: 180px;"
                alt="Preview"
            />
            <button 
                type="button"
                class="btn btn-sm btn-danger btn-remove-preview">✖</button>
        </div>

        <!-- Upload UI -->
        <div class="upload-ui">
            <button type="button" class="btn btn-sm btn-primary browse-btn">Browse</button>
            <p class="text-muted mb-1">Drop a file here or click to upload</p>
            <p class="text-muted text-xs">Only .jpg, .png, .webp are supported</p>
        </div>

        <!-- Hidden Input -->
        <input type="file" name="{{ $name }}" id="{{ $name }}" accept="{{ $accept }}"
            class="file-input d-none">
    </div>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>


@pushOnce('scripts')
    <script>
        $(function() {
            $('.file-uploader-wrapper').each(function() {
                const wrapper = $(this);
                const input = wrapper.find('.file-input');
                const dropZone = wrapper.find('.file-drop-zone');
                const uploadUI = dropZone.find('.upload-ui');
                const previewWrapper = dropZone.find('.preview-wrapper');
                const previewImage = previewWrapper.find('.uploaded-image');
                const removeBtn = previewWrapper.find('.btn-remove-preview');

                // Browse click
                dropZone.find('.browse-btn').on('click', function() {
                    input.trigger('click');
                });

                // File selected
                input.on('change', function(e) {
                    const file = this.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImage.attr('src', e.target.result);
                            previewWrapper.removeClass('d-none');
                            uploadUI.hide();
                        };
                        reader.readAsDataURL(file);
                    }
                });

                // Drag and drop
                dropZone.on('dragover dragenter', function(e) {
                    e.preventDefault();
                    dropZone.addClass('drag-over');
                });

                dropZone.on('dragleave dragend drop', function(e) {
                    e.preventDefault();
                    dropZone.removeClass('drag-over');
                });

                dropZone.on('drop', function(e) {
                    const file = e.originalEvent.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        input[0].files = e.originalEvent.dataTransfer.files;
                        input.trigger('change');
                    }
                });

                // Remove preview
                removeBtn.on('click', function() {
                    previewWrapper.addClass('d-none');
                    previewImage.attr('src', '');
                    uploadUI.show();
                    input.val('');
                });
            });
        });
    </script>
@endPushOnce

@pushOnce('styles')
    <style>
        .file-drop-zone {
            position: relative;
            border: 2px dashed #007bff;
            border-radius: 6px;
            padding: 1.5rem;
            background-color: #f8f9fa;
            position: relative;
            min-height: 300px;
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        }

        .file-drop-zone.dark {
            background-color: #2c2f33;
            border-color: #7289da;
        }

         .file-drop-zone.dark * {
            color: #ffffff !important;
         }

         .file-drop-zone.dark .btn-remove-preview {
            color: #991b1b !important;
         }

        .file-drop-zone.drag-over {
            background-color: #e7f1ff;
        }

        .uploaded-image {
            max-width: 100%;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .btn-remove-preview {
            position: absolute;
            top: 5px;
            right: 10px;
            background-color: #d8d8d8;
        }
    </style>
@endPushOnce
