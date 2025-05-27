<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\SessionHelper;

class Form8Controller extends Controller
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

        return view('form8', [
            'projectId' => $sessionData['project_id'],
            'preloadIncome' => [], // ไม่แสดงข้อมูลเก่าสำหรับ showForm
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
            'estimations' => 'required|string',
        ], [
            'estimations.required' => 'กรุณากรอกข้อมูลประมาณการรายได้อย่างน้อย 1 รายการ',
            'estimations.string' => 'รูปแบบข้อมูลประมาณการรายได้ไม่ถูกต้อง'
        ]);

        $estimations = json_decode($request->input('estimations'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($estimations)) {
            Log::error('❌ Form8 Store - JSON decode error', [
                'error' => json_last_error_msg(),
                'project_id' => $sessionData['project_id']
            ]);
            return back()->withErrors(['estimations' => 'รูปแบบข้อมูลไม่ถูกต้อง กรุณาตรวจสอบข้อมูลที่กรอก'])->withInput();
        }

        // ตรวจสอบว่ามีข้อมูลที่ถูกต้องหรือไม่
        $hasValidEstimations = collect($estimations)->contains(function ($estimate) {
            return !empty(trim($estimate['detail'] ?? ''));
        });

        if (!$hasValidEstimations) {
            return back()->withErrors(['estimations' => 'กรุณากรอกข้อมูลประมาณการรายได้อย่างน้อย 1 รายการ'])->withInput();
        }

        Log::info('📥 Form8 store - เริ่ม insert ข้อมูล testimate', [
            'project_id' => $sessionData['project_id'],
            'edit_id' => $sessionData['edit_id'],
            'estimations_count' => count($estimations)
        ]);

        try {
            DB::beginTransaction();

            // ลบข้อมูลเก่า
            DB::table('testimate')->where('project_id', $sessionData['project_id'])->delete();

            // เพิ่มข้อมูลใหม่
            $inserted = 0;
            foreach ($estimations as $estimate) {
                $detail = trim($estimate['detail'] ?? '');

                if (!empty($detail)) {
                    foreach (['2567', '2568', '2569'] as $year) {
                        $amount = $estimate["y{$year}"] ?? 0;

                        DB::table('testimate')->insert([
                            'project_id' => $sessionData['project_id'],
                            'detail' => $detail,
                            'year' => $year,
                            'amount' => $amount,
                            'rec_date' => Carbon::now(),
                            'edit_id' => $sessionData['edit_id'],
                        ]);
                        $inserted++;
                    }
                }
            }

            DB::commit();

            Log::info('✅ Form8 store success - บันทึกข้อมูลประมาณการเรียบร้อย', [
                'project_id' => $sessionData['project_id'],
                'edit_id' => $sessionData['edit_id'],
                'inserted_records' => $inserted
            ]);

            return redirect()->route('form9.show')->with('success', 'บันทึกข้อมูลประมาณการรายได้เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Form8 Store Error', [
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

        $records = DB::table('testimate')
            ->where('project_id', $id)
            ->orderBy('detail')
            ->get();

        $grouped = $records->groupBy('detail')->map(function ($group) {
            $row = ['detail' => $group[0]->detail];
            foreach (['2567', '2568', '2569'] as $year) {
                $entry = $group->firstWhere('year', $year);
                $row["y{$year}"] = $entry?->amount ?? 0;
            }
            return $row;
        })->values()->toArray();

        Log::info('✅ Form8 edit - preloadIncome ส่งเข้า Blade', [
            'project_id' => $id,
            'data_count' => count($grouped)
        ]);

        return view('form8', [
            'editMode' => true,
            'projectId' => $id,
            'preloadIncome' => $grouped,
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
            'estimations' => 'required|string',
        ], [
            'estimations.required' => 'กรุณากรอกข้อมูลประมาณการรายได้อย่างน้อย 1 รายการ',
            'estimations.string' => 'รูปแบบข้อมูลประมาณการรายได้ไม่ถูกต้อง'
        ]);

        $estimations = json_decode($request->input('estimations'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($estimations)) {
            Log::error('❌ Form8 Update - JSON decode error', [
                'error' => json_last_error_msg(),
                'project_id' => $id
            ]);
            return back()->withErrors(['estimations' => 'รูปแบบข้อมูลไม่ถูกต้อง กรุณาตรวจสอบข้อมูลที่กรอก'])->withInput();
        }

        Log::info('📥 Form8 update - เริ่ม update ข้อมูล testimate', [
            'project_id' => $id,
            'edit_id' => $sessionData['edit_id'],
            'estimations_count' => count($estimations)
        ]);

        try {
            DB::beginTransaction();

            // ลบข้อมูลเก่า
            DB::table('testimate')->where('project_id', $id)->delete();

            // เพิ่มข้อมูลใหม่
            $inserted = 0;
            foreach ($estimations as $estimate) {
                $detail = trim($estimate['detail'] ?? '');

                if (!empty($detail)) {
                    foreach (['2567', '2568', '2569'] as $year) {
                        $amount = $estimate["y{$year}"] ?? null;

                        // เพิ่มข้อมูลแม้ว่า amount จะเป็น 0 หรือว่าง
                        DB::table('testimate')->insert([
                            'project_id' => $id,
                            'detail' => $detail,
                            'year' => $year,
                            'amount' => $amount ?? 0,
                            'rec_date' => Carbon::now(),
                            'edit_id' => $sessionData['edit_id'],
                        ]);
                        $inserted++;
                    }
                }
            }

            DB::commit();

            Log::info('✅ Form8 update success - อัปเดตข้อมูลประมาณการเรียบร้อย', [
                'project_id' => $id,
                'edit_id' => $sessionData['edit_id'],
                'updated_records' => $inserted
            ]);

            return redirect()->route('form8.edit', ['id' => $id])
                ->with('success', 'อัปเดตข้อมูลประมาณการรายได้เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Form8 Update Error - UPDATE testimate failed', [
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
