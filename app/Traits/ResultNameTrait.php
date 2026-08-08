<?php

namespace App\Traits;

use App\Enums\ResultCardEnum;
use App\Models\SkippedGradeSubject;

trait ResultNameTrait
{
    private function resultStatus($grade, $studentResult, $type = 1)
    {
        $skippedSubjectsCount = SkippedGradeSubject::where(['sub_grade_id' => $studentResult->sub_grade_id, 'year' => $studentResult->year])->count();

        if ($type == 3) {
            if (($grade->total_subjects - $skippedSubjectsCount) > ($studentResult->subject_passed + 3) || $studentResult->result_id == 5) {
                return ResultCardEnum::Repeat->value;
            } elseif (($grade->total_subjects - $skippedSubjectsCount) > ($studentResult->subject_passed) && $studentResult->result_id != 5) {
                return ResultCardEnum::TrayAgain->value;
            }
        } elseif ($type == 2) {
            if (($grade->total_subjects - $skippedSubjectsCount) > ($studentResult->final_subject_passed + 3) || $studentResult->final_result_id == 5) {
                return ResultCardEnum::Repeat->value;
            } elseif (($grade->total_subjects - $skippedSubjectsCount) > ($studentResult->final_subject_passed) && $studentResult->final_result_id != 5) {
                return ResultCardEnum::TrayAgain->value;
            }
        } else {
            if (($grade->total_subjects - $skippedSubjectsCount) > $studentResult->middle_subject_passed || $studentResult->middle_result_id == 5) {
                return 'ناکام';
            }
        }

        return 'کامیاب';
    }
}
