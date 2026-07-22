<?php

namespace App\Imports;

use App\Models\Country;
use App\Models\Enrollment;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UpdateStudentInfoImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (isset($row['moodle_id'])) {
            $moodleId = $row['moodle_id'];

            $student = Student::whereIdNumber($moodleId)->first();
            if ($student) {
                $countryId = $student->country_id;

                $country = Country::whereName($row['country'])->value('id');
                if ($country) {
                    $countryId = $country;
                }

                $student->update([
                    'name' => $row['name'] ? $row['name'] : $student->name,
                    'last_name' => $row['last_name'] ? $row['last_name'] : $student->last_name,
                    'father_name' => $row['father_name'] ? $row['father_name'] : $student->father_name,
                    'fa_name' => $row['farsi_name'] ? $row['farsi_name'] : $student->fa_name,
                    'fa_father_name' => $row['farsi_father_name'] ? $row['farsi_father_name'] : $student->fa_father_name,
                    'fa_last_name' => $row['farsi_last_name'] ? $row['farsi_last_name'] : $student->fa_last_name,
                    'father_phone' => $row['father_phone'] ? $row['father_phone'] : $student->father_phone,
                    'father_email' => $row['father_email'] ? $row['father_email'] : $student->father_email,
                    'province' => $row['province'] ? $row['province'] : $student->province,
                    'email' => $row['email'] ? $row['email'] : $student->email,
                    'username' => $row['username'] ? $row['username'] : $student->username,
                    'school' => $row['school'] ? $row['school'] : $student->school,
                    'address' => $row['address'] ? $row['address'] : $student->address,
                    'miradore' => $row['miradore'] ? $row['miradore'] : $student->miradore,
                    'phone' => $row['phone'] ? $row['phone'] : $student->phone,
                    'sub_grade_id' => request()->grade_id ? request()->grade_id : $student->sub_grade_id,
                    'country_id' => $countryId,
                ]);

                if (request()->year && request()->grade_id) {
                    $where = [
                        'student_id' => $student->id,
                        'sub_grade_id' => request()->grade_id,
                        'year' => request()->year,
                    ];
                    Enrollment::updateOrCreate(
                        $where,
                        [
                            'user_id' => auth()->id(),
                        ],
                    );

                    $currentEnrollment = Enrollment::where($where)->value('id');
                    Enrollment::where(['student_id' => $student->id])->where('id', '<>', $currentEnrollment)->update(['is_active' => false]);
                }
            } else {
                $student = Student::where('email', $row['email'])->orWhere('username', $row['username'])->first();
                if($student){
                    $student->update([
                        'email' => $student->email.$student->id_number,
                        'username' => $student->username.$student->username,
                    ]);
                }
                $student = Student::create([
                    'name' => $row['name'],
                    'last_name' => $row['last_name'],
                    'father_name' => $row['father_name'],
                    'fa_name' => $row['farsi_name'] ? $row['farsi_name'] : $row['name'],
                    'fa_last_name' => $row['farsi_last_name'] ? $row['farsi_last_name'] : $row['last_name'],
                    'fa_father_name' => $row['farsi_father_name'] ? $row['farsi_father_name'] : $row['father_name'],
                    'id_number' => $row['moodle_id'],
                    'phone' => $row['phone'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'country_id' => request()->country_id,
                    'sub_grade_id' => request()->grade_id,
                    'name_in_system' => $row['name']. ' '.$row['last_name'],
                    'is_active' => 1,
                ]);

                Enrollment::create([
                    'student_id' => $student->id,
                    'sub_grade_id' => $student->sub_grade_id,
                    'user_id' => auth()->id(),
                    'year' => request()->year,
                ]);
            }
        }
    }
}
