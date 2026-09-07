<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| Intro & About
|--------------------------------------------------------------------------
*/

Route::get('/intro', function () {
    return view('auth.intro');
})->name('intro');

Route::get('/about', function () {
    return view('auth.about');
})->name('about');


/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {

    /*
    |--------------------------------------------------------------------------
    | Login Dummy
    |--------------------------------------------------------------------------
    | Untuk sementara semua login diarahkan ke dashboard admin.
    | Jika sistem login sudah menggunakan LoginController,
    | bagian ini nantinya dapat diganti dengan controller tersebut.
    |--------------------------------------------------------------------------
    */

    return redirect()->route('admin.dashboard');
})->name('login.process');


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', function () {
    return redirect()->route('login');
})->name('logout');


/*
|--------------------------------------------------------------------------
| Dashboard Admin
|--------------------------------------------------------------------------
*/

Route::get('/admin', function () {

    /*
    |--------------------------------------------------------------------------
    | DATA DEFAULT DASHBOARD
    |--------------------------------------------------------------------------
    | Data ini dibuat agar dashboard tidak mengalami error
    | "Undefined variable" ketika controller/database belum digunakan.
    |--------------------------------------------------------------------------
    */

    $stokBeras = 450;
    $stokGabah = 800;

    $kapasitasBeras = 1000;
    $kapasitasGabah = 2000;

    $targetBulan = 9000;

    $alertOpenCount = 0;

    $hargaBeliGabah = 6500;
    $hargaJualBeras = 12000;

    $totalBerasKeluar = 0;

    /*
    |--------------------------------------------------------------------------
    | Alert Aktif
    |--------------------------------------------------------------------------
    */

    $alertAktif = collect();


    /*
    |--------------------------------------------------------------------------
    | Distribusi Terkini
    |--------------------------------------------------------------------------
    */

    $distribusiTerkini = collect();


    /*
    |--------------------------------------------------------------------------
    | Data Chart
    |--------------------------------------------------------------------------
    */

    $chartLabels = [
        'Apr',
        'Mei',
        'Jun',
        'Jul',
        'Agu',
        'Sep',
    ];

    $stokBerasHistory = [
        400,
        425,
        450,
        430,
        460,
        $stokBeras,
    ];

    $targetChart = array_fill(
        0,
        count($chartLabels),
        $targetBulan
    );

    $trenPanenGabah = [
        650,
        720,
        680,
        800,
        750,
        $stokGabah,
    ];


    /*
    |--------------------------------------------------------------------------
    | Tampilkan Dashboard
    |--------------------------------------------------------------------------
    */

    return view('admin.dashboard', compact(
        'stokBeras',
        'stokGabah',
        'kapasitasBeras',
        'kapasitasGabah',
        'targetBulan',
        'alertOpenCount',
        'hargaBeliGabah',
        'hargaJualBeras',
        'totalBerasKeluar',
        'alertAktif',
        'distribusiTerkini',
        'chartLabels',
        'stokBerasHistory',
        'targetChart',
        'trenPanenGabah'
    ));

})->name('admin.dashboard');


