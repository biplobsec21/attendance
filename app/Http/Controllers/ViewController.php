<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function profileIndex()
    {
        return view("mpm.page.profile.index");
    }

    public function profileCreate()
    {
        return view("mpm.page.profile.create");
    }

    public function profileView()
    {
        return view("mpm.page.profile.view");
    }

    public function rankIndex()
    {
        return view("mpm.page.rank.index");
    }

    public function rankCreate()
    {
        return view("mpm.page.rank.create");
    }

    public function companyIndex()
    {
        return view("mpm.page.company.index");
    }

    public function companyCreate()
    {
        return view("mpm.page.company.create");
    }

    public function courseIndex()
    {
        return view("mpm.page.course.index");
    }

    public function courseCreate()
    {
        return view("mpm.page.course.create");
    }

    public function sportsIndex()
    {
        return view("mpm.page.sports.index");
    }

    public function sportsCreate()
    {
        return view("mpm.page.sports.create");
    }

    public function otherQualIndex()
    {
        return view("mpm.page.otherQual.index");
    }

    public function otherQualCreate()
    {
        return view("mpm.page.otherQual.create");
    }

    public function absentIndex()
    {
        return view("mpm.page.absent.index");
    }

    public function absentCreate()
    {
        return view("mpm.page.absent.create");
    }

    public function dutyIndex()
    {
        return view("mpm.page.duty.index");
    }

    public function dutyCreate()
    {
        return view("mpm.page.duty.create");
    }

    public function assignDutyIndex()
    {
        return view("mpm.page.assignDuty.index");
    }

    public function assignDutyCreate()
    {
        return view("mpm.page.assignDuty.create");
    }

    public function approveDuty()
    {
        return view("mpm.page.approval.duty");
    }

    public function leaveIndex()
    {
        return view("mpm.page.leave.index");
    }

    public function leaveCreate()
    {
        return view("mpm.page.leave.create");
    }

    public function approveLeave()
    {
        return view("mpm.page.approval.leave");
    }


    

    public function filter()
    {
        return view("mpm.page.employee.filter");
    }
    public function filters()
    {
        return view("mpm.page.employee.filters");
    }
}
