<?php

namespace App\Http\Controllers;

use App\Exports\ExportRetakeOpportunities;
use App\Models\StudentRetakeOpportunity;
use App\Models\SubGrade;
use App\Models\Subject;
use App\Models\Year;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentRetakeOpportunityController extends Controller
{
    public function index(Request $request)
    {
        $retakeOpportunities = StudentRetakeOpportunity::query()
            ->with([
                'student:id,name,fa_name,father_name,fa_father_name,email,id_number',
                'subGrade:id,name,full_name',
                'year:id,name',
                'subject:id,name'
            ])
            ->filter($request)
            ->orderByDesc('id')
            ->paginate(50);
        $grades = SubGrade::whereIsActive(true)->get();
        $years = Year::all(['id', 'name']);
        $subjects = Subject::all(['id', 'name']);

        return inertia('Student/RetakeOpportunities', [
            'retakeOpportunities' => $retakeOpportunities,
            'grades' => $grades,
            'years' => $years,
            'subjects' => $subjects,
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(
            new ExportRetakeOpportunities($request->all()),
            'retake-opportunities-report.xlsx'
        );
    }
}