/*
|--------------------------------------------------------------------------
| Dashboard Petani
|--------------------------------------------------------------------------
*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

Route::get('/admin/petani/export/{format}', function ($format) {

    // CSV
    if ($format === 'csv') {
        $filename = 'data-petani.csv';

        $callback = function () use ($petani) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'No',
                'Nama',
                'NIK',
                'Luas Lahan'
            ]);

            foreach ($petani as $data) {
                fputcsv($file, $data);
            }

            fclose($file);
        };

        return Response::streamDownload(
            $callback,
            $filename,
            ['Content-Type' => 'text/csv']
        );
    }

    // Excel
    if ($format === 'excel') {
        $html = '<table border="1">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Luas Lahan</th>
            </tr>';

        foreach ($petani as $data) {
            $html .= '<tr>
                <td>' . $data['No'] . '</td>
                <td>' . $data['Nama'] . '</td>
                <td>' . $data['NIK'] . '</td>
                <td>' . $data['Luas Lahan'] . '</td>
            </tr>';
        }

        $html .= '</table>';

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="data-petani.xls"');
    }

    // PDF
    if ($format === 'pdf') {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Data Petani</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                }

                h2 {
                    text-align: center;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }

                th, td {
                    border: 1px solid #000;
                    padding: 8px;
                }

                th {
                    background: #eee;
                }
            </style>
        </head>
        <body>

            <h2>DATA PETANI SIPADI</h2>

            <table>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>Luas Lahan</th>
                </tr>';

        foreach ($petani as $data) {
            $html .= '
                <tr>
                    <td>' . $data['No'] . '</td>
                    <td>' . $data['Nama'] . '</td>
                    <td>' . $data['NIK'] . '</td>
                    <td>' . $data['Luas Lahan'] . '</td>
                </tr>';
        }

        $html .= '
            </table>

        </body>
        </html>';

        return response($html)
            ->header('Content-Type', 'text/html');
    }

    abort(404);
})->name('admin.petani.export');


/*
|--------------------------------------------------------------------------
| Dashboard Petugas
|--------------------------------------------------------------------------
*/

Route::get('/petugas', function () {
    return view('petugas.dashboard');
})->name('petugas.dashboard');


/*
|--------------------------------------------------------------------------
| Menu Admin
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Admin - Petani
|--------------------------------------------------------------------------
*/

Route::get('/admin/petani', function () {
    return view('admin.petani.index');
})->name('admin.petani.index');


/*
|--------------------------------------------------------------------------
| Admin - Panen
|--------------------------------------------------------------------------
*/

Route::get('/admin/panen', function () {

    // =========================
    // DATA PETANI DUMMY
    // =========================

    $petani1 = (object) [
        'id' => 1,
        'nama' => 'Balabala',
        'luas_lahan' => 1200,
    ];

    $petani2 = (object) [
        'id' => 2,
        'nama' => 'Putra Raka',
        'luas_lahan' => 1500,
    ];

    $petani3 = (object) [
        'id' => 3,
        'nama' => 'Zakki Khairul',
        'luas_lahan' => 900,
    ];

    $petani4 = (object) [
        'id' => 4,
        'nama' => 'Satria Panca',
        'luas_lahan' => 2000,
    ];

    // =========================
    // DATA LAHAN DUMMY
    // =========================

    $lahan1 = (object) [
        'id' => 1,
        'petani' => $petani1,
    ];

    $lahan2 = (object) [
        'id' => 2,
        'petani' => $petani2,
    ];

    $lahan3 = (object) [
        'id' => 3,
        'petani' => $petani3,
    ];

    $lahan4 = (object) [
        'id' => 4,
        'petani' => $petani4,
    ];

    // =========================
    // DATA PANEN DUMMY
    // =========================

    $panenData = collect([

        (object) [
            'id' => 1,
            'lahan' => $lahan1,
            'jumlah_gabah' => 850,
            'harga_gabah_per_kg' => 6500,
            'foto_bukti' => null,
            'musim' => 'Hujan',
            'tanggal_panen' => now()->subDays(5),
        ],

        (object) [
            'id' => 2,
            'lahan' => $lahan2,
            'jumlah_gabah' => 1200,
            'harga_gabah_per_kg' => 6500,
            'foto_bukti' => null,
            'musim' => 'Hujan',
            'tanggal_panen' => now()->subDays(10),
        ],

        (object) [
            'id' => 3,
            'lahan' => $lahan3,
            'jumlah_gabah' => 650,
            'harga_gabah_per_kg' => 6500,
            'foto_bukti' => null,
            'musim' => 'Hujan',
            'tanggal_panen' => now()->subDays(15),
        ],

        (object) [
            'id' => 4,
            'lahan' => $lahan4,
            'jumlah_gabah' => 1500,
            'harga_gabah_per_kg' => 6500,
            'foto_bukti' => null,
            'musim' => 'Hujan',
            'tanggal_panen' => now()->subDays(20),
        ],

        (object) [
            'id' => 5,
            'lahan' => $lahan1,
            'jumlah_gabah' => 950,
            'harga_gabah_per_kg' => 6500,
            'foto_bukti' => null,
            'musim' => 'Hujan',
            'tanggal_panen' => now()->subDays(25),
        ],

        (object) [
            'id' => 6,
            'lahan' => $lahan2,
            'jumlah_gabah' => 1100,
            'harga_gabah_per_kg' => 6500,
            'foto_bukti' => null,
            'musim' => 'Hujan',
            'tanggal_panen' => now()->subDays(30),
        ],

    ]);

    // =========================
    // PAGINATION DUMMY
    // =========================

    $perPage = 5;

    $currentPage = LengthAwarePaginator::resolveCurrentPage();

    $currentItems = $panenData
        ->slice(($currentPage - 1) * $perPage, $perPage)
        ->values();

    $panenList = new LengthAwarePaginator(
        $currentItems,
        $panenData->count(),
        $perPage,
        $currentPage,
        [
            'path' => request()->url(),
            'query' => request()->query(),
        ]
    );

    return view('admin.panen.index', [
        'petanis' => collect([
            $petani1,
            $petani2,
            $petani3,
            $petani4,
        ]),
        'panenList' => $panenList,
    ]);

})->name('admin.panen.index');

