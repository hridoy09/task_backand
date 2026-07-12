@extends('admin.layouts.app')

@section('content')
  <x-page-header page_title="Notification Sender" />

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.notification.send') }}" method="POST">
        @csrf

        <div class="row">
          <div class="col-lg-12">
             <x-form.group for="user_group" label="User Group">
                <select name="user_group" class="form-control select2" data-placeholder="@lang('Select a User Group')" id="user_group">
                    @foreach ($userGroups as $key => $userGroupName)
                        <option value="{{ $key }}">
                            {{ $userGroupName }}
                        </option>
                    @endforeach
                </select>
            </x-form.group>
          </div>
          
          <div class="col-lg-12">
            <x-form.group label="Email Addresses" for="emails">
              <x-form.input
                type="text"
                name="emails"
                placeholder="Comma separated email addresses, e.g. user1@example.com, user2@example.com"
                required
              />
            </x-form.group>
          </div>

          <div class="col-lg-12">
            <x-form.group label="Subject" for="subject">
              <x-form.input
                type="text"
                name="subject"
                placeholder="Please enter the email subject"
                required
              />
            </x-form.group>
          </div>
          
          <div class="col-12">
            <x-form.group label="Email Body">
              <textarea
                id="editor"
                name="mail_body"
                class="form-control"
                placeholder="Write your email here"
              ></textarea>
            </x-form.group>
          </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
          <x-button type="submit">
            <x-icons.send /> Send Email
          </x-button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/tagify.css') }}">
@endpush

@push('scripts')
  <script src="{{ asset('assets/admin/js/ckeditor.js') }}"></script>
  <script src="{{ asset('assets/admin/js/tagify.js') }}"></script>

  <script>
    SystemHelper.initEditor('#editor');

    new Tagify(document.querySelector('[name="emails"]'))
  </script>
@endpush
