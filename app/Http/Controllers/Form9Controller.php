<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\SessionHelper;

class Form9Controller extends Controller
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

        return view('form9', [
            'projectId' => $sessionData['project_id'],
            'impact' => '', // ไม่แสดงข้อมูลเก่าสำหรับ showForm
            'tb_period' => '',
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
            'impact' => 'required|string|min:3',
            'tb_period' => 'required|string',
        ], [
            'impact.required' => 'กรุณากรอกข้อมูลผลกระทบ',
            'impact.min' => 'ข้อมูลผลกระทบต้องมีอย่างน้อย 3 ตัวอักษร',
            'tb_period.required' => 'กรุณาเลือกระยะเวลาดำเนินการ'
        ]);

        Log::info('📥 Form9 store - เริ่มบันทึกข้อมูลผลกระทบและระยะเวลา', [
            'project_id' => $sessionData['project_id'],
            'edit_id' => $sessionData['edit_id'],
            'impact_length' => strlen($request->input('impact')),
            'period' => $request->input('tb_period')
        ]);

        try {
            DB::beginTransaction();

            // ลบข้อมูลเก่าใน timpact (ถ้ามี)
            DB::table('timpact')->where('project_id', $sessionData['project_id'])->delete();

            // เพิ่มข้อมูลใหม่ใน timpact
            DB::table('timpact')->insert([
                'project_id' => $sessionData['project_id'],
                'detail' => $request->input('impact'),
                'rec_date' => Carbon::now(),
                'edit_id' => $sessionData['edit_id'],
            ]);

            // อัปเดตข้อมูลใน tplan
            DB::table('tplan')->where('project_id', $sessionData['project_id'])->update([
                'period_time' => $request->input('tb_period'),
                'edit_date' => Carbon::now(),
            ]);

            DB::commit();

            Log::info('✅ Form9 store success - บันทึกข้อมูลสำเร็จครบถ้วน', [
                'project_id' => $sessionData['project_id'],
                'edit_id' => $sessionData['edit_id']
            ]);

            return redirect()->route('dashboard.show')->with('success', 'บันทึกข้อมูลโครงการเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Form9 Store Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'session_data' => $sessionData
            ]);
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        // ใช้ SessionHelper trait สำหรับ edit mode
        $this->initializeSession($id);
        $sessionData = $this->getSessionData();
        $this->debugSession('edit');

        $impact = DB::table('timpact')->where('project_id', $id)->first();
        $plan = DB::table('tplan')->where('project_id', $id)->first();

        if (!$plan) {
            Log::error('❌ [edit] Project not found', ['project_id' => $id]);
            return redirect()->route('projects.index')->withErrors(['project' => 'ไม่พบข้อมูลโครงการ']);
        }

        Log::info('📄 Form9 edit - โหลดข้อมูลสำหรับแก้ไข', [
            'project_id' => $id,
            'has_impact' => !is_null($impact),
            'has_plan' => !is_null($plan)
        ]);

        return view('form9', [
            'editMode' => true,
            'projectId' => $id,
            'impact' => $impact?->detail ?? '',
            'tb_period' => $plan?->period_time ?? '',
            'plan' => $plan,
        ]);
    }

    public function update(Request $request, $id)
    {
        // ใช้ SessionHelper trait สำหรับ update
        $this->initializeSession($id);
        $sessionData = $this->getSessionData();
        $this->debugSession('update');

        $request->validate([
            'impact' => 'required|string|min:3',
            'tb_period' => 'required|string',
        ], [
            'impact.required' => 'กรุณากรอกข้อมูลผลกระทบ',
            'impact.min' => 'ข้อมูลผลกระทบต้องมีอย่างน้อย 3 ตัวอักษร',
            'tb_period.required' => 'กรุณาเลือกระยะเวลาดำเนินการ'
        ]);

        Log::info('📥 Form9 update - เริ่มอัปเดตข้อมูลผลกระทบและระยะเวลา', [
            'project_id' => $id,
            'edit_id' => $sessionData['edit_id'],
            'impact_length' => strlen($request->input('impact')),
            'period' => $request->input('tb_period')
        ]);

        try {
            DB::beginTransaction();

            // อัปเดตหรือแทรกข้อมูลใน timpact
            DB::table('timpact')->updateOrInsert(
                ['project_id' => $id],
                [
                    'detail' => $request->input('impact'),
                    'edit_id' => $sessionData['edit_id'],
                    'rec_date' => Carbon::now(),
                ]
            );

            // อัปเดตใน tplan
            DB::table('tplan')->where('project_id', $id)->update([
                'period_time' => $request->input('tb_period'),
                'edit_date' => Carbon::now(),
            ]);

            DB::commit();

            Log::info('✅ Form9 update success - อัปเดตข้อมูลสำเร็จครบถ้วน', [
                'project_id' => $id,
                'edit_id' => $sessionData['edit_id']
            ]);

            return redirect()->route('form9.edit', ['id' => $id])
                ->with('success', 'อัปเดตข้อมูลผลกระทบและระยะเวลาเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Form9 Update Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'project_id' => $id,
                'session_data' => $sessionData
            ]);
            return back()->withErrors(['update' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage()])->withInput();
        }
    }
}
