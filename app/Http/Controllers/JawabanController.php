<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jawaban;
use Illuminate\Support\Facades\Auth;

class JawabanController extends Controller
{
    public function simpanJawaban(Request $request)
    {
        Jawaban::updateOrCreate(
            [
                'id_siswa'  => optional(Auth::siswa())->id,
                'id_soal'  => $request->id_soal,
            ],
            [
                'jawaban'  => $request->jawaban,
            ]
        );

        return response()->json(['success' => true]);
    }
}