/*
|--------------------------------------------------------------------------
| DETAIL PANEN
|--------------------------------------------------------------------------
*/

Route::get('/admin/panen/{id}', function ($id) {

    return redirect()
        ->route('admin.panen.index');

})->name('admin.panen.show');


/*
|--------------------------------------------------------------------------
| EDIT PANEN
|--------------------------------------------------------------------------
*/

Route::get('/admin/panen/{id}/edit', function ($id) {

    return redirect()
        ->route('admin.panen.index')
        ->with('success', 'Halaman edit panen siap digunakan.');

})->name('admin.panen.edit');


/*
|--------------------------------------------------------------------------
| HAPUS PANEN
|--------------------------------------------------------------------------
*/

Route::delete('/admin/panen/{id}', function ($id) {

    return redirect()
        ->route('admin.panen.index')
        ->with('success', 'Data panen berhasil dihapus.');

})->name('admin.panen.destroy');

// Simpan Panen
Route::post('/admin/panen', function (\Illuminate\Http\Request $request) {

    return redirect()
        ->route('admin.panen.index')
        ->with('success', 'Data panen berhasil disimpan.');

})->name('admin.panen.store');


/*
|--------------------------------------------------------------------------
| Admin - Stok
|--------------------------------------------------------------------------
*/

Route::get('/admin/stok', function () {
    return view('admin.stok.index');
})->name('admin.stok.index');


/*
|--------------------------------------------------------------------------
| Admin - Harga
|--------------------------------------------------------------------------
*/

// =========================
// HARGA
// =========================

Route::get('/admin/harga', function () {

    $konfigurasi = collect([
        (object) [
            'id' => 1,
            'berlaku_mulai' => \Carbon\Carbon::now()->subMonths(5),
            'harga_beli_gabah' => 6500,
            'harga_jual_beras' => 12000,
            'is_active' => false,
        ],

        (object) [
            'id' => 2,
            'berlaku_mulai' => \Carbon\Carbon::now()->subMonths(3),
            'harga_beli_gabah' => 6800,
            'harga_jual_beras' => 12500,
            'is_active' => false,
        ],

        (object) [
            'id' => 3,
            'berlaku_mulai' => \Carbon\Carbon::now()->subMonth(),
            'harga_beli_gabah' => 7000,
            'harga_jual_beras' => 13000,
            'is_active' => true,
        ],

        (object) [
            'id' => 4,
            'berlaku_mulai' => \Carbon\Carbon::now()->addMonth(),
            'harga_beli_gabah' => 7200,
            'harga_jual_beras' => 13500,
            'is_active' => false,
        ],
    ]);

    return view('admin.harga.index', compact('konfigurasi'));

})->name('admin.harga.index');


