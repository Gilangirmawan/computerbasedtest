@extends('layouts.app')

@section('content')
<div class="container">
    {{-- BAGIAN BARU: Tampilan Timer --}}
    <div class="alert alert-primary position-sticky" style="top: 10px; z-index: 1000;">
        <h5 class="text-center mb-0">
            Sisa Waktu: <span id="timer" class="font-weight-bold">--:--:--</span>
        </h5>
    </div>
    {{-- AKHIR BAGIAN BARU --}}

    <h3 class="mb-4">Ujian: {{ $ujian->nama_ujian }}</h3>

    <form id="formUjian" method="POST" action="{{ route('ikutujian.selesai', $ujian->id) }}">
        @csrf

        @foreach($soals as $key => $soal)
            <div class="mb-4 p-3 border rounded bg-light soal-item">
                {{-- Tampilkan gambar soal jika ada --}}
                <p><b>{{ $key+1 }}. {{ $soal->soal }}</b></p>

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
            <div class="d-flex justify-content-center mt-4" id="pagination-controls"></div>
        <button type="button" class="btn btn-success" id="btnSelesaiUjian">
            Selesai Ujian
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- BAGIAN SCRIPT YANG DIMODIFIKASI --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("btnSelesaiUjian");
    const form = document.getElementById("formUjian");
    const timerDisplay = document.getElementById("timer");
    
    // 1. LOGIKA TIMER
    // Ambil durasi dari data ujian (dalam menit)
    const durasiUjianMenit = {{ $ujian->waktu }};
    
    // Cek apakah ada waktu akhir yang tersimpan di localStorage (untuk handle refresh)
    let waktuSelesai = localStorage.getItem('waktuSelesaiUjian_{{ $ujian->id }}');

    if (!waktuSelesai) {
        // Jika tidak ada, set waktu selesai yang baru
        waktuSelesai = new Date().getTime() + durasiUjianMenit * 60 * 1000;
        localStorage.setItem('waktuSelesaiUjian_{{ $ujian->id }}', waktuSelesai);
    }

    const timerInterval = setInterval(function() {
        const sekarang = new Date().getTime();
        const selisih = waktuSelesai - sekarang;

        // Hitung jam, menit, detik
        const jam = Math.floor((selisih % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const menit = Math.floor((selisih % (1000 * 60 * 60)) / (1000 * 60));
        const detik = Math.floor((selisih % (1000 * 60)) / 1000);

        // Tampilkan di elemen timer
        timerDisplay.textContent = 
            ('0' + jam).slice(-2) + ":" + 
            ('0' + menit).slice(-2) + ":" + 
            ('0' + detik).slice(-2);

        // Jika waktu habis
        if (selisih < 0) {
            clearInterval(timerInterval);
            localStorage.removeItem('waktuSelesaiUjian_{{ $ujian->id }}'); // Hapus localStorage
            
            // Nonaktifkan event listener 'beforeunload' sebelum submit
            window.removeEventListener('beforeunload', handleBeforeUnload);

            Swal.fire({
                title: "Waktu Habis!",
                text: "Waktu pengerjaan Anda telah berakhir. Jawaban Anda akan otomatis disubmit.",
                icon: "info",
                allowOutsideClick: false,
                confirmButtonText: "OK"
            }).then(() => {
                form.submit();
            });
        }
    }, 1000);

    // 2. LOGIKA PENCEGAHAN KELUAR HALAMAN
    const handleBeforeUnload = (event) => {
        event.preventDefault();
        // Browser modern akan menampilkan pesan default, bukan string ini
        event.returnValue = 'Anda yakin ingin meninggalkan halaman? Proses ujian masih berlangsung.';
        return event.returnValue;
    };

    window.addEventListener('beforeunload', handleBeforeUnload);

    // 3. LOGIKA TOMBOL SELESAI
    btn.addEventListener("click", function () {
        Swal.fire({
            title: "Konfirmasi",
            text: "Anda yakin ingin menyelesaikan ujian ini? Jawaban tidak dapat diubah lagi.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Selesaikan!",
            cancelButtonText: "Batal",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Hapus pencegahan keluar halaman SEBELUM form disubmit
                window.removeEventListener('beforeunload', handleBeforeUnload);
                localStorage.removeItem('waktuSelesaiUjian_{{ $ujian->id }}');
                form.submit();
            }
        });
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const soalItems = document.querySelectorAll('.soal-item');
    const paginationControls = document.getElementById('pagination-controls');
    const soalPerPage = 5; // Tampilkan 5 soal per halaman
    const pageCount = Math.ceil(soalItems.length / soalPerPage);
    let currentPage = 1;

    function showPage(page) {
        // Sembunyikan semua soal terlebih dahulu
        soalItems.forEach(item => item.style.display = 'none');

        const startIndex = (page - 1) * soalPerPage;
        const endIndex = startIndex + soalPerPage;

        // Tampilkan hanya soal untuk halaman yang aktif
        for (let i = startIndex; i < endIndex && i < soalItems.length; i++) {
            soalItems[i].style.display = 'block';
        }

        // Perbarui tombol paginasi yang aktif
        updateActiveButton(page);
    }

    function createPaginationButtons() {
        if (pageCount <= 1) return; // Jangan buat tombol jika hanya 1 halaman

        const prevButton = document.createElement('button');
        prevButton.type = 'button';
        prevButton.className = 'btn btn-outline-primary me-2';
        prevButton.innerHTML = '&laquo; Sebelumnya';
        prevButton.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });
        paginationControls.appendChild(prevButton);

        const nextButton = document.createElement('button');
        nextButton.type = 'button';
        nextButton.className = 'btn btn-outline-primary';
        nextButton.innerHTML = 'Selanjutnya &raquo;';
        nextButton.addEventListener('click', () => {
            if (currentPage < pageCount) {
                currentPage++;
                showPage(currentPage);
            }
        });
        paginationControls.appendChild(nextButton);
    }

    function updateActiveButton(page) {
        // Di sini Anda bisa menambahkan logika untuk menonaktifkan tombol 'Sebelumnya' atau 'Selanjutnya'
        const prevButton = paginationControls.querySelector('button:first-child');
        const nextButton = paginationControls.querySelector('button:last-child');
        
        if (prevButton) prevButton.disabled = (page === 1);
        if (nextButton) nextButton.disabled = (page === pageCount);
    }

    // Inisialisasi
    createPaginationButtons();
    showPage(currentPage);
});
</script>

@endsection