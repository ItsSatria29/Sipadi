```blade
{{-- DASHBOARD PAGE --}}

@extends('layout.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan stok, tren panen, dan monitoring real-time')

@section('content')

{{-- ============================================================
     ALERT AKTIF
============================================================ --}}

@if(isset($alertAktif) && $alertAktif->count() > 0)

    <div class="alert-banner danger"
         style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:flex-start;">

        <div style="display:flex;gap:12px;">

            <i class="fas fa-exclamation-triangle"
               style="margin-top:2px;"></i>

            <div>

                <strong>
                    {{ $alertAktif->count() }} Alert Stok Aktif!
                </strong>

                <br>

                <span style="font-size:12.5px;">

                    Stok menipis:

                    @foreach($alertAktif->take(3) as $a)

                        <strong>{{ $a->komoditas ?? 'Komoditas' }}</strong>
                        ({{ number_format($a->stok_saat_ini ?? 0) }} kg)

                        @if(!$loop->last)
                            ,
                        @endif

                    @endforeach

                    @if($alertAktif->count() > 3)

                        dan {{ $alertAktif->count() - 3 }} lainnya

                    @endif

                    —

                    <a href="{{ route('admin.alert.index') }}"
                       style="color:inherit;text-decoration:underline;">
                        Lihat semua alert
                    </a>

                </span>

            </div>

        </div>


        <button
            class="btn btn-sm"
            style="background:#fff;color:#991b1b;border:1px solid #fecaca;border-radius:6px;padding:6px 12px;cursor:pointer;white-space:nowrap;margin-top:2px;"
            onclick="markFirstAlertHandled()">

            <i class="fas fa-check"></i>

            Tandai Ditangani

        </button>

    </div>

@endif


{{-- ============================================================
     STAT CARDS
============================================================ --}}

@php

    $stokBerasValue = $stokBeras ?? 450;
    $stokGabahValue = $stokGabah ?? 800;

    $kapasitasBerasValue = $kapasitasBeras ?? 1000;
    $kapasitasGabahValue = $kapasitasGabah ?? 2000;

    $targetBulanValue = $targetBulan ?? 9000;

    $alertCount = $alertOpenCount ?? 0;

    $hargaBeliValue = $hargaBeliGabah ?? 6500;
    $hargaJualValue = $hargaJualBeras ?? 12000;

    $totalBerasKeluarValue = $totalBerasKeluar ?? 0;

    $persenBeras = $kapasitasBerasValue > 0
        ? min(100, round(($stokBerasValue / $kapasitasBerasValue) * 100))
        : 0;

    $persenGabah = $kapasitasGabahValue > 0
        ? min(100, round(($stokGabahValue / $kapasitasGabahValue) * 100))
        : 0;

@endphp


<div class="stat-grid">

    {{-- STOK BERAS --}}

    <div class="stat-card green animate-in">

        <div class="stat-icon">
            <i class="fas fa-warehouse"></i>
        </div>

        <div id="statStokBeras"
             class="stat-value">

            {{ number_format($stokBerasValue) }}

            <small style="font-size:14px;font-weight:600;color:var(--text-muted);">
                kg
            </small>

        </div>

        <div class="stat-label">
            Stok Beras
        </div>

        <div class="stat-change up">

            <i class="fas fa-arrow-up"></i>

            Kapasitas max
            {{ number_format($kapasitasBerasValue) }} kg
            ({{ $persenBeras }}%)

        </div>

        <div style="margin-top:10px;">

            <div class="progress-bar">

                <div
                    class="progress-fill"
                    style="width:{{ $persenBeras }}%;background:var(--green-500);">
                </div>

            </div>

        </div>

    </div>


    {{-- STOK GABAH --}}

    <div class="stat-card amber animate-in">

        <div class="stat-icon">
            <i class="fas fa-seedling"></i>
        </div>

        <div id="statStokGabah"
             class="stat-value">

            {{ number_format($stokGabahValue) }}

            <small style="font-size:14px;font-weight:600;color:var(--text-muted);">
                kg
            </small>

        </div>

        <div class="stat-label">
            Stok Gabah
        </div>

        <div class="stat-change up">

            <i class="fas fa-arrow-up"></i>

            Kapasitas max
            {{ number_format($kapasitasGabahValue) }} kg
            ({{ $persenGabah }}%)

        </div>

        <div style="margin-top:10px;">

            <div class="progress-bar">

                <div
                    class="progress-fill"
                    style="width:{{ $persenGabah }}%;background:var(--amber-500);">
                </div>

            </div>

        </div>

    </div>


    {{-- TARGET PASAR --}}

    <div class="stat-card blue animate-in">

        <div class="stat-icon">
            <i class="fas fa-chart-line"></i>
        </div>

        <div class="stat-value">

            {{ number_format($targetBulanValue) }}

            <small style="font-size:14px;font-weight:600;color:var(--text-muted);">
                kg
            </small>

        </div>

        <div class="stat-label">
            Target Pasar / Bulan
        </div>

        <div class="stat-change"
             style="color:var(--blue-500);">

            <i class="fas fa-info-circle"></i>

            Target tetap
            {{ number_format($targetBulanValue) }} kg

        </div>

    </div>


    {{-- ALERT --}}

    <div class="stat-card {{ $alertCount > 0 ? 'red' : 'green' }} animate-in">

        <div class="stat-icon">
            <i class="fas fa-bell"></i>
        </div>

        <div class="stat-value">
            {{ $alertCount }}
        </div>

        <div class="stat-label">
            Alert Terbuka
        </div>

        <div class="stat-change {{ $alertCount > 0 ? 'down' : 'up' }}">

            <i class="fas fa-{{ $alertCount > 0 ? 'exclamation-triangle' : 'check-circle' }}"></i>

            {{ $alertCount > 0
                ? 'Perlu penanganan segera'
                : 'Semua stok aman'
            }}

        </div>

    </div>

</div>


{{-- ============================================================
     CHARTS
============================================================ --}}

<div class="grid-2" style="margin-bottom:24px;">

    {{-- STOK VS TARGET --}}

    <div class="card animate-in">

        <div class="card-header">

            <div>

                <div class="card-title">
                    Stok vs Target Pasar
                </div>

                <div class="card-subtitle">
                    Perbandingan stok tersedia vs target
                    {{ number_format($targetBulanValue) }} kg/bulan
                    (6 bulan terakhir)
                </div>

            </div>

        </div>


        <div class="card-body">

            <div class="chart-wrap"
                 style="height:240px;">

                <canvas id="chartStokTarget"></canvas>

            </div>

        </div>

    </div>


    {{-- TREN PANEN --}}

    <div class="card animate-in">

        <div class="card-header">

            <div>

                <div class="card-title">
                    Tren Hasil Panen
                </div>

                <div class="card-subtitle">
                    Gabah masuk per bulan (kg)
                </div>

            </div>

        </div>


        <div class="card-body">

            <div class="chart-wrap"
                 style="height:240px;">

                <canvas id="chartTrenPanen"></canvas>

            </div>

        </div>

    </div>

</div>


{{-- ============================================================
     BOTTOM ROW
============================================================ --}}

<div class="grid-2">


    {{-- ========================================================
         DISTRIBUSI TERKINI
    ========================================================= --}}

    <div class="card">

        <div class="card-header"
             style="flex-wrap:wrap;gap:8px;">

            <div>

                <div class="card-title">
                    Distribusi Terkini
                </div>

                <div class="card-subtitle">

                    Transaksi stok beras keluar —

                    <span id="distribusiCount">
                        {{ count($distribusiTerkini ?? []) }}
                    </span>

                    transaksi

                </div>

            </div>


            <a
                href="{{ route('admin.stok.index') }}"
                class="btn btn-secondary btn-sm">

                Lihat Semua

            </a>

        </div>


        {{-- SEARCH --}}

        <div style="padding:10px 16px;">

            <div style="position:relative;">

                <i
                    class="fas fa-search"
                    style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px;">
                </i>


                <input
                    type="text"
                    id="searchDistribusi"
                    placeholder="Cari tujuan, jumlah..."
                    oninput="filterDistribusi()"
                    style="width:100%;padding:8px 12px 8px 34px;border-radius:8px;border:1.5px solid var(--border);font-size:13px;background:var(--surface-2);color:var(--text-primary);outline:none;box-sizing:border-box;transition:border-color .2s;"
                    onfocus="this.style.borderColor='var(--green-500)'"
                    onblur="this.style.borderColor='var(--border)'"
                >

            </div>

        </div>


        {{-- TABLE --}}

        <div
            class="table-container"
            style="max-height:165px;overflow-y:auto;overflow-x:auto;">

            <table
                class="data-table"
                id="distribusiTable"
                style="width:100%;min-width:400px;">

                <thead
                    style="position:sticky;top:0;z-index:1;background:var(--surface);">

                    <tr>

                        <th>
                            Tujuan
                        </th>

                        <th>
                            Jumlah
                        </th>

                        <th>
                            Tanggal
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse(($distribusiTerkini ?? []) as $dist)

                        <tr>

                            <td>

                                <div style="font-weight:600;">
                                    {{ $dist->tujuan ?? '-' }}
                                </div>

                            </td>


                            <td>

                                <strong style="color:var(--green-600);">

                                    {{ number_format($dist->jumlah ?? 0) }}

                                    kg

                                </strong>

                            </td>


                            <td
                                style="color:var(--text-muted);font-size:12px;white-space:nowrap;">

                                {{ $dist->tanggal ?? '-' }}

                            </td>

                        </tr>

                    @empty

                        <tr id="distribusiEmptyRow">

                            <td
                                colspan="3"
                                style="text-align:center;padding:32px;color:var(--text-muted);">

                                <i
                                    class="fas fa-inbox"
                                    style="font-size:28px;margin-bottom:8px;display:block;">
                                </i>

                                Belum ada data distribusi

                            </td>

                        </tr>

                    @endforelse


                    <tr
                        id="distribusiNoResult"
                        style="display:none;">

                        <td
                            colspan="3"
                            style="text-align:center;padding:24px;color:var(--text-muted);">

                            <i
                                class="fas fa-search"
                                style="margin-right:6px;">
                            </i>

                            Tidak ditemukan hasil untuk pencarian ini

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    {{-- ========================================================
         HARGA & PENDAPATAN
    ========================================================= --}}

    

    <div class="card">

        <div class="card-header">

            <div>

                <div class="card-title">
                    Harga & Estimasi Pendapatan
                </div>

                <div class="card-subtitle">
                    Berdasarkan harga konfigurasi aktif
                </div>

            </div>


            <a
                href="{{ route('admin.harga.index') }}"
                class="btn btn-secondary btn-sm">

                Ubah Harga

            </a>

        </div>


        <div class="card-body">

            <div
                style="display:flex;flex-direction:column;gap:16px;">


                {{-- HARGA --}}

                <div
                    style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">

                    <div
                        style="background:var(--surface-2);border-radius:10px;padding:14px;">

                        <div
                            style="font-size:11.5px;color:var(--text-muted);margin-bottom:6px;font-weight:600;text-transform:uppercase;">

                            Harga Beli Gabah

                        </div>

                        <div
                            style="font-size:17px;font-weight:700;">

                            Rp
                            {{ number_format($hargaBeliValue, 0, ',', '.') }}

                            / kg

                        </div>

                    </div>


                    <div
                        style="background:var(--surface-2);border-radius:10px;padding:14px;">

                        <div
                            style="font-size:11.5px;color:var(--text-muted);margin-bottom:6px;font-weight:600;text-transform:uppercase;">

                            Harga Jual Beras

                        </div>

                        <div
                            style="font-size:17px;font-weight:700;">

                            Rp
                            {{ number_format($hargaJualValue, 0, ',', '.') }}

                            / kg

                        </div>

                    </div>

                </div>


                {{-- ESTIMASI PENDAPATAN --}}

                <div
                    style="background:var(--blue-100);border-radius:10px;padding:14px;display:flex;align-items:center;gap:12px;">

                    <i
                        class="fas fa-chart-bar"
                        style="font-size:22px;color:var(--blue-500);">
                    </i>


                    <div>

                        <div
                            style="font-weight:700;font-size:14px;color:#1e40af;">

                            Estimasi Pendapatan Bulan Ini

                        </div>


                        <div
                            style="font-size:14px;font-weight:700;color:#1e40af;">

                            Rp
                            {{
                                number_format(
                                    $totalBerasKeluarValue * $hargaJualValue,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}

                            <span
                                style="font-size:11px;opacity:0.8;font-weight:normal;">

                                (beras keluar bulan ini × harga jual)

                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection


{{-- ============================================================
     JAVASCRIPT
============================================================ --}}

@push('scripts')

<script>

/*
|--------------------------------------------------------------------------
| DATA CHART
|--------------------------------------------------------------------------
*/

    $chartLabelsData = $chartLabels ?? [
        'Apr',
        'Mei',
        'Jun',
        'Jul',
        'Agu',
        'Sep'
    ];

    $stokHistoryData = $stokBerasHistory ?? [
        400,
        425,
        450,
        430,
        460,
        450
    ];

    $targetChartData = $targetChart ?? [
        9000,
        9000,
        9000,
        9000,
        9000,
        9000
    ];

    $trenPanenDataValue = $trenPanenGabah ?? [
        650,
        720,
        680,
        800,
        750,
        800
    ];



/*
|--------------------------------------------------------------------------
| CHART STOK VS TARGET
|--------------------------------------------------------------------------
*/

const chartStokTargetElement =
    document.getElementById('chartStokTarget');

if (chartStokTargetElement) {

    const ctx1 =
        chartStokTargetElement.getContext('2d');

    new Chart(ctx1, {

        type: 'bar',

        data: {

            labels: chartLabels,

            datasets: [

                {

                    label: 'Stok Beras (kg)',

                    data: stokHistory,

                    backgroundColor: 'rgba(56,161,105,.75)',

                    borderRadius: 6,

                    borderSkipped: false,

                },

                {

                    label: 'Target ({{ number_format($targetBulanValue) }} kg)',

                    data: targetChart,

                    type: 'line',

                    borderColor: '#f59e0b',

                    borderDash: [5, 4],

                    borderWidth: 2,

                    pointRadius: 0,

                    tension: 0,

                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {

                    position: 'bottom',

                    labels: {

                        font: {

                            family: 'Plus Jakarta Sans',

                            size: 11

                        }

                    }

                }

            },

            scales: {

                y: {

                    beginAtZero: true,

                    grid: {

                        color: 'rgba(0,0,0,.05)'

                    },

                    ticks: {

                        font: {

                            family: 'Plus Jakarta Sans',

                            size: 11

                        }

                    }

                },

                x: {

                    grid: {

                        display: false

                    },

                    ticks: {

                        font: {

                            family: 'Plus Jakarta Sans',

                            size: 11

                        }

                    }

                }

            }

        }

    });

}


/*
|--------------------------------------------------------------------------
| CHART TREN PANEN
|--------------------------------------------------------------------------
*/

const chartTrenPanenElement =
    document.getElementById('chartTrenPanen');

if (chartTrenPanenElement) {

    const ctx2 =
        chartTrenPanenElement.getContext('2d');

    new Chart(ctx2, {

        type: 'line',

        data: {

            labels: chartLabels,

            datasets: [

                {

                    label: 'Gabah Masuk (kg)',

                    data: trenPanenData,

                    borderColor: '#f59e0b',

                    backgroundColor: 'rgba(245,158,11,.1)',

                    borderWidth: 2.5,

                    pointBackgroundColor: '#f59e0b',

                    pointRadius: 5,

                    fill: true,

                    tension: .35,

                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {

                    position: 'bottom',

                    labels: {

                        font: {

                            family: 'Plus Jakarta Sans',

                            size: 11

                        }

                    }

                }

            },

            scales: {

                y: {

                    beginAtZero: true,

                    grid: {

                        color: 'rgba(0,0,0,.05)'

                    },

                    ticks: {

                        font: {

                            family: 'Plus Jakarta Sans',

                            size: 11

                        }

                    }

                },

                x: {

                    grid: {

                        display: false

                    },

                    ticks: {

                        font: {

                            family: 'Plus Jakarta Sans',

                            size: 11

                        }

                    }

                }

            }

        }

    });

}


/*
|--------------------------------------------------------------------------
| AUTO REFRESH DASHBOARD
|--------------------------------------------------------------------------
*/

const dashboardRefreshInterval = 15000;

let dashboardLastRefresh = Date.now();


function refreshDashboardIfVisible() {

    if (document.visibilityState === 'visible') {

        const now = Date.now();

        if (
            now - dashboardLastRefresh
            >= dashboardRefreshInterval
        ) {

            dashboardLastRefresh = now;

            window.location.reload();

        }

    }

}


setInterval(
    refreshDashboardIfVisible,
    dashboardRefreshInterval
);


window.addEventListener(
    'focus',
    refreshDashboardIfVisible
);


/*
|--------------------------------------------------------------------------
| FUNCTION UPDATE STOK
|--------------------------------------------------------------------------
*/

function updateDashboardStock() {

    

    .then(response => {

        if (!response.ok) {
            throw new Error(
                'HTTP Error ' + response.status
            );
        }

        return response.json();

    })

    .then(data => {

        /*
        |--------------------------------------------------------------------------
        | STOK BERAS
        |--------------------------------------------------------------------------
        */

        if (data.stokBeras !== undefined) {

            const el =
                document.getElementById(
                    'statStokBeras'
                );

            if (el) {

                el.innerHTML =
                    Number(
                        data.stokBeras
                    ).toLocaleString('id-ID')
                    +
                    ' <small style="font-size:14px;font-weight:600;color:var(--text-muted);">kg</small>';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | STOK GABAH
        |--------------------------------------------------------------------------
        */

        if (data.stokGabah !== undefined) {

            const el2 =
                document.getElementById(
                    'statStokGabah'
                );

            if (el2) {

                el2.innerHTML =
                    Number(
                        data.stokGabah
                    ).toLocaleString('id-ID')
                    +
                    ' <small style="font-size:14px;font-weight:600;color:var(--text-muted);">kg</small>';

            }

        }

    })

    .catch(error => {

        console.error(
            'Gagal memuat ringkasan stok:',
            error
        );

    });

}


/*
|--------------------------------------------------------------------------
| STORAGE EVENT
|--------------------------------------------------------------------------
*/

window.addEventListener(
    'storage',
    function (e) {

        if (e.key === 'stok:updated') {

            console.log(
                'Dashboard menerima update stok'
            );

            updateDashboardStock();

        }

    }
);


/*
|--------------------------------------------------------------------------
| BROADCAST CHANNEL
|--------------------------------------------------------------------------
*/

try {

    const bc =
        new BroadcastChannel('stok_channel');


    bc.onmessage =
        function (ev) {

            console.log(
                'Dashboard menerima BroadcastChannel:',
                ev.data
            );


            if (
                ev.data &&
                ev.data.updated
            ) {

                updateDashboardStock();

            }

        };

}
catch (e) {

    console.log(
        'BroadcastChannel tidak tersedia.'
    );

}


/*
|--------------------------------------------------------------------------
| TANDAI ALERT DITANGANI
|--------------------------------------------------------------------------
*/

function markFirstAlertHandled() {

    // Ambil data alert dari Laravel
    const alerts = @json($alertAktif ?? []);

    // Jika tidak ada alert
    if (!Array.isArray(alerts) || alerts.length === 0) {
        return;
    }

    // Ambil ID alert pertama
    const firstAlertId = alerts[0].id;

    // URL endpoint
    const url = "{{ url('/admin/alert') }}/" + firstAlertId + "/tangani";

    fetch(url, {
        method: 'PATCH',

        headers: {
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content'),

            'Accept': 'application/json',

            'Content-Type': 'application/json'
        }
    })
    .then(function (response) {

        if (!response.ok) {
            throw new Error(
                'HTTP Error ' + response.status
            );
        }

        return response.json();

    })
    .then(function (data) {

        console.log(
            'Alert berhasil ditangani:',
            data
        );

        window.location.reload();

    })
    .catch(function (error) {

        console.error(
            'Gagal menangani alert:',
            error
        );

    });
}


/*
|--------------------------------------------------------------------------
| SEARCH DISTRIBUSI
|--------------------------------------------------------------------------
*/

function filterDistribusi() {

    const input =
        document.getElementById(
            'searchDistribusi'
        );


    if (!input) {
        return;
    }


    const q =
        input.value
            .toLowerCase()
            .trim();


    const rows =
        document.querySelectorAll(
            '#distribusiTable tbody tr:not(#distribusiNoResult):not(#distribusiEmptyRow)'
        );


    let visibleCount = 0;


    rows.forEach(
        function (row) {

            const text =
                row.textContent
                    .toLowerCase();


            const show =
                text.includes(q);


            row.style.display =
                show ? '' : 'none';


            if (show) {

                visibleCount++;

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | NO RESULT
    |--------------------------------------------------------------------------
    */

    const noResult =
        document.getElementById(
            'distribusiNoResult'
        );


    if (noResult) {

        noResult.style.display =
            (
                visibleCount === 0 &&
                q.length > 0
            )
                ? ''
                : 'none';

    }


    /*
    |--------------------------------------------------------------------------
    | COUNTER
    |--------------------------------------------------------------------------
    */

    const counter =
        document.getElementById(
            'distribusiCount'
        );


    if (counter) {

        counter.textContent =
            q.length > 0
                ? visibleCount + ' hasil'
                : rows.length;

    }

}

</script>

@endpush
```
