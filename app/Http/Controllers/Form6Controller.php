<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class Form6Controller extends Controller
{
    public function showForm()
    {
        $projectId = session('project_id');

        if (!$projectId) {
            return redirect()->route('form1.show')->with('error', 'กรุณากรอกข้อมูลหน้าแรกก่อน');
        }

        return view('form6', [
            'projectId' => $projectId,
            'goalJob' => [],
            'goalDepart' => [],
            'goalPeople' => [],
        ]);
    }

    public function store(Request $request)
    {
        $projectId = session('project_id');
        $editId    = session('user_id') ?? session('admin_id') ?? 1;

        if (!$projectId) {
            return redirect()->route('form1.show')->with('error', 'กรุณากรอกข้อมูลหน้าแรกก่อน');
        }

        $request->validate([
            'job_advantage'    => 'nullable|json',
            'depart_advantage' => 'nullable|json',
            'people_advantage' => 'nullable|json',
        ]);

        $jobAdvantages    = json_decode($request->input('job_advantage') ?? '[]', true);
        $departAdvantages = json_decode($request->input('depart_advantage') ?? '[]', true);
        $peopleAdvantages = json_decode($request->input('people_advantage') ?? '[]', true);

        if (!is_array($jobAdvantages) || !is_array($departAdvantages) || !is_array($peopleAdvantages)) {
            return back()->withErrors(['data' => 'ข้อมูลไม่ถูกต้อง'])->withInput();
        }

        $max = max(count($jobAdvantages), count($departAdvantages), count($peopleAdvantages));
        $recDate = Carbon::now();

        Log::info('📥 เริ่ม insert ข้อมูล tadvantage', [
            'project_id' => $projectId,
            'edit_id'    => $editId,
            'max_rows'   => $max,
            'job'        => $jobAdvantages,
            'depart'     => $departAdvantages,
            'people'     => $peopleAdvantages,
        ]);

        try {
            for ($i = 0; $i < $max; $i++) {
                DB::table('tadvantage')->insert([
                    'project_id'       => $projectId,
                    'job_advantage'    => $jobAdvantages[$i] ?? '',
                    'depart_advantage' => $departAdvantages[$i] ?? '',
                    'people_advantage' => $peopleAdvantages[$i] ?? '',
                    'rec_date'         => $recDate,
                    'edit_id'          => $editId,
                ]);
            }

            Log::info('✅ บันทึกข้อมูลทั้งหมดสำเร็จ');
            return redirect()->route('form7.show', ['id' => $projectId])
                ->with('success', 'บันทึกข้อมูลประโยชน์ที่ได้รับเรียบร้อยแล้ว');
        } catch (Exception $e) {
            Log::error('❌ ERROR insert tadvantage', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ]);
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการบันทึก กรุณาตรวจสอบ log');
        }
    }

    public function edit($id)
    {
        session(['project_id' => $id]);

        // ดึงข้อมูลโครงการ
        $plan = DB::table('tplan')->where('project_id', $id)->first();

        // ดึงข้อมูลประโยชน์ที่จะได้รับ
        $goals = DB::table('tadvantage')->where('project_id', $id)->get();

        return view('form6', [
            'plan'        => $plan, // เพิ่มตัวแปรนี้เข้า view
            'projectId'   => $id,
            'goalJob'     => $goals->pluck('job_advantage')->filter()->map(fn($v) => ['detail' => $v])->toArray(),
            'goalDepart'  => $goals->pluck('depart_advantage')->filter()->map(fn($v) => ['detail' => $v])->toArray(),
            'goalPeople'  => $goals->pluck('people_advantage')->filter()->map(fn($v) => ['detail' => $v])->toArray(),
        ]);
    }
    public function update(Request $request, $id)
    {
        $editId = session('user_id') ?? session('admin_id') ?? 1;

        $request->validate([
            'job_advantage'    => 'nullable|json',
            'depart_advantage' => 'nullable|json',
            'people_advantage' => 'nullable|json',
        ]);

        $jobAdvantages    = json_decode($request->input('job_advantage') ?? '[]', true);
        $departAdvantages = json_decode($request->input('depart_advantage') ?? '[]', true);
        $peopleAdvantages = json_decode($request->input('people_advantage') ?? '[]', true);

        if (!is_array($jobAdvantages) || !is_array($departAdvantages) || !is_array($peopleAdvantages)) {
            return back()->withErrors(['data' => 'ข้อมูล JSON ไม่ถูกต้อง'])->withInput();
        }

        $max = max(count($jobAdvantages), count($departAdvantages), count($peopleAdvantages));
        $recDate = Carbon::now();

        try {
            DB::beginTransaction();
            DB::table('tadvantage')->where('project_id', $id)->delete();

            for ($i = 0; $i < $max; $i++) {
                DB::table('tadvantage')->insert([
                    'project_id'       => $id,
                    'job_advantage'    => $jobAdvantages[$i] ?? '',
                    'depart_advantage' => $departAdvantages[$i] ?? '',
                    'people_advantage' => $peopleAdvantages[$i] ?? '',
                    'rec_date'         => $recDate,
                    'edit_id'          => $editId,
                ]);
            }

            DB::commit();
            return redirect()->route('form6.edit', ['id' => $id])
                ->with('success', 'อัปเดตข้อมูลประโยชน์ที่ได้รับเรียบร้อยแล้ว');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('❌ ERROR update tadvantage', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ]);
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการอัปเดต กรุณาตรวจสอบ log');
        }
    }
}
