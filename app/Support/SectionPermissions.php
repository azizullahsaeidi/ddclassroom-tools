<?php

namespace App\Support;

final class SectionPermissions
{
    public const ALL = [
        'dashboard.view' => 'View dashboard',

        'students.view' => 'View students and handbooks',
        'students.create' => 'Create students and results',
        'students.edit' => 'Edit students and results',
        'students.delete' => 'Delete students and results',

        'attendance.view' => 'View attendance',
        'attendance.create' => 'Create attendance',
        'attendance.edit' => 'Edit attendance',
        'attendance.delete' => 'Delete attendance',

        'reports.view' => 'View reports',
        'reports.export' => 'Export reports',

        'scores.view' => 'View student scores',
        'scores.create' => 'Create student scores',
        'scores.edit' => 'Edit student scores',
        'scores.delete' => 'Delete student scores',

        'shoqa.view' => 'View Shoqa',
        'shoqa.export' => 'Export Shoqa',

        'countries.view' => 'View countries',
        'countries.create' => 'Create countries',
        'countries.edit' => 'Edit countries',
        'countries.delete' => 'Delete countries',

        'grades.view' => 'View grades',
        'grades.create' => 'Create grades',
        'grades.edit' => 'Edit grades',
        'grades.delete' => 'Delete grades',

        'subjects.view' => 'View subjects',
        'subjects.create' => 'Create subjects',
        'subjects.edit' => 'Edit subjects',
        'subjects.delete' => 'Delete subjects',

        'semester-subjects.view' => 'View semester subjects',
        'semester-subjects.create' => 'Create semester subjects',
        'semester-subjects.edit' => 'Edit semester subjects',
        'semester-subjects.delete' => 'Delete semester subjects',

        'years.view' => 'View years',
        'years.create' => 'Create years',
        'years.edit' => 'Edit years',
        'years.delete' => 'Delete years',

        'results.view' => 'View results',
        'results.create' => 'Create results',
        'results.edit' => 'Edit results',
        'results.delete' => 'Delete results',

        'homeroom-teachers.view' => 'View homeroom teachers',
        'homeroom-teachers.create' => 'Create homeroom teachers',
        'homeroom-teachers.edit' => 'Edit homeroom teachers',
        'homeroom-teachers.delete' => 'Delete homeroom teachers',

        'skipped-subjects.view' => 'View skipped subjects',
        'skipped-subjects.create' => 'Create skipped subjects',
        'skipped-subjects.edit' => 'Edit skipped subjects',
        'skipped-subjects.delete' => 'Delete skipped subjects',

        'users.view' => 'View users and teachers',
        'users.create' => 'Create users and teachers',
        'users.edit' => 'Edit users and teachers',
        'users.delete' => 'Delete users and teachers',

        'roles.view' => 'View roles',
        'roles.create' => 'Create roles',
        'roles.edit' => 'Edit roles',
        'roles.delete' => 'Delete roles',

        'permissions.view' => 'View permissions',
        'permissions.create' => 'Create permissions',
        'permissions.edit' => 'Edit permissions',
        'permissions.delete' => 'Delete permissions',

        'backups.view' => 'View backups',
        'backups.download' => 'Download backups',
        'logs.view' => 'View application logs',
    ];

    public const TEACHER = [
        'dashboard.view',
        'students.view', 'students.create', 'students.edit', 'students.delete',
        'attendance.view', 'attendance.create', 'attendance.edit', 'attendance.delete',
        'reports.view', 'reports.export',
        'scores.view', 'scores.create', 'scores.edit', 'scores.delete',
        'shoqa.view', 'shoqa.export',
    ];

    public const USER = [
        'dashboard.view',
        'reports.view',
        'reports.export',
    ];

    private function __construct() {}
}
