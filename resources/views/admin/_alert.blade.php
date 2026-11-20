@if(session('success'))
    <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 py-2 mb-3" role="alert">
        <i class="bi bi-check-circle-fill flex-shrink-0"></i>
        <span class="small">{{ session('success') }}</span>
        <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 py-2 mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
        <span class="small">{{ session('error') }}</span>
        <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif
