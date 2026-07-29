<!DOCTYPE html>
<html lang="en" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            padding: 0px;
            margin: 0px;
        }

        .font-small {
            font-size: 11px !important;
        }
    </style>

</head>

<body>
    <div style="direction: rtl !important;">
        <table style="direction: rtl !important;">
            <tr>
                <td colspan="9" rowspan="5">
                    <img src="{{ public_path('images/logo.png') }}" height="70px;">
                </td>
                <td style="text-align: right"> ( {{ $type == 1 ? 'چهارنیم ماه' : 'سالانه' }} )</td>
                <td>امتحان</td>
            </tr>
            <tr>
                <td></td>
                <td>نام معلم</td>
            </tr>
            <tr>
                <td style="text-align: right">( {{ $grade->name }} )</td>
                <td>صنف</td>
            </tr>
            <tr>
                <td style="text-align: right">({{ $subject->name }} )</td>
                <td>مضمون</td>
            </tr>
            <tr>
                <td style="text-align: right"> ({{ $year }})</td>
                <td>تاریخ</td>
            </tr>

            <tr>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">مجموع
                    {{ $total }}</th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">ارزیابی
                    {{ $evaluation }}
                </th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">پریزنتیشن
                    {{ $homework }}
                </th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">امتحانات صنفی و ارزیابی
                    {{ $activity }}</th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">فعالیت صنفی و حاضری
                    {{ $attendance }}</th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">کارخانگی
                    {{ $oral }}
                </th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">امتحان تحریری و امتحان تقریری
                    {{ $written }}
                </th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">نام پدر
                </th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">نام</th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">نمبر اساس
                </th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">شماره</th>
            </tr>
            @foreach ($enrollments as $enrollment)
                @if ($enrollment->student)
                    <?php
                    $studentScore = $enrollment->student?->score?->attendance;
                    $score = 0;
                    if (!$studentScore || $studentScore <= 0) {
                        $score = $enrollment->student?->attendance?->total_class_hours != 0 ? round((($enrollment->student?->attendance?->total_class_hours - $enrollment->student?->attendance?->absent) * 5) / $enrollment->student?->attendance?->total_class_hours, 2) : 0;
                    }
                    ?>
                    <tr>
                        <td style="text-align: center;border:1px solid #000 !important">
                            {{ $enrollment->student?->score?->total + $score }}
                        </td>
                        <td style="text-align: center;border:1px solid #000 !important">
                            {{ $enrollment->student?->score?->evaluation }}
                        </td>
                        <td style="text-align: center;border:1px solid #000 !important">
                            {{ $enrollment->student?->score?->homework }}
                        </td>
                        <td style="text-align: center;border:1px solid #000 !important">
                            {{ $enrollment->student?->score?->activity }}
                        </td>
                        <td style="text-align: center;border:1px solid #000 !important">
                            {{ $studentScore + $score }}

                        </td>
                        <td style="text-align: center;border:1px solid #000 !important">
                            {{ $enrollment->student?->score?->verbal }}
                        </td>
                        <td style="text-align: center;border:1px solid #000 !important">
                            {{ $enrollment->student?->score?->written }}
                        </td>
                        <td style="text-align: center;border:1px solid #000 !important">
                            {{ $enrollment->student->fa_father_name }}
                        </td>
                        <td style="text-align: center;border:1px solid #000 !important">
                            {{ $enrollment->student->fa_name }}
                        </td>
                        <td style="text-align: center;border:1px solid #000 !important">
                            {{ $enrollment->student->id_number }}
                        </td>
                        <td style="text-align: center;border:1px solid #000 !important">
                            {{ $loop->iteration }}
                        </td>
                    </tr>
                @endif
            @endforeach
        </table>
    </div>
</body>

</html>
