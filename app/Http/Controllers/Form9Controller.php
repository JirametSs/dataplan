<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Form9Controller extends Controller
{
    public function showForm()
    {
        return view('form9');
    }

    public function store(Request $request)
    {
        $projectId = session('project_id');
        $userId    = session('user_id') ?? 0;

        if (!$projectId) {
            return redirect()->route('form1.show')->with('error', 'กรุณากรอกข้อมูลหน้าแรกก่อน');
        }

        $request->validate([
            'impact'    => 'required|string',
            'tb_period' => 'required|string',
        ]);

        DB::table('timpact')->insert([
            'project_id' => $projectId,
            'detail'     => $request->input('impact'),
            'rec_date'   => Carbon::now(),
            'edit_id'    => $userId,
        ]);

        DB::table('tplan')->where('project_id', $projectId)->update([
            'period_time' => $request->input('tb_period'),
            'edit_date'   => Carbon::now(),
        ]);

        return redirect()->route('dashboard.show')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        session(['project_id' => $id]);

        $impact = DB::table('timpact')->where('project_id', $id)->first();
        $plan   = DB::table('tplan')->where('project_id', $id)->first();

        return view('form9', [
            'editMode'   => true,
            'projectId'  => $id,
            'impact'     => $impact?->detail ?? '',
            'tb_period'  => $plan?->period_time ?? '',
            'plan'       => $plan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $userId = session('user_id') ?? 0;

        $request->validate([
            'impact'    => 'required|string',
            'tb_period' => 'required|string',
        ]);

        // อัปเดตหรือแทรกข้อมูลใน timpact
        DB::table('timpact')->updateOrInsert(
            ['project_id' => $id],
            [
                'detail'   => $request->input('impact'),
                'edit_id'  => $userId,
                'rec_date' => Carbon::now(),
            ]
        );

        // อัปเดตใน tplan
        DB::table('tplan')->where('project_id', $id)->update([
            'period_time' => $request->input('tb_period'),
            'edit_date'   => Carbon::now(),
        ]);

        return redirect()->route('form9.edit', ['id' => $id])
            ->with('success', 'อัปเดตข้อมูลประมาณการเรียบร้อยแล้ว');
    }
}
