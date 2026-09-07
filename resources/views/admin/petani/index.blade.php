@extends('layout.admin')

@section('title', 'Data Petani')
@section('page-title', 'Data Petani')
@section('page-subtitle', 'Manajemen data petani dan lahan mitra')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom:16px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom:16px;">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
    <div class="toolbar" style="display:flex;flex-wrap:wrap;align-items:center;gap:12px;margin-bottom:18px;">
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <input type="text" id="searchInput" placeholder="Cari petani, kontak..." style="padding:10px 12px;border:1px solid var(--border);border-radius:8px;min-width:220px;">
            <select id="filterKomoditas" onchange="filterTable()" style="padding:10px 12px;border:1px solid var(--border);border-radius:8px;min-width:180px;">
                <option value="">Semua Komoditas</option>
                <option value="Padi">Padi</option>
            </select>
        </div>

        <div style="margin-left:auto;display:flex;gap:10px;flex-wrap:wrap;">
            <button class="btn btn-primary" onclick="openModal('modalTambah')">
                <i class="fas fa-plus"></i> Tambah Petani
            </button>
            <a href="{{ route('admin.petani.export', ['format' => 'pdf']) }}" class="btn btn-secondary">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('admin.petani.export', ['format' => 'excel']) }}" class="btn btn-secondary">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a href="{{ route('admin.petani.export', ['format' => 'csv']) }}" class="btn btn-secondary">
                <i class="fas fa-file-csv"></i> CSV
            </a>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table" id="tablePetani">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Petani</th>
                    <th>Kontak</th>
                    <th>Luas Lahan</th>
                    <th>Komoditas</th>
                    <th>Alamat</th>
                    <th>Status</th>
                    
                    <th>Aksi</th>
                    
                </tr>
            </thead>
            <tbody>
                
                    
                
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if(isset($petani) && $petani->hasPages())
        <div style="padding:16px;display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:10px;border-top:1px solid var(--border,#e2e8f0);">
            <div style="width:100%;text-align:center;font-size:12px;color:var(--text-muted);">
                Menampilkan {{ $petani->firstItem() }}–{{ $petani->lastItem() }} dari {{ $petani->total() }} petani
            </div>
            <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:4px;">
                @if($petani->onFirstPage())
                    <span style="padding:6px 10px;min-width:66px;text-align:center;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:var(--surface-3);color:var(--text-muted);font-size:12px;cursor:not-allowed;">First</span>
                    <span style="padding:6px 10px;min-width:66px;text-align:center;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:var(--surface-3);color:var(--text-muted);font-size:12px;cursor:not-allowed;"><i class="fas fa-chevron-left"></i></span>
                @else
                    <a href="{{ $petani->url(1) }}" style="padding:6px 10px;min-width:66px;text-align:center;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:var(--surface-3);color:var(--text-primary);font-size:12px;text-decoration:none;">First</a>
                    <a href="{{ $petani->previousPageUrl() }}" style="padding:6px 10px;min-width:66px;text-align:center;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:var(--surface-3);color:var(--text-primary);font-size:12px;text-decoration:none;"><i class="fas fa-chevron-left"></i></a>
                @endif
                @foreach($petani->getUrlRange(max(1,$petani->currentPage()-2), min($petani->lastPage(),$petani->currentPage()+2)) as $page => $url)
                    <a href="{{ $url }}" style="padding:6px 10px;border-radius:6px;font-size:12px;text-decoration:none;background:{{ $page == $petani->currentPage() ? 'var(--green-600)' : 'var(--surface-3)' }};color:{{ $page == $petani->currentPage() ? 'white' : 'var(--text-primary)' }};">{{ $page }}</a>
                @endforeach
                @if($petani->hasMorePages())
                    <a href="{{ $petani->nextPageUrl() }}" style="padding:6px 10px;min-width:66px;text-align:center;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:var(--surface-3);color:var(--text-primary);font-size:12px;text-decoration:none;"><i class="fas fa-chevron-right"></i></a>
                    <a href="{{ $petani->url($petani->lastPage()) }}" style="padding:6px 10px;min-width:66px;text-align:center;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:var(--surface-3);color:var(--text-primary);font-size:12px;text-decoration:none;">Last</a>
                @else
                    <span style="padding:6px 10px;min-width:66px;text-align:center;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:var(--surface-3);color:var(--text-muted);font-size:12px;cursor:not-allowed;"><i class="fas fa-chevron-right"></i></span>
                    <span style="padding:6px 10px;min-width:66px;text-align:center;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:var(--surface-3);color:var(--text-muted);font-size:12px;cursor:not-allowed;">Last</span>
                @endif
            </div>
        </div>
    @endif
</div>



<!-- Modal Konfirmasi Hapus -->
<div class="modal-overlay" id="modalHapus">
    <div class="modal" style="max-width: 400px; text-align: center;">
        <div style="font-size: 48px; color: var(--red-500); margin-bottom: 15px; margin-top: 15px;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 style="margin-bottom: 10px;">Konfirmasi Hapus</h3>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Apakah Anda yakin ingin menghapus data petani <strong id="hapusNamaPetani"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
        
        <form method="POST" id="formHapusPetani">
            @csrf @method('DELETE')
            <div style="display: flex; gap: 10px; justify-content: center; margin-bottom: 15px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalHapus')">Batal</button>
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const k = document.getElementById('filterKomoditas').value.toLowerCase();
    document.querySelectorAll('#tablePetani tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        const matchQ = text.includes(q);
        const matchK = k === '' || text.includes(k);
        row.style.display = (matchQ && matchK) ? '' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formTambahPetani');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    closeModal('modalTambah');
                    form.reset();
                    // Reload page setelah 500ms untuk memastikan data tersimpan
                    setTimeout(() => location.reload(), 500);
                } else {
                    alert('Gagal menyimpan data. Silakan coba lagi.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
});

function togglePetaniStatus(id) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    fetch(`{{ url('admin/petani') }}/${id}/toggle-status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': token || '',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (!data || !data.success) {
            alert(data?.message || 'Gagal memperbarui status');
            return;
        }
        const cell = document.getElementById(`status-cell-${id}`);
        if (cell) {
            if (data.status === 'aktif') {
                cell.innerHTML = '<span class="badge badge-green">Aktif</span>';
            } else {
                cell.innerHTML = '<span class="badge badge-red">Non-aktif</span>';
            }
        }
    })
    .catch(err => {
        alert('Gagal menghubungi server');
        console.error(err);
    });
}

function confirmDelete(id, nama) {
    document.getElementById('hapusNamaPetani').innerText = nama;
    const form = document.getElementById('formHapusPetani');
    form.action = `{{ url('admin/petani') }}/${id}`;
    openModal('modalHapus');
}
</script>
@endpush
        