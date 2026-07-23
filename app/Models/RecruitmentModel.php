<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruitmentModel extends Model
{
    protected $fillable = [
        "id",
        "reference_number",
        "title",
        "location",
        "posted_by_id",
        "posted_by_name",
        "start_date",
        "end_date",
        "work_type",
        "job_type",
        "region_id",
        "region_name",
        "status",
        "current_state",
        "description",
        "enrollment_id",
        "organisation_unit_id",
        "lastUpdated",
        "created",
        "storedBy",
    ];
}
