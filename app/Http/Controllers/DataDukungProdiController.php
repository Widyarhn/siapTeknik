<?php

namespace App\Http\Controllers;

use App\Models\DataDukung;
use App\Models\Golongan;
use Illuminate\Support\Facades\Storage;
use App\Models\ProgramStudi;
use App\Models\Kriteria;
use App\Models\MatriksPenilaian;
use App\Models\Suplemen;
use App\Models\UserProdi;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DataDukungProdiController extends Controller
{

    public function elemen(Request $request, $id_prodi)
    {
        $user_prodi = UserProdi::where("user_id", Auth::user()->id)
        ->where("program_studi_id", $id_prodi)
        ->get();
        $data = [
            "kriteria" => Kriteria::all(),
        ];
        $program_studi = ProgramStudi::findOrFail($id_prodi);
        return view('prodi.dokumen.data-dukung.elemen', $data, ['program_studi'=>$program_studi, 'user_prodi'=>$user_prodi]);
    }

    public function json(Request $request, $id_prodi)
    {
        $data = Kriteria::query();
        $year = date("Y");

        if ($request->searchByYear) {
            $year = $request->searchByYear;
        }

        // if ($request->searchByYear) {
        //     $data->whereHas("matriks_penilaian.data_dukung.program_studi.user_prodi.tahun", function ($query) use ($request) {
        //        $query->where('tahun', $request->searchByYear); 
        //     });
        // }

        $data = $data->orderBy('id', 'ASC')->get();

        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function ($row) use ($id_prodi, $year)
        {
            return '<div class="buttons">
            <a href="'.route('prodi.data-dukung.data', ['id' => $row->id, 'id_prodi'=>$id_prodi, 'tahun' => $year]).'" class="btn btn-primary btn-md"><i class="fa fa-eye"></i></a>
            </div>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function data(Request $request, $id, $id_prodi, $year)
    {   
        $kriteria = Kriteria::where("id", $id)->first();

        $program_studi = ProgramStudi::findOrFail($id_prodi);

        $user_prodi = UserProdi::where("user_id", Auth::user()->id)->where("program_studi_id", $program_studi->id)->first();
        
        if (count($kriteria->matriks_penilaian) > 0 ) {
            $data["matriks_penilaian"] = MatriksPenilaian::where("jenjang_id", $user_prodi->jenjang_id)->where("kriteria_id", $id)->get();
        } else if (count($kriteria->suplemen) > 0) {
            $data["suplemen"] = Suplemen::where("program_studi_id", $id_prodi)->where("kriteria_id", $id)->get();
        }else{
            $data[" "] = "Belum ada data yang dimasukkan";
        }

        
        return view("prodi.dokumen.data-dukung.data", $data, compact("kriteria", "program_studi", "user_prodi", "year"));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'file' => ['required', 'mimes:pdf']
        ],
        [
            'file.mimes' => 'File harus berupa pdf'
        ]);
        $file = $request->file('file');
        $file_name= $file->getClientOriginalName();
        $file->move('storage/data_dukung', $file->getClientOriginalName());
        $data_dukung = new DataDukung;
        $data_dukung->file = $file_name;
        $data_dukung->program_studi_id = $request->program_studi_id;
        $data_dukung->matriks_penilaian_id = empty($request->matriks_penilaian_id) ? null : $request
        ->matriks_penilaian_id;
        $data_dukung->suplemen_id = empty($request->suplemen_id) ? null : $request->suplemen_id;
        $data_dukung->tahun_id = $request->tahun_id;
        $data_dukung->jenjang_id = $request->jenjang_id;
        $data_dukung->save();

        return redirect()->back()->with('success', 'Data Dukung Berhasil Ditambahkan');
    }

    public function edit(Request $request, $id)
    {
        $data = [
            "kriteria" => Kriteria::all(),
            "golongan" => Golongan::all(),
        ];
        $data_dukung = DataDukung::find($id);
        return view('prodi.diploma-tiga.dokumen.data-dukung.edit', $data, [ 'data_dukung' => $data_dukung]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'file' => ['required', 'mimes:pdf']
        ],
        [
            'file.mimes' => 'File harus berupa pdf'
        ]);
        $data_dukung = DataDukung::find($id);

        if($request->hasFile('file')){
            $file = $request->file('file');
            Storage::delete("public/data_dukung/".$data_dukung->file);
            $file->storeAs('public/data_dukung', $file->getClientOriginalName());
            $file = $file->getClientOriginalName();
        }else {
            $file = $data_dukung->file;
        }
        $data_dukung->update([
            "file" => $file,
        ]);

        return redirect()->back()->with('success', 'Data Dukung Berhasil Diubah');
    }

    public function elemenHistory(Request $request, $id_prodi)
    {
        $tahun = UserProdi::with('tahun')->where("user_id", Auth::user()->id)->where("program_studi_id", $id_prodi)->first();
        $data = [
            "kriteria" => Kriteria::all(),
        ];
        $program_studi = ProgramStudi::findOrFail($id_prodi);
        return view('prodi.dokumen.data-dukung.elemen-history', $data, ['program_studi'=>$program_studi, 'tahun'=>$tahun]);
    }

    public function jsonHistory(Request $request, $id_prodi)
    {
        $data = Kriteria::orderBy('id', 'ASC')->get();

        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function ($row) use ($id_prodi)
        {
            return '<div class="buttons">
            <a href="'.route('prodi.data-dukung.dataHistory', ['id' => $row->id, 'id_prodi'=>$id_prodi]).'" class="btn btn-primary btn-md"><i class="fa fa-eye"></i></a>
            </div>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function dataHistory(Request $request, $id, $id_prodi)
    {   
        $kriteria = Kriteria::where("id", $id)->first();

        $program_studi = ProgramStudi::findOrFail($id_prodi);

        $user_prodi = UserProdi::where("user_id", Auth::user()->id)->where("program_studi_id", $program_studi->id)->first();
        
        if (count($kriteria->matriks_penilaian) > 0 ) {
            $data["matriks_penilaian"] = MatriksPenilaian::where("jenjang_id", $user_prodi->jenjang_id)->where("kriteria_id", $id)->get();
        } else if (count($kriteria->suplemen) > 0) {
            $data["suplemen"] = Suplemen::where("program_studi_id", $id_prodi)->where("kriteria_id", $id)->get();
        }
        
        return view("prodi.dokumen.data-dukung.data-history", $data, compact("kriteria", "program_studi", "user_prodi"));
      
    }
    
}