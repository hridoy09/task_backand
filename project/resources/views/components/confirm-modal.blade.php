<div class="modal fade" id="confirmModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">@lang('Confirmation Modal')</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-0 p-0">
                        @lang('Are you sure you want to proceed with this action?')
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger"
                        data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn-outline-theme">@lang('Yes')</button>
                </div>
            </form>

        </div>
    </div>
</div>


@push('scripts')
    <script>
        $('.confirmBtn').on('click', function() {
            var actionUrl = $(this).data('action');
            var question = $(this).data('question') || "@lang('Are you sure you want to proceed with this action?')";
            var modal = $('#confirmModal');
            modal.find('.modal-body p').text(question);
            $('#deleteForm').attr('action', actionUrl);
            modal.modal('show');
        });
    </script>
@endpush
