<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use App\Http\Traits\SessionHelper;

class Form6Controller extends Controller
{
    use SessionHelper;

    public function showForm()
    {
        // ใช้ SessionHelper trait
        $this->initializeSession();

        // ตรวจสอบ session และ redirect หากจำเป็น
        $sessionCheck = $this->validateSession();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        // ดึงข้อมูล session ที่จำเป็น
        $sessionData = $this->getSessionData();

        // Debug session
        $this->debugSession('showForm');

        return view('form6', [
            'projectId' => $sessionData['project_id'],
            'goalJob' => [],
            'goalDepart' => [],
            'goalPeople' => [],
            'editMode' => false
        ]);
    }

    public function store(Request $request)
    {
        // ใช้ SessionHelper trait
        $this->initializeSession();

        // ตรวจสอบ session และ redirect หากจำเป็น
        $sessionCheck = $this->validateSession();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        // ดึงข้อมูล session ที่จำเป็น
        $sessionData = $this->getSessionData();
        $this->debugSession('store');

        $request->validate([
            'job_advantage' => 'nullable|json',
            'depart_advantage' => 'nullable|json',
            'people_advantage' => 'nullable|json',
        ], [
            'job_advantage.json' => 'รูปแบบข้อมูลประโยชน์ด้านงานไม่ถูกต้อง',
            'depart_advantage.json' => 'รูปแบบข้อมูลประโยชน์ด้านหน่วยงานไม่ถูกต้อง',
            'people_advantage.json' => 'รูปแบบข้อมูลประโยชน์ด้านบุคลากรไม่ถูกต้อง'
        ]);

        $jobAdvantages = json_decode($request->input('job_advantage') ?? '[]', true);
        $departAdvantages = json_decode($request->input('depart_advantage') ?? '[]', true);
        $peopleAdvantages = json_decode($request->input('people_advantage') ?? '[]', true);

        if (!is_array($jobAdvantages) || !is_array($departAdvantages) || !is_array($peopleAdvantages)) {
            return back()->withErrors(['data' => 'ข้อมูลไม่ถูกต้อง กรุณาตรวจสอบรูปแบบข้อมูล'])->withInput();
        }

        $max = max(count($jobAdvantages), count($departAdvantages), count($peopleAdvantages));

        // ตรวจสอบว่ามีข้อมูลอย่างน้อย 1 รายการ
        if ($max === 0) {
            return back()->withErrors(['data' => 'กรุณากรอกข้อมูลประโยชน์ที่ได้รับอย่างน้อย 1 รายการ'])->withInput();
        }

        Log::info('📥 Form6 store - เริ่ม insert ข้อมูล tadvantage', [
            'project_id' => $sessionData['project_id'],
            'edit_id' => $sessionData['edit_id'],
            'max_rows' => $max,
            'job_count' => count($jobAdvantages),
            'depart_count' => count($departAdvantages),
            'people_count' => count($peopleAdvantages)
        ]);

        try {
            DB::beginTransaction();

            // ลบข้อมูลเก่า
            DB::table('tadvantage')->where('project_id', $sessionData['project_id'])->delete();

            // เพิ่มข้อมูลใหม่
            for ($i = 0; $i < $max; $i++) {
                DB::table('tadvantage')->insert([
                    'project_id' => $sessionData['project_id'],
                    'job_advantage' => $jobAdvantages[$i] ?? '',
                    'depart_advantage' => $departAdvantages[$i] ?? '',
                    'people_advantage' => $peopleAdvantages[$i] ?? '',
                    'rec_date' => Carbon::now(),
                    'edit_id' => $sessionData['edit_id'],
                ]);
            }

            DB::commit();

            Log::info('✅ Form6 store success - บันทึกข้อมูลทั้งหมดสำเร็จ', [
                'project_id' => $sessionData['project_id'],
                'edit_id' => $sessionData['edit_id'],
                'inserted_rows' => $max
            ]);

            return redirect()->route('form7.show', ['id' => $sessionData['project_id']])
                ->with('success', 'บันทึกข้อมูลประโยชน์ที่ได้รับเรียบร้อยแล้ว');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('❌ Form6 Store Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'session_data' => $sessionData
            ]);
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        // ใช้ SessionHelper trait สำหรับ edit mode
        $this->initializeSession($id);
        $sessionData = $this->getSessionData();
        $this->debugSession('edit');

        // ดึงข้อมูลโครงการ
        $plan = DB::table('tplan')->where('project_id', $id)->first();

        if (!$plan) {
            Log::error('❌ [edit] Project not found', ['project_id' => $id]);
            return redirect()->route('projects.index')->withErrors(['project' => 'ไม่พบข้อมูลโครงการ']);
        }

        // ดึงข้อมูลประโยชน์ที่จะได้รับ
        $goals = DB::table('tadvantage')->where('project_id', $id)->get();

        return view('form6', [
            'plan' => $plan,
            'projectId' => $id,
            'goalJob' => $goals->pluck('job_advantage')->filter()->map(fn($v) => ['detail' => $v])->toArray(),
            'goalDepart' => $goals->pluck('depart_advantage')->filter()->map(fn($v) => ['detail' => $v])->toArray(),
            'goalPeople' => $goals->pluck('people_advantage')->filter()->map(fn($v) => ['detail' => $v])->toArray(),
            'editMode' => true
        ]);
    }

