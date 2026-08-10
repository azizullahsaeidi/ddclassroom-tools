<?php

namespace App\Exports;

use App\Models\StudentRetakeOpportunity;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportRetakeOpportunities implements FromView, ShouldAutoSize
{
    public function __construct(private array $filters) {}

    public function view(): View
    {
        $retakeOpportunities = StudentRetakeOpportunity::query()
            ->with([
                'student:id,name,fa_name,father_name,fa_father_name,email,id_number',
                'subGrade:id,name,full_name',
                'year:id,name',
                'subject:id,name'
            ])
            ->filter($this->filters)
            ->orderByDesc('id')
            ->get();

        return view('exports.retake-opportunities-report', [
            'retakeOpportunities' => $retakeOpportunities,
        ]);
    }
}

