<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Form7Controller extends Controller
{
    public function showForm()
    {
        return view('form7');
    }

    public function store(Request $request)
    {
        $request->validate([
            'indicators' => 'required|json'
        ]);

        $projectId = session('project_id');
        $editId = session('user_id') ?? session('admin_id') ?? 1;

        if (!$projectId) {
            return redirect()->route('form1.show')->withErrors(['project_id' => 'ไม่พบรหัสโครงการ กรุณากรอกข้อมูลหน้าแรก'])->withInput();
        }

        $indicators = json_decode($request->input('indicators'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($indicators)) {
            return back()->withErrors(['indicators' => 'รูปแบบข้อมูลตัวชี้วัดไม่ถูกต้อง'])->withInput();
        }

        try {
            DB::beginTransaction();

            foreach ($indicators as $indicator) {
                DB::table('tindex')->insert([
                    'project_id'  => $projectId,
                    'index_id'    => trim($indicator['index_id'] ?? ''),
                    'detail'      => trim($indicator['detail'] ?? ''),
                    'index_value' => trim($indicator['index_value'] ?? ''),
                    'rec_date'    => now(),
                    'edit_id'     => $editId,
                ]);
            }

            DB::commit();
            Log::info('✅ STORE tindex successful', ['project_id' => $projectId]);

            return redirect()->route('form8.edit', ['id' => $projectId])
                ->with('success', 'บันทึกข้อมูลตัวชี้วัดสำเร็จ');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ STORE tindex failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'])->withInput();
        }
    }

    public function edit($id)
    {
        session(['project_id' => $id]);

        $plan = DB::table('tplan')->where('project_id', $id)->first();

        $indicators = DB::table('tindex')
            ->where('project_id', $id)
            ->select('index_id', 'detail', 'index_value')
            ->get()
            ->map(fn($row) => [
                'type'   => $row->index_id,
                'detail' => $row->detail,
                'target' => $row->index_value,
            ])
            ->toArray();

        return view('form7', [
            'editMode'          => true,
            'projectId'         => $id,
            'preloadIndicators' => $indicators,
            'plan'              => $plan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'indicators' => 'required|json'
        ]);

        $editId = session('user_id') ?? session('admin_id') ?? 1;
        $recDate = Carbon::now();

        $rawInput = $request->input('indicators');
        Log::info('📥 START update indicators', [
            'project_id' => $id,
            'edit_id' => $editId,
            'raw_input' => $rawInput
        ]);

        $indicators = json_decode($rawInput, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($indicators)) {
            Log::error('❌ JSON decode failed on update()', [
                'error' => json_last_error_msg(),
                'raw' => $rawInput
            ]);
            return back()->withErrors(['indicators' => 'รูปแบบข้อมูลตัวชี้วัดไม่ถูกต้อง'])->withInput();
        }

        try {
            DB::beginTransaction();

            Log::info('🗑️ Deleting old indicators for project', ['project_id' => $id]);
            DB::table('tindex')->where('project_id', $id)->delete();

            foreach ($indicators as $index => $indicator) {
                DB::table('tindex')->insert([
                    'project_id'  => $id,
                    'index_id'    => trim($indicator['index_id'] ?? ''),
                    'detail'      => trim($indicator['detail'] ?? ''),
                    'index_value' => trim($indicator['index_value'] ?? ''),
                    'rec_date'    => $recDate,
                    'edit_id'     => $editId,
                ]);
            }

            DB::commit();
            Log::info('✅ UPDATE tindex successful', ['project_id' => $id]);

            return redirect()->route('form7.edit', ['id' => $id])
                ->with('success', 'อัปเดตข้อมูลตัวชี้วัดเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ UPDATE tindex failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString()
            ]);
            return back()->withErrors(['update' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล'])->withInput();
        }
    }
}
