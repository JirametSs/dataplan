<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\Traits\SessionHelper;

class Form3Controller extends Controller
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

        return view('form3', [
            'projectId' => $sessionData['project_id'],
            'results' => [], // ไม่แสดงข้อมูลเก่าสำหรับ showForm
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
            'results' => 'required|string',
        ], [
            'results.required' => 'กรุณากรอกข้อมูลผลลัพธ์อย่างน้อย 1 รายการ'
        ]);

        $results = json_decode($request->input('results'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($results) || count($results) === 0) {
            return back()->withErrors(['results' => 'รูปแบบข้อมูลไม่ถูกต้อง หรือไม่มีรายการ'])->withInput();
        }

        // ตรวจสอบว่ามีข้อมูลที่ถูกต้องหรือไม่
        $hasValidResults = collect($results)->contains(function ($item) {
            return !empty($item['detail']);
        });

        if (!$hasValidResults) {
            return back()->withErrors(['results' => 'กรุณากรอกข้อมูลผลลัพธ์อย่างน้อย 1 รายการ'])->withInput();
        }

        try {
            DB::beginTransaction();

            // ลบข้อมูลเก่า
            DB::table('tresult')->where('project_id', $sessionData['project_id'])->delete();

            // เพิ่มข้อมูลใหม่
            foreach ($results as $item) {
                if (!empty($item['detail'])) {
                    DB::table('tresult')->insert([
                        'project_id' => $sessionData['project_id'],
                        'detail' => $item['detail'],
                        'unit' => $item['unit'] ?? '',
                        'rec_date' => Carbon::now(),
                        'edit_id' => $sessionData['edit_id'],
                    ]);
                }
            }

            DB::commit();

            Log::info('✅ Form3 store success', [
                'project_id' => $sessionData['project_id'],
                'edit_id' => $sessionData['edit_id'],
                'inserted_results' => count($results)
            ]);

            return redirect()->route('form4.show')->with('success', 'บันทึกข้อมูลผลลัพธ์เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Form3 Store Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_data' => $sessionData
            ]);
            return back()->withErrors(['database' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        // ใช้ SessionHelper trait สำหรับ edit mode
        $this->initializeSession($id);
        $sessionData = $this->getSessionData();
        $this->debugSession('edit');

        // ดึงข้อมูลโครงการจาก tplan
        $plan = DB::table('tplan')->where('project_id', $id)->first();

        if (!$plan) {
            Log::error('❌ [edit] Project not found', ['project_id' => $id]);
            return redirect()->route('projects.index')->withErrors(['project' => 'ไม่พบข้อมูลโครงการ']);
        }

        // ดึงข้อมูลผลลัพธ์ของระบบจาก tresult
        $results = DB::table('tresult')
            ->where('project_id', $id)
            ->select('detail', 'unit')
            ->get()
            ->map(fn($item) => [
                'detail' => $item->detail,
                'unit' => $item->unit,
            ])
            ->values()
            ->toArray();

        return view('form3', [
            'plan' => $plan,
            'results' => $results,
            'projectId' => $id,
            'editMode' => true,
        ]);
    }

    public function update(Request $request, $id)
    {
        // ใช้ SessionHelper trait สำหรับ update
        $this->initializeSession($id);
        $sessionData = $this->getSessionData();
        $this->debugSession('update');

        $request->validate([
            'results' => 'required|string',
        ], [
            'results.required' => 'กรุณากรอกข้อมูลผลลัพธ์อย่างน้อย 1 รายการ'
        ]);

        $results = json_decode($request->input('results'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($results)) {
            return back()->withErrors(['results' => 'รูปแบบข้อมูลไม่ถูกต้อง'])->withInput();
        }

        // ตรวจสอบว่ามีข้อมูลที่ถูกต้องหรือไม่
        $hasValidResults = collect($results)->contains(function ($item) {
            return !empty($item['detail']);
        });

        if (!$hasValidResults) {
            return back()->withErrors(['results' => 'กรุณากรอกข้อมูลผลลัพธ์อย่างน้อย 1 รายการ'])->withInput();
        }

        try {
            DB::beginTransaction();

            // ลบข้อมูลเก่า
            DB::table('tresult')->where('project_id', $id)->delete();

            // เพิ่มข้อมูลใหม่
            foreach ($results as $row) {
                if (!empty($row['detail'])) {
                    DB::table('tresult')->insert([
                        'project_id' => $id,
                        'detail' => $row['detail'],
                        'unit' => $row['unit'] ?? '',
                        'rec_date' => Carbon::now(),
                        'edit_id' => $sessionData['edit_id'],
                    ]);
                }
            }

            DB::commit();

            Log::info('✅ Form3 update success', [
                'project_id' => $id,
                'edit_id' => $sessionData['edit_id'],
                'updated_results' => count($results)
            ]);

            return redirect()->route('form3.edit', ['id' => $id])
                ->with('success', 'บันทึกข้อมูลผลลัพธ์เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Form3 Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'project_id' => $id,
                'session_data' => $sessionData
            ]);
            return back()->withErrors(['database' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()])->withInput();
        }
    }
}
