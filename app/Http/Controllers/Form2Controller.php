<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\SessionHelper;

class Form2Controller extends Controller
{
    use SessionHelper;

    /**
     * Clear ข้อมูล OKR เก่าสำหรับโครงการใหม่
     */
    public function clearOldData()
    {
        $projectId = session('project_id');

        if ($projectId) {
            DB::table('tokr')->where('project_id', $projectId)->delete();
            Log::info('🗑️ Cleared old OKR data', ['project_id' => $projectId]);
        }

        return redirect()->route('form2.show')->with('success', 'ล้างข้อมูลเก่าแล้ว');
    }

    public function showForm(Request $request)
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

        $okrs = DB::table('tb_okr')->get();
        $subokrs = DB::table('tb_subokr')->where('okr_id', '>', 0)->get();

        // สำหรับ showForm ให้เป็น array ว่าง (ไม่ดึงข้อมูลเก่า)
        $selectedSubokrs = [];

        return view('form2', [
            'okrs' => $okrs,
            'subokrs' => $subokrs,
            'selectedSubokrs' => $selectedSubokrs,
            'projectId' => $sessionData['project_id'],
            'editMode' => false
        ]);
    }

    public function edit($id)
    {
        // ใช้ SessionHelper trait สำหรับ edit mode
        $this->initializeSession($id);
        $sessionData = $this->getSessionData();
        $this->debugSession('edit');

        $plan = DB::table('tplan')->where('project_id', $id)->first();

        if (!$plan) {
            Log::error('❌ [edit] Project not found', ['project_id' => $id]);
            return redirect()->route('projects.index')->withErrors(['project' => 'ไม่พบข้อมูลโครงการ']);
        }

        return view('form2', [
            'plan' => $plan,
            'okrs' => DB::table('tb_okr')->get(),
            'subokrs' => DB::table('tb_subokr')->where('okr_id', '>', 0)->get(),
            'selectedSubokrs' => DB::table('tokr')->where('project_id', $id)->pluck('okr_id')->toArray(),
            'editMode' => true,
            'projectId' => $id
        ]);
    }

    public function store(Request $request)
    {
        // ใช้ SessionHelper trait
        $this->initializeSession($request->input('project_id'));

        // ตรวจสอบ session และ redirect หากจำเป็น
        $sessionCheck = $this->validateSession();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        // ดึงข้อมูล session ที่จำเป็น
        $sessionData = $this->getSessionData();
        $this->debugSession('store');

        $request->validate([
            'subokrs' => 'required|array',
            'subokrs.*' => 'required|integer',
        ], [
            'subokrs.required' => 'กรุณาเลือก OKR อย่างน้อย 1 รายการ',
            'subokrs.array' => 'รูปแบบข้อมูล OKR ไม่ถูกต้อง'
        ]);

        try {
            DB::beginTransaction();

            // ลบข้อมูลเก่า
            DB::table('tokr')->where('project_id', $sessionData['project_id'])->delete();

            // เพิ่มข้อมูลใหม่
            foreach ($request->input('subokrs') as $subokrId) {
                DB::table('tokr')->insert([
                    'project_id' => $sessionData['project_id'],
                    'okr_id' => $subokrId,
                    'rec_date' => Carbon::now(),
                    'edit_id' => $sessionData['edit_id'],
                ]);
            }

            DB::commit();

            Log::info('✅ Form2 store success', [
                'project_id' => $sessionData['project_id'],
                'edit_id' => $sessionData['edit_id'],
                'inserted_okrs' => count($request->input('subokrs'))
            ]);

            return redirect()->route('form3.show', ['id' => $sessionData['project_id']])
                ->with('success', 'บันทึก OKR เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Form2 Store Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_data' => $sessionData
            ]);
            return back()->withErrors(['database' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        // ใช้ SessionHelper trait สำหรับ update
        $this->initializeSession($id);
        $sessionData = $this->getSessionData();
        $this->debugSession('update');

        $request->validate([
            'subokrs' => 'required|array',
            'subokrs.*' => 'required|integer',
        ], [
            'subokrs.required' => 'กรุณาเลือก OKR อย่างน้อย 1 รายการ',
            'subokrs.array' => 'รูปแบบข้อมูล OKR ไม่ถูกต้อง'
        ]);

        try {
            DB::beginTransaction();

            // ลบข้อมูลเก่า
            DB::table('tokr')->where('project_id', $id)->delete();

            // เพิ่มข้อมูลใหม่
            foreach ($request->input('subokrs') as $subokrId) {
                DB::table('tokr')->insert([
                    'project_id' => $id,
                    'okr_id' => $subokrId,
                    'rec_date' => Carbon::now(),
                    'edit_id' => $sessionData['edit_id'],
                ]);
            }

            DB::commit();

            Log::info('✅ Form2 update success', [
                'project_id' => $id,
                'edit_id' => $sessionData['edit_id'],
                'updated_okrs' => count($request->input('subokrs'))
            ]);

            return redirect()->route('form2.edit', ['id' => $id])
                ->with('success', 'อัปเดต OKR สำเร็จแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Form2 Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'project_id' => $id,
                'session_data' => $sessionData
            ]);
            return back()->withErrors(['database' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage()])->withInput();
        }
    }
}