    public function update(Request $request, $id)
    {
        // ใช้ SessionHelper trait สำหรับ update
        $this->initializeSession($id);
        $sessionData = $this->getSessionData();
        $this->debugSession('update');

        $request->validate([
            'job_advantage' => 'nullable|json',
            'depart_advantage' => 'nullable|json',
            'people_advantage' => 'nullable|json',
        ], [
            'job_advantage.json' => 'รูปแบบข้อมูลประโยชน์ด้านงานไม่ถูกต้อง',
            'depart_advantage.json' => 'รูปแบบข้อมูลประโยชน์ด้านหน่วยงานไม่ถูกต้อง',
            'people_advantage.json' => 'รูปแบบข้อมูลประโยชน์ด้านบุคลากรไม่ถูกต้อง'
        ]);

        $jobAdvantages = json_decode($request->input('job_advantage') ?? '[]', true);
        $departAdvantages = json_decode($request->input('depart_advantage') ?? '[]', true);
        $peopleAdvantages = json_decode($request->input('people_advantage') ?? '[]', true);

        if (!is_array($jobAdvantages) || !is_array($departAdvantages) || !is_array($peopleAdvantages)) {
            return back()->withErrors(['data' => 'ข้อมูล JSON ไม่ถูกต้อง กรุณาตรวจสอบรูปแบบข้อมูล'])->withInput();
        }

        $max = max(count($jobAdvantages), count($departAdvantages), count($peopleAdvantages));

        Log::info('📥 Form6 update - เริ่ม update ข้อมูล tadvantage', [
            'project_id' => $id,
            'edit_id' => $sessionData['edit_id'],
            'max_rows' => $max,
            'job_count' => count($jobAdvantages),
            'depart_count' => count($departAdvantages),
            'people_count' => count($peopleAdvantages)
        ]);

        try {
            DB::beginTransaction();

            // ลบข้อมูลเก่า
            DB::table('tadvantage')->where('project_id', $id)->delete();

            // เพิ่มข้อมูลใหม่ (ถ้ามีข้อมูล)
            if ($max > 0) {
                for ($i = 0; $i < $max; $i++) {
                    DB::table('tadvantage')->insert([
                        'project_id' => $id,
                        'job_advantage' => $jobAdvantages[$i] ?? '',
                        'depart_advantage' => $departAdvantages[$i] ?? '',
                        'people_advantage' => $peopleAdvantages[$i] ?? '',
                        'rec_date' => Carbon::now(),
                        'edit_id' => $sessionData['edit_id'],
                    ]);
                }
            }

            DB::commit();

            Log::info('✅ Form6 update success - อัปเดตข้อมูลทั้งหมดสำเร็จ', [
                'project_id' => $id,
                'edit_id' => $sessionData['edit_id'],
                'updated_rows' => $max
            ]);

            return redirect()->route('form6.edit', ['id' => $id])
                ->with('success', 'อัปเดตข้อมูลประโยชน์ที่ได้รับเรียบร้อยแล้ว');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('❌ Form6 Update Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'project_id' => $id,
                'session_data' => $sessionData
            ]);
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการอัปเดต: ' . $e->getMessage())->withInput();
        }
    }
}
