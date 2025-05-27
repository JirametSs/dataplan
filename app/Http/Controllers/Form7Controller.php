<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\Traits\SessionHelper;

class Form7Controller extends Controller
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

        return view('form7', [
            'projectId' => $sessionData['project_id'],
            'preloadIndicators' => [], // ไม่แสดงข้อมูลเก่าสำหรับ showForm
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
            'indicators' => 'required|json'
        ], [
            'indicators.required' => 'กรุณากรอกข้อมูลตัวชี้วัดอย่างน้อย 1 รายการ',
            'indicators.json' => 'รูปแบบข้อมูลตัวชี้วัดไม่ถูกต้อง'
        ]);

        $indicators = json_decode($request->input('indicators'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($indicators)) {
            Log::error('❌ Form7 Store - JSON decode error', [
                'error' => json_last_error_msg(),
                'project_id' => $sessionData['project_id']
            ]);
            return back()->withErrors(['indicators' => 'รูปแบบข้อมูลตัวชี้วัดไม่ถูกต้อง'])->withInput();
        }

        // ตรวจสอบว่ามีข้อมูลที่ถูกต้องหรือไม่
        $hasValidIndicators = collect($indicators)->contains(function ($indicator) {
            return !empty(trim($indicator['detail'] ?? ''));
        });

        if (!$hasValidIndicators) {
            return back()->withErrors(['indicators' => 'กรุณากรอกข้อมูลตัวชี้วัดอย่างน้อย 1 รายการ'])->withInput();
        }

        Log::info('📥 Form7 store - เริ่ม insert ข้อมูล tindex', [
            'project_id' => $sessionData['project_id'],
            'edit_id' => $sessionData['edit_id'],
            'indicators_count' => count($indicators)
        ]);

        try {
            DB::beginTransaction();

            // ลบข้อมูลเก่า
            DB::table('tindex')->where('project_id', $sessionData['project_id'])->delete();

            // เพิ่มข้อมูลใหม่
            $inserted = 0;
            foreach ($indicators as $indicator) {
                $detail = trim($indicator['detail'] ?? '');

                if (!empty($detail)) {
                    DB::table('tindex')->insert([
                        'project_id' => $sessionData['project_id'],
                        'index_id' => trim($indicator['index_id'] ?? ''),
                        'detail' => $detail,
                        'index_value' => trim($indicator['index_value'] ?? ''),
                        'rec_date' => Carbon::now(),
                        'edit_id' => $sessionData['edit_id'],
                    ]);
                    $inserted++;
                }
            }

            DB::commit();

            Log::info('✅ Form7 store success - STORE tindex successful', [
                'project_id' => $sessionData['project_id'],
                'edit_id' => $sessionData['edit_id'],
                'inserted_indicators' => $inserted
            ]);

            return redirect()->route('form8.show', ['id' => $sessionData['project_id']])
                ->with('success', 'บันทึกข้อมูลตัวชี้วัดสำเร็จ');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Form7 Store Error - STORE tindex failed', [
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

        $plan = DB::table('tplan')->where('project_id', $id)->first();

        if (!$plan) {
            Log::error('❌ [edit] Project not found', ['project_id' => $id]);
            return redirect()->route('projects.index')->withErrors(['project' => 'ไม่พบข้อมูลโครงการ']);
        }

        $indicators = DB::table('tindex')
            ->where('project_id', $id)
            ->select('index_id', 'detail', 'index_value')
            ->get()
            ->map(fn($row) => [
                'type' => $row->index_id,
                'detail' => $row->detail,
                'target' => $row->index_value,
            ])
            ->toArray();

        return view('form7', [
            'editMode' => true,
            'projectId' => $id,
            'preloadIndicators' => $indicators,
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
            'indicators' => 'required|json'
        ], [
            'indicators.required' => 'กรุณากรอกข้อมูลตัวชี้วัดอย่างน้อย 1 รายการ',
            'indicators.json' => 'รูปแบบข้อมูลตัวชี้วัดไม่ถูกต้อง'
        ]);

        $rawInput = $request->input('indicators');

        Log::info('📥 Form7 update - START update indicators', [
            'project_id' => $id,
            'edit_id' => $sessionData['edit_id'],
            'raw_input_length' => strlen($rawInput)
        ]);

        $indicators = json_decode($rawInput, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($indicators)) {
            Log::error('❌ Form7 Update - JSON decode failed', [
                'error' => json_last_error_msg(),
                'project_id' => $id,
                'raw_input' => $rawInput
            ]);
            return back()->withErrors(['indicators' => 'รูปแบบข้อมูลตัวชี้วัดไม่ถูกต้อง'])->withInput();
        }

        // ตรวจสอบว่ามีข้อมูลที่ถูกต้องหรือไม่
        $hasValidIndicators = collect($indicators)->contains(function ($indicator) {
            return !empty(trim($indicator['detail'] ?? ''));
        });

        if (!$hasValidIndicators) {
            return back()->withErrors(['indicators' => 'กรุณากรอกข้อมูลตัวชี้วัดอย่างน้อย 1 รายการ'])->withInput();
        }

        try {
            DB::beginTransaction();

            Log::info('🗑️ Form7 update - Deleting old indicators for project', ['project_id' => $id]);
            DB::table('tindex')->where('project_id', $id)->delete();

            $inserted = 0;
            foreach ($indicators as $indicator) {
                $detail = trim($indicator['detail'] ?? '');

                if (!empty($detail)) {
                    DB::table('tindex')->insert([
                        'project_id' => $id,
                        'index_id' => trim($indicator['index_id'] ?? ''),
                        'detail' => $detail,
                        'index_value' => trim($indicator['index_value'] ?? ''),
                        'rec_date' => Carbon::now(),
                        'edit_id' => $sessionData['edit_id'],
                    ]);
                    $inserted++;
                }
            }

            DB::commit();

            Log::info('✅ Form7 update success - UPDATE tindex successful', [
                'project_id' => $id,
                'edit_id' => $sessionData['edit_id'],
                'updated_indicators' => $inserted
            ]);

            return redirect()->route('form7.edit', ['id' => $id])
                ->with('success', 'อัปเดตข้อมูลตัวชี้วัดเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Form7 Update Error - UPDATE tindex failed', [
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
