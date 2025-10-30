@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Tampilan Timer --}}
    <div class="alert alert-primary position-sticky" style="top: 10px; z-index: 1000;">
        <h5 class="text-center mb-0">
            Sisa Waktu: <span id="timer" class="font-weight-bold">--:--:--</span>
        </h5>
    </div>

    <h3 class="mb-4">Ujian: {{ $ujian->nama_ujian }}</h3>

    <form id="formUjian" method="POST" action="{{ route('ikutujian.selesai', $ujian->id) }}">
        @csrf

        {{-- PERBAIKAN KECIL: Mengubah $soal menjadi $soals agar sesuai standar --}}
        @foreach($soal as $key => $soal)
            <div class="mb-4 p-3 border rounded bg-light soal-item soal-wrapper">
                <p><b>{{ $key+1 }}. {{ $soal->soal }}</b></p>
                
                @if($soal->file)
                    {{-- 
                      Kita menggunakan helper Str::startsWith untuk mengecek tipe file.
                      Ini akan menangani 'image/jpeg', 'image/png', dll.
                    --}}
                    @if(Illuminate\Support\Str::startsWith($soal->tipe_file, 'image/'))
                        <div class="my-2">
                            <img src="{{ asset('storage/' . $soal->file) }}" 
                                 alt="Gambar Soal" 
                                 class="img-fluid rounded" 
                                 style="max-width: 450px;">
                        </div>
                    
                    {{-- (Opsional) Jika Anda berencana mendukung file audio untuk listening --}}
                    @elseif(Illuminate\Support\Str::startsWith($soal->tipe_file, 'audio/'))
                        <audio controls class="my-3" style="width: 100%;">
                            <source src="{{ asset('storage/' . $soal->file) }}" type="{{ $soal->tipe_file }}">
                            Browser Anda tidak mendukung elemen audio.
                        </audio>
                    @endif
                @endif

                @foreach(['a','b','c','d','e'] as $opsi)
                    @php $field = 'opsi_'.$opsi; @endphp
                    @if(!empty($soal->$field))
                        <div class="form-check">
                            <input class="form-check-input" type="radio" 
                                   name="jawaban[{{ $soal->id }}]" 
                                   value="{{ $opsi }}" 
                                   id="soal{{ $soal->id }}_{{ $opsi }}">
                            <label class="form-check-label" for="soal{{ $soal->id }}_{{ $opsi }}">
                                {{ strtoupper($opsi) }}. {{ $soal->$field }}
                            </label>
                        </div>
                    @endif
                @endforeach
            </div>
        @endforeach
        
        <div class="d-flex justify-content-center my-4" id="pagination-controls"></div>
        
        <button type="button" class="btn btn-success" id="btnSelesaiUjian" disabled>Selesai Ujian</button>
    </form>
