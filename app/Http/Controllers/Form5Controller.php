<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use App\Http\Traits\SessionHelper;

class Form5Controller extends Controller
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

        return view('form5', [
            'projectId' => $sessionData['project_id'],
            'goals' => [], // ไม่แสดงข้อมูลเก่าสำหรับ showForm
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

        Log::info('[FORM5] 🔄 Raw goals input (store)', [
            'raw_input' => $request->input('goals'),
            'all_inputs' => $request->all(),
            'project_id' => $sessionData['project_id'],
            'edit_id' => $sessionData['edit_id']
        ]);

        $request->validate([
            'goals' => 'nullable|json'
        ], [
            'goals.json' => 'รูปแบบข้อมูลเป้าหมายไม่ถูกต้อง'
        ]);

        $goals = json_decode($request->input('goals') ?? '[]', true);

        if (!is_array($goals)) {
            return back()->withErrors(['goals' => 'ข้อมูลไม่ถูกต้อง กรุณาตรวจสอบรูปแบบข้อมูล'])->withInput();
        }

        // ตรวจสอบว่ามีข้อมูลอย่างน้อย 1 รายการ
        $validGoals = collect($goals)->filter(function ($goal) {
            return !empty(trim($goal['detail'] ?? ''));
        })->values()->toArray();

        if (empty($validGoals)) {
            return back()->withErrors(['goals' => 'กรุณากรอกข้อมูลเป้าหมายอย่างน้อย 1 รายการ'])->withInput();
        }

        Log::info('📥 Form5 store - เริ่ม insert ข้อมูล ttarget', [
            'project_id' => $sessionData['project_id'],
            'edit_id' => $sessionData['edit_id'],
            'goals_count' => count($validGoals)
        ]);

        try {
            DB::beginTransaction();

            // ลบข้อมูลเก่า
            DB::table('ttarget')->where('project_id', $sessionData['project_id'])->delete();

            $inserted = 0;
            foreach ($validGoals as $goal) {
                $detail = trim($goal['detail'] ?? '');
                if (!empty($detail)) {
                    DB::table('ttarget')->insert([
                        'project_id' => $sessionData['project_id'],
                        'detail' => $detail,
                        'rec_date' => Carbon::now(),
                        'edit_id' => $sessionData['edit_id'],
                    ]);
                    $inserted++;
                }
            }

            DB::commit();

            Log::info("✅ Form5 store success - บันทึกข้อมูลทั้งหมดสำเร็จ", [
                'project_id' => $sessionData['project_id'],
                'edit_id' => $sessionData['edit_id'],
                'inserted_rows' => $inserted
            ]);

            return redirect()->route('form6.show', ['id' => $sessionData['project_id']])
                ->with('success', 'บันทึกข้อมูลเป้าหมายเรียบร้อยแล้ว');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('❌ Form5 Store Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'session_data' => $sessionData
            ]);
            return back()->with('error', 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage())->withInput();
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

        // ดึงเป้าหมายจาก ttarget
        $goals = DB::table('ttarget')
            ->where('project_id', $id)
            ->select('detail')
            ->get()
            ->map(fn($row) => ['detail' => $row->detail])
            ->toArray();

        return view('form5', [
            'plan' => $plan,
            'editMode' => true,
            'projectId' => $id,
            'goals' => $goals,
        ]);
    }

    public function update(Request $request, $id)
    {
        // ใช้ SessionHelper trait สำหรับ update
        $this->initializeSession($id);
        $sessionData = $this->getSessionData();
        $this->debugSession('update');

        Log::info('[FORM5] 🔄 Raw goals input (update)', [
            'raw_input' => $request->input('goals'),
            'all_inputs' => $request->all(),
            'project_id' => $id,
            'edit_id' => $sessionData['edit_id']
        ]);

        $request->validate([
            'goals' => 'nullable|json'
        ], [
            'goals.json' => 'รูปแบบข้อมูลเป้าหมายไม่ถูกต้อง'
        ]);

        $goals = json_decode($request->input('goals') ?? '[]', true);

        if (!is_array($goals)) {
            return back()->withErrors(['goals' => 'ข้อมูล JSON ไม่ถูกต้อง กรุณาตรวจสอบรูปแบบข้อมูล'])->withInput();
        }

        // กรองข้อมูลที่ถูกต้อง
        $validGoals = collect($goals)->filter(function ($goal) {
            return !empty(trim($goal['detail'] ?? ''));
        })->values()->toArray();

        Log::info('📥 Form5 update - เริ่ม update ข้อมูล ttarget', [
            'project_id' => $id,
            'edit_id' => $sessionData['edit_id'],
            'goals_count' => count($validGoals)
        ]);

        try {
            DB::beginTransaction();

            // ดึงข้อมูลเป้าหมายที่มีอยู่แล้ว
            $existingGoals = DB::table('ttarget')
                ->where('project_id', $id)
                ->pluck('detail')
                ->toArray();

            $newInserted = 0;
            $duplicates = 0;

            foreach ($validGoals as $goal) {
                $detail = trim($goal['detail'] ?? '');
                if (!empty($detail)) {
                    // ตรวจสอบว่าข้อมูลนี้มีอยู่แล้วหรือไม่
                    if (!in_array($detail, $existingGoals)) {
                        // เป็นข้อมูลใหม่ - เพิ่มเข้าไป
                        DB::table('ttarget')->insert([
                            'project_id' => $id,
                            'detail' => $detail,
                            'rec_date' => Carbon::now(),
                            'edit_id' => $sessionData['edit_id'],
                        ]);
                        $newInserted++;

                        Log::info('[FORM5] ➕ Added new goal', [
                            'project_id' => $id,
                            'detail' => $detail
                        ]);
                    } else {
                        // ข้อมูลซ้ำ - ข้าม
                        $duplicates++;

                        Log::info('[FORM5] ⚠️ Duplicate goal skipped', [
                            'project_id' => $id,
                            'detail' => $detail
                        ]);
                    }
                }
            }

            DB::commit();

            // สร้างข้อความแจ้งผลลัพธ์
            $message = '';
            if ($newInserted > 0 && $duplicates > 0) {
                $message = "เพิ่มเป้าหมายใหม่ {$newInserted} รายการ (ข้าม {$duplicates} รายการที่ซ้ำ)";
            } elseif ($newInserted > 0) {
                $message = "เพิ่มเป้าหมายใหม่ {$newInserted} รายการเรียบร้อยแล้ว";
            } elseif ($duplicates > 0) {
                $message = "ข้อมูลทั้งหมดมีอยู่แล้ว (ข้าม {$duplicates} รายการที่ซ้ำ)";
            } else {
                $message = "ไม่มีข้อมูลใหม่ที่ถูกต้องสำหรับการเพิ่ม";
            }

            Log::info("✅ Form5 update success - อัปเดตข้อมูลทั้งหมดสำเร็จ", [
                'project_id' => $id,
                'edit_id' => $sessionData['edit_id'],
                'new_inserted' => $newInserted,
                'duplicates' => $duplicates
            ]);

            return redirect()->route('form5.edit', ['id' => $id])
                ->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('❌ Form5 Update Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'project_id' => $id,
                'session_data' => $sessionData
            ]);
            return back()->with('error', 'เกิดข้อผิดพลาดในการอัปเดต: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * ลบเป้าหมายเฉพาะรายการ
     */
    public function deleteGoal(Request $request, $id)
    {
        $this->initializeSession($id);
        $sessionData = $this->getSessionData();

        $request->validate([
            'detail' => 'required|string'
        ]);

        try {
            $deleted = DB::table('ttarget')
                ->where('project_id', $id)
                ->where('detail', $request->input('detail'))
                ->delete();

            if ($deleted > 0) {
                Log::info('[FORM5] 🗑️ Goal deleted', [
                    'project_id' => $id,
                    'detail' => $request->input('detail')
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'ลบเป้าหมายเรียบร้อยแล้ว'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบเป้าหมายที่ต้องการลบ'
                ], 404);
            }
        } catch (Exception $e) {
            Log::error('❌ Form5 Delete Goal Exception', [
                'message' => $e->getMessage(),
                'project_id' => $id,
                'detail' => $request->input('detail')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Route สำหรับ debug (เพิ่มใน web.php ถ้าต้องการ)
     */
    public function debug(Request $request, $id)
    {
        return response()->json([
            'request_all' => $request->all(),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_content' => $request->getContent(),
            'project_id' => $id
        ]);
    }
}