// Tambah konfigurasi
Route::get('/admin/harga/create', function () {
    return redirect()
        ->route('admin.harga.index')
        ->with('success', 'Form tambah konfigurasi harga siap digunakan.');
})->name('admin.harga.create');


// Aktifkan konfigurasi
Route::patch('/admin/harga/{id}/activate', function ($id) {
    return redirect()
        ->route('admin.harga.index')
        ->with('success', 'Konfigurasi harga berhasil diaktifkan.');
})->name('admin.harga.activate');


// Edit konfigurasi
Route::get('/admin/harga/{id}/edit', function ($id) {
    return redirect()
        ->route('admin.harga.index')
        ->with('success', 'Halaman edit konfigurasi harga siap digunakan.');
})->name('admin.harga.edit');


// Hapus konfigurasi
Route::delete('/admin/harga/{id}', function ($id) {
    return redirect()
        ->route('admin.harga.index')
        ->with('success', 'Konfigurasi harga berhasil dihapus.');
})->name('admin.harga.destroy');


/*
|--------------------------------------------------------------------------
| Admin - Alert
|--------------------------------------------------------------------------
*/

Route::get('/admin/alert', function () {
    return view('admin.alert.index');
})->name('admin.alert.index');


/*
|--------------------------------------------------------------------------
| Admin - Laporan
|--------------------------------------------------------------------------
*/

Route::get('/admin/laporan', function () {
    return view('admin.laporan.index');
})->name('admin.laporan.index');


/*
|--------------------------------------------------------------------------
| Admin - Pengguna
|--------------------------------------------------------------------------
*/

Route::get('/admin/pengguna/create', function () {
    return view('admin.pengguna.create');
})->name('admin.pengguna.create');

Route::get('/admin/pengguna', function () {

    $users = collect([
        (object) [
            'id' => 1,
            'name' => 'balabala 1',
            'email' => 'balabala_1@sipadi.test',
            'role' => 'admin',
            'created_at' => now()->subMonths(5),
        ],

        (object) [
            'id' => 2,
            'name' => 'Balabala 2',
            'email' => 'balabala_2@sipadi.test',
            'role' => 'petugas',
            'created_at' => now()->subMonths(4),
        ],

        (object) [
            'id' => 3,
            'name' => 'Satria Panca',
            'email' => 'satria@sipadi.test',
            'role' => 'petugas',
            'created_at' => now()->subMonths(3),
        ],

        (object) [
            'id' => 4,
            'name' => 'Zakki Khairul',
            'email' => 'zakki@sipadi.test',
            'role' => 'petani',
            'created_at' => now()->subMonths(2),
        ],

        (object) [
            'id' => 5,
            'name' => 'Putra Raka',
            'email' => 'putra@sipadi.test',
            'role' => 'petani',
            'created_at' => now()->subMonth(),
        ],
    ]);

    return view('admin.pengguna.index', compact('users'));

})->name('admin.pengguna.index');


/*
|--------------------------------------------------------------------------
| Admin - Pengaturan
|--------------------------------------------------------------------------
*/

Route::get('/admin/pengaturan', function () {
    return view('admin.pengaturan.index');
})->name('admin.pengaturan.index');

Route::put('/admin/pengaturan', function (\Illuminate\Http\Request $request) {

    $validated = $request->validate([
        'kapasitas_max_beras' => 'required|numeric|min:1',
        'kapasitas_max_gabah' => 'required|numeric|min:1',
        'target_pasar'        => 'required|numeric|min:0',
        'batas_min_beras'     => 'required|numeric|min:0',
        'batas_min_gabah'     => 'required|numeric|min:0',
    ]);

    return redirect()
        ->route('admin.pengaturan.index')
        ->with('success', 'Pengaturan berhasil disimpan.');

})->name('admin.pengaturan.update');


/*
|--------------------------------------------------------------------------
| Admin - Tujuan Distribusi
|--------------------------------------------------------------------------
*/

Route::get('/admin/tujuan-distribusi', function () {
    return view('admin.tujuan-distribusi.index');
})->name('admin.tujuan-distribusi.index');


