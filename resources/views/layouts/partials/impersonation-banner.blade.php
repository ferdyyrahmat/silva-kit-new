@if(session()->has('impersonation.original_user_id') && auth()->check())
<div class="alert alert-warning rounded-0 border-0 mb-0 py-2 px-3 d-flex align-items-center justify-content-center gap-3 position-relative" style="z-index: 1040;" role="alert">
    <i class="mdi mdi-account-switch-outline fs-20"></i>
    <span class="fs-13">{{ __('messages.impersonating', ['user' => auth()->user()->name]) }}</span>
    <form method="POST" action="{{ route('impersonation.stop') }}" class="m-0 native-submit-form">
        @csrf
        <button type="submit" class="btn btn-dark btn-sm"><i class="mdi mdi-undo me-1"></i>{{ __('messages.back_to_developer') }}</button>
    </form>
</div>
@endif
