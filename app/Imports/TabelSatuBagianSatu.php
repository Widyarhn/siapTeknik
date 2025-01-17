<?php

// namespace App\Imports;

// use App\Models\LabelImport;
// use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\ToCollection;
// use Maatwebsite\Excel\Concerns\WithMappedCells;
// use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

// class TabelSatuBagianSatu implements WithMappedCells, ToCollection, WithCalculatedFormulas
// {
//     public function mapping(): array
//     {
//         $labelImports = LabelImport::where('sheet_name', '1-1')->get();

//         $structuredData = [];

//         foreach ($labelImports as $labelImport) {
//             if (!empty($labelImport->label) && !empty($labelImport->cell)) {
//                 $structuredData[$labelImport->label] = $labelImport->cell;
//             }
//         }
//         return $structuredData;
//     }

//     // public function model(array $row)
//     // {
//     //     $structuredArray = [];

//     //     foreach ($row as $label => $nilai) {
//     //         $structuredArray[] = [
//     //             'label' => $label,
//     //             'nilai' => $nilai,
//     //         ];
//     //     }
        
//     //     dd($structuredArray);
//     //     return LabelImport::where('label')([
//     //         'name' => $row['name'],
//     //         'email' => $row['email'],
//     //     ]);
//     // }

//     public function collection(Collection $rows)
//     {
        
//         foreach ($rows as $label=> $nilai) 
//         {
//             LabelImport::where('label', $label)->update([
//                 'nilai' => $nilai,
//             ]);
//         }
//     }
// }


namespace App\Imports;

use App\Models\LabelImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TabelSatuBagianSatu implements WithMappedCells, ToCollection
{
    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function mapping(): array
    {
        $labelImports = LabelImport::where('sheet_name', '1-1')->get();

        $structuredData = [];

        foreach ($labelImports as $labelImport) {
            if (!empty($labelImport->label) && !empty($labelImport->cell)) {
                $structuredData[$labelImport->label] = $labelImport->cell;
            }
        }
        return $structuredData;
    }

    public function collection(Collection $rows)
    {
        $spreadsheet = IOFactory::load($this->file->getRealPath());
        
        $sheet = $spreadsheet->getSheetByName('1-1');
        
        $structuredData = $this->mapping();

        foreach ($structuredData as $label => $cellAddress) {
            $calculatedValue = $sheet->getCell($cellAddress)->getCalculatedValue();

            LabelImport::where('label', $label)->update([
                'nilai' => $calculatedValue,
            ]);
        }
    }
}