/*
|--------------------------------------------------------------------------
| API / AJAX - Ringkasan Stok
|--------------------------------------------------------------------------
|
| Route ini digunakan oleh dashboard:
|
| fetch('{{ route('admin.stok.summary') }}')
|
*/
// ===============================
// DETAIL STOK
// ===============================

Route::get('/admin/stok', function () {

    $currentYear = now()->year;
    $currentMonth = now()->month;

    $masukBerasBulanIni = 450;
    $masukGabahBulanIni = 800;
    $stokBeras = 450;
    $stokGabah = 800;

    // Dummy data transaksi
    $transaksiData = collect([
        (object) [
            'id' => 1,
            'tanggal' => now()->subDays(2),
            'tanggal_update' => now()->subDays(2),
            'jenis_transaksi' => 'masuk',
            'komoditas' => 'Gabah',
            'jumlah' => 800,
            'keterangan' => 'Hasil panen petani',
            'catatan' => 'Panen musim pertama',
            'saldo_setelah' => 800,
            'status' => 'aktif',
            'foto_bukti' => null,
            'user' => (object) ['name' => 'Administrator'],
        ],

        (object) [
            'id' => 2,
            'tanggal' => now()->subDays(4),
            'tanggal_update' => now()->subDays(4),
            'jenis_transaksi' => 'masuk',
            'komoditas' => 'Beras',
            'jumlah' => 450,
            'keterangan' => 'Penerimaan stok beras',
            'catatan' => 'Stok awal',
            'saldo_setelah' => 1250,
            'status' => 'aktif',
            'foto_bukti' => null,
            'user' => (object) ['name' => 'Budi Santoso'],
        ],

        (object) [
            'id' => 3,
            'tanggal' => now()->subDays(6),
            'tanggal_update' => now()->subDays(6),
            'jenis_transaksi' => 'keluar',
            'komoditas' => 'Beras',
            'jumlah' => 200,
            'keterangan' => 'Distribusi ke pasar',
            'catatan' => 'Distribusi rutin',
            'saldo_setelah' => 1050,
            'status' => 'aktif',
            'foto_bukti' => null,
            'user' => (object) ['name' => 'Siti Aminah'],
        ],

        (object) [
            'id' => 4,
            'tanggal' => now()->subDays(8),
            'tanggal_update' => now()->subDays(8),
            'jenis_transaksi' => 'masuk',
            'komoditas' => 'Gabah',
            'jumlah' => 650,
            'keterangan' => 'Pembelian gabah petani',
            'catatan' => null,
            'saldo_setelah' => 1500,
            'status' => 'aktif',
            'foto_bukti' => null,
            'user' => (object) ['name' => 'Administrator'],
        ],

        (object) [
            'id' => 5,
            'tanggal' => now()->subDays(10),
            'tanggal_update' => now()->subDays(10),
            'jenis_transaksi' => 'keluar',
            'komoditas' => 'Beras',
            'jumlah' => 150,
            'keterangan' => 'Distribusi pelanggan',
            'catatan' => 'Pesanan pelanggan',
            'saldo_setelah' => 900,
            'status' => 'aktif',
            'foto_bukti' => null,
            'user' => (object) ['name' => 'Budi Santoso'],
        ],
    ]);

    // Pagination dummy
    $perPage = 5;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();

    $currentItems = $transaksiData
        ->slice(($currentPage - 1) * $perPage, $perPage)
        ->values();

    $transaksis = new LengthAwarePaginator(
        $currentItems,
        $transaksiData->count(),
        $perPage,
        $currentPage,
        [
            'path' => request()->url(),
            'query' => request()->query(),
        ]
    );

    return view('admin.stok.index', compact(
        'currentYear',
        'currentMonth',
        'masukBerasBulanIni',
        'masukGabahBulanIni',
        'stokBeras',
        'stokGabah',
        'transaksis'
    ));

})->name('admin.stok.index');


// ===============================
// SIMPAN TRANSAKSI STOK
// ===============================

