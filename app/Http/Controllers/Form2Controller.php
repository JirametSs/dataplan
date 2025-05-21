<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Form2Controller extends Controller
{
    public function showForm(Request $request)
    {
        // $projectId = session('project_id');

        // echo $projectId;
        // exit;

        // if (!$projectId) {
        // return redirect()->route('form1.show')->withErrors('ไม่พบข้อมูลโครงการ');
        // }

        $okrs    = DB::table('tb_okr')->get();
        $subokrs = DB::table('tb_subokr')->where('okr_id', '>', 0)->get();

        $selectedSubokrs = DB::table('tokr')
            ->pluck('okr_id')
            ->toArray();

        return view('form2', compact('okrs', 'subokrs'));
    }

    public function edit($id)
    {
        session(['project_id' => $id]);

        $plan = DB::table('tplan')->where('project_id', $id)->first();

        return view('form2', [
            'plan' => $plan,
            'okrs' => DB::table('tb_okr')->get(),
            'subokrs' => DB::table('tb_subokr')->where('okr_id', '>', 0)->get(),
            'selectedSubokrs' => DB::table('tokr')->where('project_id', $id)->pluck('okr_id')->toArray()
        ])->with('editMode', true);
    }

    public function store(Request $request)
    {

        if (!session()->has('project_id')) {
            session(['project_id' => $request->input('project_id') ?? 1]);
        }

        if (!session()->has('admin_id')) {
            session(['admin_id' => optional(Auth::user())->id ?? 1]);
        }

        $projectId = session('project_id');
        $editId    = session('admin_id');
        $recDate   = Carbon::now();

        $request->validate([
            'subokrs'   => 'required|array',
            'subokrs.*' => 'required|integer',
        ]);

        DB::table('tokr')->where('project_id', $projectId)->delete();

        foreach ($request->input('subokrs') as $subokrId) {
            DB::table('tokr')->insert([
                'project_id' => $projectId,
                'okr_id'     => $subokrId,
                'rec_date'   => $recDate,
                'edit_id'    => $editId,
            ]);
        }

        return redirect()->route('form3.show', ['id' => $projectId])
            ->with('success', 'บันทึก OKR เรียบร้อยแล้ว');
    }

    public function update(Request $request, $id)
    {

        session(['project_id' => $id]);

        if (!session()->has('admin_id')) {
            session(['admin_id' => optional(Auth::user())->id ?? 1]);
        }

        $editId  = session('admin_id');
        $recDate = Carbon::now();

        $request->validate([
            'subokrs'   => 'required|array',
            'subokrs.*' => 'required|integer',
        ]);

        DB::table('tokr')->where('project_id', $id)->delete();

        foreach ($request->input('subokrs') as $subokrId) {
            DB::table('tokr')->insert([
                'project_id' => $id,
                'okr_id'     => $subokrId,
                'rec_date'   => $recDate,
                'edit_id'    => $editId,
            ]);
        }

        return redirect()->route('form2.edit', ['id' => $id])
            ->with('success', 'อัปเดต OKR สำเร็จแล้ว');
    }
}
