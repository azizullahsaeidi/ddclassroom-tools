<p>
    Dear {{ $student->name }},
</p>

<p>
    This is your Grade 9 monthly attendance
    update for {{ $month->name ?? '' }}
    {{ $log->year }}.
</p>

<p>
    Your absence percentage was
    <strong>
        {{ number_format(
            $log->absence_percentage,
            2
        ) }}%
    </strong>.
</p>

<p>
    Because your absence percentage was more
    than 30%, you are not eligible for monthly
    support for this month.
</p>

<p>
    Support type:
    <strong>
        {{
            $log->support_type === 'credit_card'
                ? 'Credit Card'
                : 'Cash'
        }}
    </strong>
</p>

<p>
    Regards,<br>
    DDClassroom Team
</p>
