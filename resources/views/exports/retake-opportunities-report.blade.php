<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Retake Opportunities Report</title>
</head>

<body>
    <table>
        <tr>
            <th style="text-align: center;border:1px solid #000 !important">NO</th>
            <th style="text-align: right;border:1px solid #000 !important">نام</th>
            <th style="text-align: left;border:1px solid #000 !important">Name</th>
            <th style="text-align: left;border:1px solid #000 !important">Email</th>
            <th style="text-align: center;border:1px solid #000 !important">Moodle ID</th>
            <th style="text-align: center;border:1px solid #000 !important">Grade</th>
            <th style="text-align: center;border:1px solid #000 !important">Year</th>
            <th style="text-align: center;border:1px solid #000 !important">Subject</th>
            <th style="text-align: center;border:1px solid #000 !important">First Chance Score</th>
            <th style="text-align: center;border:1px solid #000 !important">Second Chance Score</th>
            <th style="text-align: center;border:1px solid #000 !important">Third Chance Score</th>
            <th style="text-align: center;border:1px solid #000 !important">Second Chance Date</th>
            <th style="text-align: center;border:1px solid #000 !important">Third Chance Date</th>
            <th style="text-align: center;border:1px solid #000 !important">Status</th>
        </tr>
        @foreach ($retakeOpportunities as $opportunity)
            <tr>
                <td style="text-align: center;border:1px solid #000 !important">{{ $loop->iteration }}</td>
                <td style="text-align: right;border:1px solid #000 !important">{{ $opportunity->student->fa_name }} {{ $opportunity->student->fa_father_name }}</td>
                <td style="text-align: left;border:1px solid #000 !important">{{ $opportunity->student->name}} {{ $opportunity->student?->father_name }}</td>
                <td style="text-align: left;border:1px solid #000 !important">{{ $opportunity->student->email}}</td>
                <td style="text-align: center;border:1px solid #000 !important">{{ $opportunity->student->id_number}}</td>
                <td style="text-align: center;border:1px solid #000 !important">{{ $opportunity->subGrade->full_name}}</td>
                <td style="text-align: center;border:1px solid #000 !important">{{ $opportunity->year->name}}</td>
                <td style="text-align: center;border:1px solid #000 !important">{{ $opportunity->subject->name}}</td>
                <td style="text-align: center;border:1px solid #000 !important">{{ $opportunity->score}}</td>
                <td style="text-align: center;border:1px solid #000 !important">{{ $opportunity->second_chance_score}}</td>
                <td style="text-align: center;border:1px solid #000 !important">{{ $opportunity->third_chance_score}}</td>
                <td style="text-align: center;border:1px solid #000 !important">{{ $opportunity->second_chance_date ? \Carbon\Carbon::parse($opportunity->second_chance_date)->format('Y-m-d') : '' }}</td>
                <td style="text-align: center;border:1px solid #000 !important">{{ $opportunity->third_chance_date ? \Carbon\Carbon::parse($opportunity->third_chance_date)->format('Y-m-d') : '' }}</td>
                <td style="text-align: center;border:1px solid #000 !important">{{ $opportunity->is_passed ? 'Passed' : 'Failed' }}</td>
            </tr>
        @endforeach
    </table>
</body>

</html>