Route::post('/admin/stok', function (\Illuminate\Http\Request $request) {

    return redirect()
        ->route('admin.stok.index')
        ->with('success', 'Transaksi stok berhasil disimpan.');

})->name('admin.stok.store');


// ===============================
// EDIT STOK
// ===============================

Route::get('/admin/stok/{id}/edit', function ($id) {

    return redirect()
        ->route('admin.stok.index')
        ->with('success', 'Halaman edit transaksi stok #' . $id);

})->name('admin.stok.edit');


// ===============================
// DETAIL STOK
// ===============================

Route::get('/admin/stok/{id}', function ($id) {

    return redirect()
        ->route('admin.stok.index')
        ->with('success', 'Detail transaksi stok #' . $id);

})->name('admin.stok.show');


// ===============================
// TOGGLE STATUS
// ===============================

Route::patch('/admin/stok/{id}/toggle', function ($id) {

    return response()->json([
        'success' => true,
        'message' => 'Status transaksi berhasil diperbarui.',
        'id' => $id,
    ]);

})->name('admin.stok.toggle');


// ===============================
// HAPUS STOK
// ===============================

Route::delete('/admin/stok/{id}', function ($id) {

    return response()->json([
        'success' => true,
        'message' => 'Transaksi stok berhasil dihapus.',
        'id' => $id,
    ]);

})->name('admin.stok.destroy');


// ===============================
// TUJUAN DISTRIBUSI
// ===============================

Route::get('/admin/tujuan-distribusi', function (\Illuminate\Http\Request $request) {

    // ==========================================
    // DUMMY DATA TUJUAN DISTRIBUSI
    // ==========================================

    $tujuanData = collect([
        (object) [
            'id' => 1,
            'nama' => 'Pasar Johar Karawang',
            'total_terkirim' => 1250,
            'created_at' => now()->subMonths(5),
        ],

        (object) [
            'id' => 2,
            'nama' => 'Pasar Baru Karawang',
            'total_terkirim' => 980,
            'created_at' => now()->subMonths(4),
        ],

        (object) [
            'id' => 3,
            'nama' => 'Toko Beras Makmur',
            'total_terkirim' => 750,
            'created_at' => now()->subMonths(3),
        ],

        (object) [
            'id' => 4,
            'nama' => 'Koperasi UNSIKA',
            'total_terkirim' => 520,
            'created_at' => now()->subMonths(2),
        ],

        (object) [
            'id' => 5,
            'nama' => 'Pasar Cilamaya',
            'total_terkirim' => 430,
            'created_at' => now()->subMonth(),
        ],

        (object) [
            'id' => 6,
            'nama' => 'Pasar Rengasdengklok',
            'total_terkirim' => 380,
            'created_at' => now()->subDays(20),
        ],

        (object) [
            'id' => 7,
            'nama' => 'Toko Sembako Sejahtera',
            'total_terkirim' => 275,
            'created_at' => now()->subDays(15),
        ],
    ]);


    // ==========================================
    // SEARCH
    // ==========================================

    $search = $request->input('search');

    if ($search) {

        $tujuanData = $tujuanData
            ->filter(function ($tujuan) use ($search) {

                return stripos(
                    $tujuan->nama,
                    $search
                ) !== false;

            })
            ->values();
    }


    // ==========================================
    // TOTAL DATA
    // ==========================================

    $total = $tujuanData->count();

    $totalTujuan = $total;


    // ==========================================
    // TUJUAN DENGAN DISTRIBUSI TERBANYAK
    // ==========================================

    $tujuanTerbanyak = $tujuanData
        ->sortByDesc('total_terkirim')
        ->first()
        ->nama ?? '-';


    // ==========================================
    // TOTAL YANG DIKIRIM
    // ==========================================

    $totalDikirimBulanIni = $tujuanData
        ->sum('total_terkirim');


    // ==========================================
    // PAGINATION
    // ==========================================

    $perPage = 10;

    $currentPage =
        \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();

    $currentItems = $tujuanData
        ->slice(
            ($currentPage - 1) * $perPage,
            $perPage
        )
        ->values();

    $tujuans = new \Illuminate\Pagination\LengthAwarePaginator(
        $currentItems,
        $tujuanData->count(),
        $perPage,
        $currentPage,
        [
            'path' => request()->url(),
            'query' => request()->query(),
        ]
    );


    // ==========================================
    // HALAMAN TERAKHIR
    // ==========================================

    $lastPage = $tujuans->lastPage();


    // ==========================================
    // KIRIM SEMUA DATA KE BLADE
    // ==========================================

    return view(
        'admin.tujuan-distribusi.index',
        compact(
            'tujuans',
            'total',
            'totalTujuan',
            'tujuanTerbanyak',
            'totalDikirimBulanIni',
            'search',
            'currentPage',
            'lastPage'
        )
    );

    // ===============================
    // SEARCH
    // ===============================

    $search = $request->input('search');

    if ($search) {
        $tujuanData = $tujuanData
            ->filter(function ($tujuan) use ($search) {
                return stripos($tujuan->nama, $search) !== false;
            })
            ->values();
    }


    // ===============================
    // PAGINATION DUMMY
    // ===============================

    $perPage = 10;

    $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();

    $currentItems = $tujuanData
        ->slice(($currentPage - 1) * $perPage, $perPage)
        ->values();

    $tujuans = new \Illuminate\Pagination\LengthAwarePaginator(
        $currentItems,
        $tujuanData->count(),
        $perPage,
        $currentPage,
        [
            'path' => request()->url(),
            'query' => request()->query(),
        ]
    );

    $lastPage = $tujuans->lastPage();

    return view('admin.tujuan-distribusi.index', compact(
        'tujuans',
        'total',
        'totalTujuan',
        'tujuanTerbanyak',
        'totalDikirimBulanIni',
        'search',
        'currentPage',
        'lastPage'
    ));


    // ===============================
    // STATISTIK
    // ===============================

    $total = $tujuanData->count();

    $totalTujuan = $tujuanData->count();

    $tujuanTerbanyak = $tujuanData
        ->sortByDesc('total_terkirim')
        ->first()
        ->nama ?? '-';

    $totalDikirimBulanIni = $tujuanData->sum('total_terkirim');


    // ===============================
    // KIRIM KE BLADE
    // ===============================

    return view('admin.tujuan-distribusi.index', compact(
        'tujuans',
        'total',
        'totalTujuan',
        'tujuanTerbanyak',
        'totalDikirimBulanIni',
        'search',
        'currentPage'
    ));

})->name('admin.tujuan-distribusi.index');

