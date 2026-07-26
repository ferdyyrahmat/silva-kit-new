@extends('layouts.vertical', ['title' => 'Database Management'])

@section('content')
<div class="container-fluid">
    <div class="py-3"><h4 class="fs-18 fw-semibold mb-1"><i class="mdi mdi-database-cog-outline me-1"></i>Database Management</h4><p class="text-muted mb-0">Developer-only tools for clearing application data.</p></div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="alert alert-danger border-0"><i class="mdi mdi-alert-outline me-1"></i><strong>Developer-only destructive action.</strong> All application data can be cleared. The Developer account, Developer role, and their relationship are always preserved.</div>

    <form method="POST" action="{{ route('admin.database.clear') }}" class="native-submit-form" id="database-clear-form">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center"><h5 class="mb-0">Application tables</h5><label class="form-check mb-0"><input class="form-check-input" type="checkbox" id="select-all-tables"> <span class="form-check-label">Select all</span></label></div>
            <div class="card-body p-0"><div class="list-group list-group-flush">@forelse($tables as $table)<label class="list-group-item d-flex align-items-center gap-3 py-3"><input class="form-check-input table-check m-0" type="checkbox" name="tables[]" value="{{ $table['name'] }}"><span class="fw-semibold flex-grow-1">{{ $table['name'] }}</span><span class="text-muted fs-12">{{ number_format($table['rows']) }} rows</span></label>@empty<div class="p-3 text-muted">No application tables found.</div>@endforelse</div></div>
            <div class="card-footer bg-transparent"><button type="button" class="btn btn-outline-danger" id="clear-selected"><i class="mdi mdi-delete-sweep-outline me-1"></i>Clear selected tables</button><button type="button" class="btn btn-danger float-end" id="clear-all"><i class="mdi mdi-database-remove-outline me-1"></i>Clear all application data</button></div>
        </div>
        <input type="hidden" name="clear_all" id="clear-all-input" value="0">
        <input type="hidden" name="confirmation" id="confirmation-input">
    </form>
    <div class="alert alert-info mt-3"><i class="mdi mdi-information-outline me-1"></i>The Developer account and role are preserved automatically. Clearing <code>sessions</code> will still end the current login session; clearing <code>migrations</code> requires running migrations again afterward.</div>
</div>
@endsection

@section('script-bottom')
<script>
$(function () {
    $('#select-all-tables').on('change', function () { $('.table-check').prop('checked', this.checked); });
    $('#clear-selected').on('click', function () {
        if (!$('.table-check:checked').length) return Swal.fire('Select a table', 'Choose at least one table to clear.', 'warning');
        Swal.fire({ title: 'Clear selected tables?', text: 'All rows in the selected tables will be deleted.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Yes, clear them' }).then(function (result) { if (result.isConfirmed) $('#database-clear-form').trigger('submit'); });
    });
    $('#clear-all').on('click', function () {
        Swal.fire({ title: 'Clear all application data?', text: 'Type CLEAR ALL DATA to confirm this irreversible action.', input: 'text', inputPlaceholder: 'CLEAR ALL DATA', icon: 'error', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Clear all', preConfirm: function (value) { if (value !== 'CLEAR ALL DATA') { Swal.showValidationMessage('The confirmation text does not match.'); return false; } return value; } }).then(function (result) { if (result.isConfirmed) { $('#clear-all-input').val('1'); $('#confirmation-input').val(result.value); $('#database-clear-form').trigger('submit'); } });
    });
});
</script>
@endsection
