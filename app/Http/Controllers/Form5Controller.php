<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class Form5Controller extends Controller
{
    public function showForm()
    {
        return view('form5');
    }

    public function store(Request $request)
    {
        $projectId = session('project_id');
        $editId    = session('user_id') ?? session('admin_id') ?? 1;

        if (empty($projectId)) {
            Log::error('❌ [store] Project ID is missing');
            return redirect()->route('form1.show')->with('error', 'กรุณากรอกข้อมูลหน้าแรกก่อน');
        }

        // ✅ Log ค่าที่รับมาจาก input
        Log::info('[FORM5] 🔄 Raw goals input (store)', [
            'raw_input' => $request->input('goals')
        ]);

        $request->validate([
            'goals' => 'required|string'
        ]);

        $goals = json_decode($request->input('goals'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($goals)) {
            Log::error('❌ [store] JSON decode error: ' . json_last_error_msg());
            return back()->withErrors(['goals' => 'รูปแบบข้อมูลไม่ถูกต้อง'])->withInput();
        }

        $recDate = Carbon::now()->format('Y-m-d H:i:s');

        try {
            $inserted = 0;

            foreach ($goals as $i => $goal) {
                $detail = trim($goal['detail'] ?? '');

                if (!empty($detail)) {
                    DB::table('ttarget')->insert([
                        'project_id' => $projectId,
                        'detail'     => $detail,
                        'rec_date'   => $recDate,
                        'edit_id'    => $editId,
                    ]);
                    $inserted++;
                }
            }

            Log::info("✅ [store] Inserted {$inserted} rows into ttarget", [
                'project_id' => $projectId,
                'edit_id' => $editId
            ]);

            return redirect()->route('form6.show', ['id' => $projectId])
                ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        } catch (Exception $e) {
            Log::error('❌ [store] Exception: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการบันทึก')->withInput();
        }
    }

    public function edit($id)
    {
        session(['project_id' => $id]);

        // ดึงข้อมูลโครงการจาก tplan
        $plan = DB::table('tplan')->where('project_id', $id)->first();

        // ดึงเป้าหมายจาก ttarget
        $goals = DB::table('ttarget')
            ->where('project_id', $id)
            ->select('detail')
            ->get()
            ->map(fn($row) => ['detail' => $row->detail])
            ->toArray();

        return view('form5', [
            'plan'      => $plan,
            'editMode'  => true,
            'projectId' => $id,
            'goals'     => $goals,
        ]);
    }

    public function update(Request $request, $id)
    {
        $editId  = session('user_id') ?? session('admin_id') ?? 1;
        $recDate = Carbon::now()->format('Y-m-d H:i:s');

        // ✅ Log ค่าที่รับจาก request
        Log::info('[FORM5] 🔄 Raw goals input (update)', [
            'raw_input' => $request->input('goals')
        ]);

        $request->validate([
            'goals' => 'required|string'
        ]);

        $goals = json_decode($request->input('goals'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($goals)) {
            Log::error('❌ [update] JSON decode error: ' . json_last_error_msg());
            return back()->withErrors(['goals' => 'รูปแบบข้อมูลไม่ถูกต้อง'])->withInput();
        }

        try {
            DB::beginTransaction();

            DB::table('ttarget')->where('project_id', $id)->delete();

            $inserted = 0;
            foreach ($goals as $goal) {
                $detail = trim($goal['detail'] ?? '');

                if (!empty($detail)) {
                    DB::table('ttarget')->insert([
                        'project_id' => $id,
                        'detail'     => $detail,
                        'rec_date'   => $recDate,
                        'edit_id'    => $editId,
                    ]);
                    $inserted++;
                }
            }

            DB::commit();

            Log::info("✅ [update] Updated {$inserted} rows for project_id {$id}", [
                'edit_id' => $editId
            ]);

            return redirect()->route('form5.edit', ['id' => $id])
                ->with('success', 'อัปเดตเป้าหมายเรียบร้อยแล้ว');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('❌ [update] Exception: ' . $e->getMessage());
            return back()->withErrors(['db' => 'เกิดข้อผิดพลาดในการอัปเดต'])->withInput();
        }
    }
}
