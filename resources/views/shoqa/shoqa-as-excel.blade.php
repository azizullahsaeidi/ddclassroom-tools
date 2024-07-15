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
                <td style="width: 50% !important; vertical-align:middle !important; height:100px !important" colspan="5">
                    <img src="{{ public_path('images/logo.webp')}}"  height="70px;">
                </td>
                <td style="width: 50% !important; vertical-align:middle !important; height:100px !important" colspan="6">
                    <h4 height="20px">
                        امتحان: ( {{ $type == 1 ? 'چهارنیم ماه' : 'سالانه'}} )
                    </h4>
                    <h4 height="20px">
                        نام معلم: (..........)
                    </h4>
                    <h4 height="20px">
                        صنف: ( {{$grade->name}} )
                    </h4>
                    <h4 height="20px">
                        مضمون: ({{ $subject->name }} )
                    </h4>
                    <h4 height="20px">
                        تاریخ: ({{$year}})
                    </h4>
                </td>
            </tr>
        </table>
        <table style=";direction: rtl !important;">
            <tr>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">مجموع {{ $total }}</th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">ارزیابی {{ $evaluation }}
                </th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">کارخانگی {{ $homework }}
                </th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">فعالیت {{ $activity }}</th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">حاضری {{ $attendance }}</th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">تقریری {{ $oral }}
                </th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">تحریری {{ $written }}
                </th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">نام پدر
                </th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">نام</th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">نمبر اساس</th>
                <th style="text-align: center;border:1px solid #000 !important;text-weight:bold !important;">شماره</th>
            </tr>
            @foreach ($enrollments as $enrollment)
                <tr>
                    <td style="text-align: center;border:1px solid #000 !important">
                        {{ $enrollment->student?->score?->total }}
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
                        {{ $enrollment->student?->score?->attendance }}
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
            @endforeach
        </table>
    </div>
</body>

</html>
