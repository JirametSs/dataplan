<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Form3Controller extends Controller
{
    public function showForm()
    {
        // $projectId = session('project_id');

        // if (!$projectId) {
        //     return redirect()->route('form1.show')->withErrors(['ไม่พบข้อมูลโครงการ กรุณากรอกข้อมูลหน้าแรกก่อน']);
        // }
        // $existingResults = DB::table('tresult')
        //     ->where('project_id', $projectId)
        //     ->select('detail', 'unit')
        //     ->get()
        //     ->map(fn($item) => [
        //         'detail' => $item->detail,
        //         'unit'   => $item->unit,
        //     ])
        //     ->values()
        //     ->toArray();

        return view('form3', [
            // 'projectId' => $projectId,
            // 'results' => $existingResults,
            'editMode' => false
        ]);
    }

    public function store(Request $request)
    {
        $projectId = session('project_id');
        $editId    = session('admin_id');
        $recDate   = Carbon::now();

        Log::info('Form3::store - SESSION', [
            'project_id' => $projectId,
            'admin_id' => $editId,
            'results' => $request->input('results')
        ]);

        if (!$projectId || !$editId) {
            return back()->withErrors(['session' => 'Session โครงการหรือผู้ใช้ไม่ถูกต้อง'])->withInput();
        }

        $request->validate([
            'results' => 'required|string',
        ], [
            'results.required' => 'กรุณากรอกข้อมูลผลลัพธ์อย่างน้อย 1 รายการ'
        ]);

        $results = json_decode($request->input('results'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($results) || count($results) === 0) {
            return back()->withErrors(['results' => 'รูปแบบข้อมูลไม่ถูกต้อง หรือไม่มีรายการ'])->withInput();
        }

        // Check if any valid results exist
        $hasValidResults = false;
        foreach ($results as $item) {
            if (!empty($item['detail'])) {
                $hasValidResults = true;
                break;
            }
        }

        if (!$hasValidResults) {
            return back()->withErrors(['results' => 'กรุณากรอกข้อมูลผลลัพธ์อย่างน้อย 1 รายการ'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Delete existing records for this project
            DB::table('tresult')->where('project_id', $projectId)->delete();

            // Insert new records
            foreach ($results as $item) {
                if (!empty($item['detail'])) {
                    DB::table('tresult')->insert([
                        'project_id' => $projectId,
                        'detail'     => $item['detail'],
                        'unit'       => $item['unit'] ?? '',
                        'rec_date'   => $recDate,
                        'edit_id'    => $editId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('form4.show')->with('success', 'บันทึกข้อมูลผลลัพธ์เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Form3 Store Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['database' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        // เก็บ project_id ไว้ใน session เพื่อใช้ในหน้าถัดไป
        session(['project_id' => $id]);

        // ดึงข้อมูลโครงการจาก tplan
        $plan = DB::table('tplan')->where('project_id', $id)->first();

        if (!$plan) {
            return redirect()->route('projects.index')->withErrors(['project' => 'ไม่พบข้อมูลโครงการ']);
        }

        // ดึงข้อมูลผลลัพธ์ของระบบจาก tresult
        $results = DB::table('tresult')
            ->where('project_id', $id)
            ->select('detail', 'unit')
            ->get()
            ->map(fn($item) => [
                'detail' => $item->detail,
                'unit'   => $item->unit,
            ])
            ->values()
            ->toArray();

        // ส่งข้อมูลไปยัง view
        return view('form3', [
            'plan'       => $plan,
            'results'    => $results,
            'projectId'  => $id,
            'editMode'   => true,
        ]);
    }

    public function update(Request $request, $id)
    {
        $projectId = $id;
        $editId    = session('admin_id');
        $recDate   = Carbon::now();

        Log::info('Form3::update - Data', [
            'project_id' => $projectId,
            'admin_id' => $editId,
            'results' => $request->input('results')
        ]);

        if (!$editId) {
            return back()->withErrors(['session' => 'ไม่พบ session ผู้ใช้งาน'])->withInput();
        }

        $request->validate([
            'results' => 'required|string',
        ], [
            'results.required' => 'กรุณากรอกข้อมูลผลลัพธ์อย่างน้อย 1 รายการ'
        ]);

        $results = json_decode($request->input('results'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($results)) {
            return back()->withErrors(['results' => 'รูปแบบข้อมูลไม่ถูกต้อง'])->withInput();
        }

        // Check if any valid results exist
        $hasValidResults = false;
        foreach ($results as $item) {
            if (!empty($item['detail'])) {
                $hasValidResults = true;
                break;
            }
        }

        if (!$hasValidResults) {
            return back()->withErrors(['results' => 'กรุณากรอกข้อมูลผลลัพธ์อย่างน้อย 1 รายการ'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Delete existing records
            DB::table('tresult')->where('project_id', $projectId)->delete();

            // Insert new records
            foreach ($results as $row) {
                if (!empty($row['detail'])) {
                    DB::table('tresult')->insert([
                        'project_id' => $projectId,
                        'detail'     => $row['detail'],
                        'unit'       => $row['unit'] ?? '',
                        'rec_date'   => $recDate,
                        'edit_id'    => $editId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('form3.edit', ['id' => $projectId])
                ->with('success', 'บันทึกข้อมูลผลลัพธ์เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Form3 Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['database' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()])->withInput();
        }
    }
}