</div>

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- =================================================================== --}}
{{-- SEMUA SCRIPT DIGABUNGKAN MENJADI SATU BLOK YANG LEBIH BAIK --}}
{{-- =================================================================== --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    // --- Deklarasi Variabel ---
    const form = document.getElementById("formUjian");
    const timerDisplay = document.getElementById("timer");
    const soalItems = document.querySelectorAll('.soal-item');
    const paginationControls = document.getElementById('pagination-controls');
    const btnSelesai = document.getElementById("btnSelesaiUjian");

    // --- Logika Timer ---
    const durasiUjianMenit = {{ $ujian->waktu }};
    let waktuSelesai = localStorage.getItem('waktuSelesaiUjian_{{ $ujian->id }}');
    if (!waktuSelesai) {
        waktuSelesai = new Date().getTime() + durasiUjianMenit * 60 * 1000;
        localStorage.setItem('waktuSelesaiUjian_{{ $ujian->id }}', waktuSelesai);
    }
    const timerInterval = setInterval(function() {
        const selisih = waktuSelesai - new Date().getTime();
        if (selisih < 0) {
            clearInterval(timerInterval);
            localStorage.removeItem('waktuSelesaiUjian_{{ $ujian->id }}');
            window.removeEventListener('beforeunload', handleBeforeUnload);
            Swal.fire({
                title: "Waktu Habis!", text: "Jawaban Anda akan otomatis disubmit.", icon: "info",
                allowOutsideClick: false, confirmButtonText: "OK"
            }).then(() => { form.submit(); });
        } else {
            const jam = Math.floor((selisih % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const menit = Math.floor((selisih % (1000 * 60 * 60)) / (1000 * 60));
            const detik = Math.floor((selisih % (1000 * 60)) / 1000);
            timerDisplay.textContent = ('0' + jam).slice(-2) + ":" + ('0' + menit).slice(-2) + ":" + ('0' + detik).slice(-2);
        }
    }, 1000);

    // --- Logika Pencegahan Keluar Halaman ---
    const handleBeforeUnload = (event) => {
        event.preventDefault();
        event.returnValue = 'Anda yakin ingin meninggalkan halaman? Proses ujian masih berlangsung.';
        return event.returnValue;
    };
    window.addEventListener('beforeunload', handleBeforeUnload);

    // ===================================================================
    // KODE BARU: LOGIKA UNTUK MENGAKTIFKAN TOMBOL SETELAH SEMUA SOAL DIJAWAB
    // ===================================================================
    const totalSoal = soalItems.length;
    form.addEventListener('change', function() {
        const totalJawaban = document.querySelectorAll('input[type="radio"]:checked').length;
        btnSelesai.disabled = (totalJawaban !== totalSoal);
    });
    // ===================================================================

    // --- Logika Tombol Selesai (SweetAlert) ---
    btnSelesai.addEventListener("click", function () {
        Swal.fire({
            title: "Konfirmasi", text: "Anda yakin ingin menyelesaikan ujian ini?", icon: "warning",
            showCancelButton: true, confirmButtonText: "Ya, Selesaikan!", cancelButtonText: "Batal", reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.removeEventListener('beforeunload', handleBeforeUnload);
                localStorage.removeItem('waktuSelesaiUjian_{{ $ujian->id }}');
                form.submit();
            }
        });
    });

    // --- Logika Paginasi ---
    const soalPerPage = 5;
    const pageCount = Math.ceil(soalItems.length / soalPerPage);
    let currentPage = 1;

    function showPage(page) {
        soalItems.forEach(item => item.style.display = 'none');
        const startIndex = (page - 1) * soalPerPage;
        const endIndex = startIndex + soalPerPage;
        for (let i = startIndex; i < endIndex && i < soalItems.length; i++) {
            soalItems[i].style.display = 'block';
        }
        updateActiveButton(page);
    }

    function createPaginationButtons() {
        if (pageCount <= 1) return;
        const prevButton = document.createElement('button');
        prevButton.type = 'button';
        prevButton.className = 'btn btn-outline-primary me-2';
        prevButton.innerHTML = '&laquo; Sebelumnya';
        prevButton.addEventListener('click', () => { if (currentPage > 1) { currentPage--; showPage(currentPage); } });
        paginationControls.appendChild(prevButton);

        const nextButton = document.createElement('button');
        nextButton.type = 'button';
        nextButton.className = 'btn btn-outline-primary';
        nextButton.innerHTML = 'Selanjutnya &raquo;';
        nextButton.addEventListener('click', () => { if (currentPage < pageCount) { currentPage++; showPage(currentPage); } });
        paginationControls.appendChild(nextButton);
    }

    function updateActiveButton(page) {
        const prevButton = paginationControls.querySelector('button:first-child');
        const nextButton = paginationControls.querySelector('button:last-child');
        if (prevButton) prevButton.disabled = (page === 1);
        if (nextButton) nextButton.disabled = (page === pageCount);
    }

    // Inisialisasi Paginasi
    createPaginationButtons();
    showPage(currentPage);
});
</script>
@endsection