Route::delete('/admin/tujuan-distribusi/{id}', function ($id) {

    return response()->json([
        'success' => true,
        'message' => 'Tujuan distribusi berhasil dihapus.',
        'id' => $id,
    ]);

})->name('admin.tujuan-distribusi.destroy');


// TAMBAH TUJUAN DISTRIBUSI
Route::post('/admin/tujuan-distribusi', function (\Illuminate\Http\Request $request) {

    $nama = $request->input('nama');

    return response()->json([
        'success' => true,
        'message' => 'Tujuan distribusi berhasil ditambahkan.',
        'data' => [
            'id' => rand(100, 999),
            'nama' => $nama,
        ],
    ]);

})->name('admin.tujuan-distribusi.store');


/*
|--------------------------------------------------------------------------
| Admin - Tangani Alert
|--------------------------------------------------------------------------
|
| Digunakan oleh tombol:
|
| "Tandai Ditangani"
|
*/

Route::patch('/admin/alert/{id}/tangani', function ($id) {

    /*
    |--------------------------------------------------------------------------
    | Sementara
    |--------------------------------------------------------------------------
    | Route sudah disediakan agar tidak terjadi RouteNotFoundException.
    |
    | Jika model Alert sudah tersedia, bagian ini bisa diganti dengan:
    |
    | $alert = Alert::findOrFail($id);
    | $alert->update([...]);
    |
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'success' => true,
        'message' => 'Alert berhasil ditandai sebagai ditangani.',
        'id' => $id,
    ]);

})->name('admin.alert.tangani');