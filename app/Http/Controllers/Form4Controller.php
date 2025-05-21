<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\QueryException;

class Form4Controller extends Controller
{
    public function showForm()
    {
        if (!session()->has('admin_id')) {
            session()->put('admin_id', 1);
        }

        if (!session()->has('project_id')) {
            session()->put('project_id', 1);
        }

        $projectId = session('project_id');

        return view('form4', [
            'editMode'      => false,
            'projectId'     => $projectId,
            'system_detail' => '',
            'old_workflow'  => '',
            'new_workflow'  => '',
            'whouse_users'  => [],
        ]);
    }

    public function store(Request $request)
    {
        $projectId = session('project_id');
        $editId    = session('admin_id');
        $recDate   = Carbon::now()->format('Y-m-d H:i:s');

        if (!$projectId || !$editId) {
            Log::warning('⚠️ Session หาย', [
                'project_id' => $projectId,
                'edit_id'    => $editId,
                'session_all' => session()->all()
            ]);
            return redirect()->back()->with('error', 'Session หมดอายุ กรุณาเริ่มใหม่อีกครั้ง');
        }

        $request->validate([
            'system_detail'  => 'required|string|min:3',
            'old_workflow'   => 'nullable|string',
            'new_workflow'   => 'nullable|string',
            'whouse_users'   => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // ตรวจว่ามีโครงการใน tplan
            if (!DB::table('tplan')->where('project_id', $projectId)->exists()) {
                throw new Exception("❌ project_id: {$projectId} ไม่มีในตาราง tplan");
            }

            Log::info('🧪 INSERT or UPDATE tworkflow', [
                'project_id' => $projectId,
                'workflow' => $request->input('system_detail')
            ]);

            DB::table('tworkflow')->updateOrInsert(
                ['project_id' => $projectId],
                [
                    'workflow'     => $request->input('system_detail'),
                    'old_workflow' => $request->input('old_workflow'),
                    'new_workflow' => $request->input('new_workflow'),
                    'rec_date'     => $recDate,
                    'edit_id'      => $editId,
                ]
            );

            $whouseUsers = $request->input('whouse_users', []);

            foreach ($whouseUsers as $userLabel) {
                $whouseRecord = DB::table('tb_whouse')->where('name', $userLabel)->first();
                if ($whouseRecord) {
                    DB::table('twhouse')->insert([
                        'project_id' => $projectId,
                        'whouse_id'  => $whouseRecord->id,
                        'rec_date'   => $recDate,
                        'edit_id'    => $editId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('form5.show', ['id' => $projectId])
                ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('❌ SQL ERROR', [
                'sql_message' => $e->getMessage(),
                'bindings'    => $e->getBindings(),
            ]);
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด SQL กรุณาตรวจสอบ log');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('❌ Exception', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ]);
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        session(['project_id' => $id]);

        // ดึงข้อมูลโครงการจากตาราง tplan
        $plan = DB::table('tplan')->where('project_id', $id)->first();

        // ดึงข้อมูลเวิร์กโฟลว์
        $workflow = DB::table('tworkflow')->where('project_id', $id)->first();

        // ดึงรายชื่อผู้ใช้งานระบบ (จากตารางเชื่อมโยง)
        $whouseUsers = DB::table('twhouse')
            ->join('tb_whouse', 'twhouse.whouse_id', '=', 'tb_whouse.id')
            ->where('twhouse.project_id', $id)
            ->pluck('tb_whouse.name')
            ->toArray();

        return view('form4', [
            'plan'          => $plan,
            'editMode'      => true,
            'projectId'     => $id,
            'system_detail' => $workflow?->workflow ?? '',
            'old_workflow'  => $workflow?->old_workflow ?? '',
            'new_workflow'  => $workflow?->new_workflow ?? '',
            'whouse_users'  => $whouseUsers,
        ]);
    }

    public function update(Request $request, $id)
    {
        $editId  = session('admin_id') ?? 1;
        $recDate = Carbon::now()->format('Y-m-d H:i:s');

        $request->validate([
            'system_detail'   => 'required|string|min:3',
            'old_workflow'    => 'nullable|string',
            'new_workflow'    => 'nullable|string',
            'whouse_users'    => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            DB::table('tworkflow')->updateOrInsert(
                ['project_id' => $id],
                [
                    'workflow'     => $request->input('system_detail'),
                    'old_workflow' => $request->input('old_workflow'),
                    'new_workflow' => $request->input('new_workflow'),
                    'rec_date'     => $recDate,
                    'edit_id'      => $editId,
                ]
            );

            DB::table('twhouse')->where('project_id', $id)->delete();

            foreach ($request->input('whouse_users', []) as $userLabel) {
                $whouseRecord = DB::table('tb_whouse')->where('name', $userLabel)->first();
                if ($whouseRecord) {
                    DB::table('twhouse')->insert([
                        'project_id' => $id,
                        'whouse_id'  => $whouseRecord->id,
                        'rec_date'   => $recDate,
                        'edit_id'    => $editId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('form4.edit', ['id' => $id])
                ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('❌ Form4 Update Error', ['message' => $e->getMessage()]);
            return back()->withErrors(['db' => 'เกิดข้อผิดพลาดในการอัปเดต'])->withInput();
        }
    }
}
