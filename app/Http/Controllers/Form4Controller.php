<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\QueryException;
use App\Http\Traits\SessionHelper;

class Form4Controller extends Controller
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

        return view('form4', [
            'editMode' => false,
            'projectId' => $sessionData['project_id'],
            'system_detail' => '',
            'old_workflow' => '',
            'new_workflow' => '',
            'whouse_users' => [],
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
            'system_detail' => 'required|string|min:3',
            'old_workflow' => 'nullable|string',
            'new_workflow' => 'nullable|string',
            'whouse_users' => 'nullable|array',
        ], [
            'system_detail.required' => 'กรุณากรอกรายละเอียดระบบ',
            'system_detail.min' => 'รายละเอียดระบบต้องมีอย่างน้อย 3 ตัวอักษร'
        ]);

        try {
            DB::beginTransaction();

            // ตรวจว่ามีโครงการใน tplan
            if (!DB::table('tplan')->where('project_id', $sessionData['project_id'])->exists()) {
                throw new Exception("❌ project_id: {$sessionData['project_id']} ไม่มีในตาราง tplan");
            }

            Log::info('🧪 INSERT or UPDATE tworkflow', [
                'project_id' => $sessionData['project_id'],
                'workflow' => $request->input('system_detail'),
                'edit_id' => $sessionData['edit_id']
            ]);

            // อัปเดตหรือเพิ่มข้อมูล workflow
            DB::table('tworkflow')->updateOrInsert(
                ['project_id' => $sessionData['project_id']],
                [
                    'workflow' => $request->input('system_detail'),
                    'old_workflow' => $request->input('old_workflow'),
                    'new_workflow' => $request->input('new_workflow'),
                    'rec_date' => Carbon::now(),
                    'edit_id' => $sessionData['edit_id'],
                ]
            );

            // ลบข้อมูลเก่าของ whouse ก่อน
            DB::table('twhouse')->where('project_id', $sessionData['project_id'])->delete();

            // เพิ่มข้อมูล whouse users ใหม่
            $whouseUsers = $request->input('whouse_users', []);
            foreach ($whouseUsers as $userLabel) {
                $whouseRecord = DB::table('tb_whouse')->where('name', $userLabel)->first();
                if ($whouseRecord) {
                    DB::table('twhouse')->insert([
                        'project_id' => $sessionData['project_id'],
                        'whouse_id' => $whouseRecord->id,
                        'rec_date' => Carbon::now(),
                        'edit_id' => $sessionData['edit_id'],
                    ]);
                }
            }

            DB::commit();

            Log::info('✅ Form4 store success', [
                'project_id' => $sessionData['project_id'],
                'edit_id' => $sessionData['edit_id'],
                'whouse_users_count' => count($whouseUsers)
            ]);

            return redirect()->route('form5.show', ['id' => $sessionData['project_id']])
                ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('❌ Form4 SQL ERROR', [
                'sql_message' => $e->getMessage(),
                'bindings' => $e->getBindings(),
                'session_data' => $sessionData
            ]);
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด SQL กรุณาตรวจสอบ log')->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('❌ Form4 Store Exception', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'session_data' => $sessionData
            ]);
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        // ใช้ SessionHelper trait สำหรับ edit mode
        $this->initializeSession($id);
        $sessionData = $this->getSessionData();
        $this->debugSession('edit');

        // ดึงข้อมูลโครงการจากตาราง tplan
        $plan = DB::table('tplan')->where('project_id', $id)->first();

        if (!$plan) {
            Log::error('❌ [edit] Project not found', ['project_id' => $id]);
            return redirect()->route('projects.index')->withErrors(['project' => 'ไม่พบข้อมูลโครงการ']);
        }

        // ดึงข้อมูลเวิร์กโฟลว์
        $workflow = DB::table('tworkflow')->where('project_id', $id)->first();

        // ดึงรายชื่อผู้ใช้งานระบบ (จากตารางเชื่อมโยง)
        $whouseUsers = DB::table('twhouse')
            ->join('tb_whouse', 'twhouse.whouse_id', '=', 'tb_whouse.id')
            ->where('twhouse.project_id', $id)
            ->pluck('tb_whouse.name')
            ->toArray();

        return view('form4', [
            'plan' => $plan,
            'editMode' => true,
            'projectId' => $id,
            'system_detail' => $workflow?->workflow ?? '',
            'old_workflow' => $workflow?->old_workflow ?? '',
            'new_workflow' => $workflow?->new_workflow ?? '',
            'whouse_users' => $whouseUsers,
        ]);
    }

    public function update(Request $request, $id)
    {
        // ใช้ SessionHelper trait สำหรับ update
        $this->initializeSession($id);
        $sessionData = $this->getSessionData();
        $this->debugSession('update');

        $request->validate([
            'system_detail' => 'required|string|min:3',
            'old_workflow' => 'nullable|string',
            'new_workflow' => 'nullable|string',
            'whouse_users' => 'nullable|array',
        ], [
            'system_detail.required' => 'กรุณากรอกรายละเอียดระบบ',
            'system_detail.min' => 'รายละเอียดระบบต้องมีอย่างน้อย 3 ตัวอักษร'
        ]);

        try {
            DB::beginTransaction();

            // อัปเดตข้อมูล workflow
            DB::table('tworkflow')->updateOrInsert(
                ['project_id' => $id],
                [
                    'workflow' => $request->input('system_detail'),
                    'old_workflow' => $request->input('old_workflow'),
                    'new_workflow' => $request->input('new_workflow'),
                    'rec_date' => Carbon::now(),
                    'edit_id' => $sessionData['edit_id'],
                ]
            );

            // ลบข้อมูลเก่าของ whouse
            DB::table('twhouse')->where('project_id', $id)->delete();

            // เพิ่มข้อมูล whouse users ใหม่
            foreach ($request->input('whouse_users', []) as $userLabel) {
                $whouseRecord = DB::table('tb_whouse')->where('name', $userLabel)->first();
                if ($whouseRecord) {
                    DB::table('twhouse')->insert([
                        'project_id' => $id,
                        'whouse_id' => $whouseRecord->id,
                        'rec_date' => Carbon::now(),
                        'edit_id' => $sessionData['edit_id'],
                    ]);
                }
            }

            DB::commit();

            Log::info('✅ Form4 update success', [
                'project_id' => $id,
                'edit_id' => $sessionData['edit_id'],
                'whouse_users_count' => count($request->input('whouse_users', []))
            ]);

            return redirect()->route('form4.edit', ['id' => $id])
                ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('❌ Form4 Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'project_id' => $id,
                'session_data' => $sessionData
            ]);
            return back()->withErrors(['db' => 'เกิดข้อผิดพลาดในการอัปเดต: ' . $e->getMessage()])->withInput();
        }
    }
}
