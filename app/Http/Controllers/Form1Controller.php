<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Tplan;
use Carbon\Carbon;
use App\Http\Traits\SessionHelper;

class Form1Controller extends Controller
{
    use SessionHelper;

    public function showForm()
    {
        session()->forget(['project_id', 'admin_id', 'user_id']);

        Log::info('🔄 Form1 showForm - Cleared old session for new project');

        $projectTypes = DB::table('tb_projecttype')->get();
        $jobUnits = DB::table('tjob')
            ->where('name', 'like', '%เทคโนโลยีสารสนเทศ%')
            ->get();

        $goals = DB::table('tb_goal')->get();
        $subgoals = DB::table('tb_subgoal')->get();

        return view('form1', compact('projectTypes', 'jobUnits', 'goals', 'subgoals'));
    }

    public function searchEmployee(Request $request)
    {
        $query = $request->input('q', '');

        $employees = DB::table('temployee')
            ->where('T_Work_id', '101-1400-0')
            ->when($query, function ($qBuilder) use ($query) {
                $qBuilder->where(function ($sub) use ($query) {
                    $sub->where('fname', 'like', "%{$query}%")
                        ->orWhere('lname', 'like', "%{$query}%");
                });
            })
            ->select('fname', 'lname', 'tel_o', 'email_cmu')
            ->orderBy('fname')
            ->limit(20)
            ->get()
            ->map(function ($e) {
                return [
                    'id' => "{$e->fname} {$e->lname}",
                    'text' => "{$e->fname} {$e->lname}",
                    'phone' => $e->tel_o,
                    'email' => $e->email_cmu,
                ];
            });

        return response()->json($employees->values());
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Bangkok");

        $request->validate([
            'title' => 'required|string|max:255',
            'project_type' => 'required|string',
            'job_unit_id' => 'required|string',
            'responsible_person' => 'required|string',
            'tel' => 'required|string',
            'email' => 'required|string|email',
            'year_long' => 'nullable|integer',
            'month_long' => 'nullable|integer',
            'day_long' => 'nullable|integer',
            'sdate' => 'required|date',
            'edate' => 'required|date|after:sdate',
            'subgoals' => 'required|array|min:1',
        ], [
            'title.required' => 'กรุณากรอกชื่อโครงการ',
            'project_type.required' => 'กรุณาเลือกประเภทโครงการ',
            'job_unit_id.required' => 'กรุณาเลือกหน่วยงาน',
            'responsible_person.required' => 'กรุณากรอกชื่อผู้รับผิดชอบ',
            'tel.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'sdate.required' => 'กรุณาเลือกวันที่เริ่มต้น',
            'edate.required' => 'กรุณาเลือกวันที่สิ้นสุด',
            'edate.after' => 'วันที่สิ้นสุดต้องหลังวันที่เริ่มต้น',
            'subgoals.required' => 'กรุณาเลือกเป้าหมายย่อยอย่างน้อย 1 รายการ',
            'subgoals.min' => 'กรุณาเลือกเป้าหมายย่อยอย่างน้อย 1 รายการ'
        ]);

        Log::info('📥 Form1 store - เริ่มสร้างโครงการใหม่', [
            'title' => $request->input('title'),
            'project_type' => $request->input('project_type'),
            'responsible_person' => $request->input('responsible_person')
        ]);

        try {
            DB::beginTransaction();

            $plan = Tplan::create([
                'title' => $request->input('title'),
                'project_type' => $request->input('project_type'),
                'Dep_id' => $request->input('job_unit_id'),
                'who_present' => $request->input('responsible_person'),
                'tel' => $request->input('tel'),
                'email' => $request->input('email'),
                'cojob' => $request->input('collaboration'),
                'budget_detail' => $request->input('budget_source'),
                'year_long' => $request->input('year_long') ?? 0,
                'month_long' => $request->input('month_long') ?? 0,
                'day_long' => $request->input('day_long') ?? 0,
                'add_date' => Carbon::now(),
                'sdate' => $request->input('sdate'),
                'edate' => $request->input('edate'),
                'flag' => 0,
            ]);

            $projectId = $plan->id;
            $editId = optional(Auth::user())->id ?? 1;

            session([
                'project_id' => $projectId,
                'admin_id' => $editId,
                'user_id' => $editId,
            ]);

            Log::info('✅ Form1 store - Session created', [
                'project_id' => $projectId,
                'admin_id' => $editId
            ]);

            foreach ($request->input('subgoals') as $subgoalId) {
                DB::table('tgoal')->insert([
                    'project_id' => $projectId,
                    'tgoalsub_id' => $subgoalId,
                    'rec_date' => Carbon::now(),
                    'edit_id' => $editId,
                ]);
            }

            if ($request->filled('rationale')) {
                DB::table('tb_reason')->insert([
                    'project_id' => $projectId,
                    'detail' => $request->input('rationale'),
                    'rec_date' => Carbon::now(),
                    'edit_id' => $editId,
                ]);
            }

            $objectives = json_decode($request->input('objective'), true);

            Log::info('📥 Form1 store - Processing objectives', [
                'objective_raw' => $request->input('objective'),
                'decoded' => $objectives
            ]);

            if (is_array($objectives) && count($objectives) > 0) {
                foreach ($objectives as $item) {
                    if (!empty($item['detail'])) {
                        DB::table('tobjective')->insert([
                            'project_id' => $projectId,
                            'detail' => $item['detail'],
                            'rec_date' => Carbon::now(),
                            'edit_id' => $editId,
                        ]);
                    }
                }
            }

            DB::commit();

            Log::info('✅ Form1 store success - โครงการสร้างสำเร็จ', [
                'project_id' => $projectId,
                'title' => $request->input('title')
            ]);

            return redirect()->route('form2.show')->with('success', 'บันทึกข้อมูลโครงการเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Form1 Store Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $plan = DB::table('tplan')->where('project_id', $id)->first();

        if (!$plan) {
            Log::error('❌ Form1 edit - Project not found', ['project_id' => $id]);
            return redirect()->route('dashboard')->with('error', 'ไม่พบโครงการที่เลือก');
        }

        $projectTypes = DB::table('tb_projecttype')->get();
        $jobUnits = DB::table('tjob')->where('name', 'like', '%เทคโนโลยีสารสนเทศ%')->get();
        $goals = DB::table('tb_goal')->get();
        $subgoals = DB::table('tb_subgoal')->get();

        $planSubgoals = DB::table('tgoal')
            ->where('project_id', $id)
            ->pluck('tgoalsub_id')
            ->toArray();

        $rationale = DB::table('tb_reason')
            ->where('project_id', $id)
            ->value('detail');

        $objectives = DB::table('tobjective')
            ->where('project_id', $id)
            ->select('id', 'detail')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'detail' => $item->detail
            ])
            ->values()
            ->toArray();

        $editId = Auth::check() ? Auth::user()->id : 1;

        session([
            'project_id' => $id,
            'admin_id' => $editId,
            'user_id' => $editId,
        ]);

        Log::info('✅ Form1 edit - Session created for editing', [
            'project_id' => $id,
            'admin_id' => $editId,
            'title' => $plan->title
        ]);

        $unitId = $plan->unit_id ?? null;

        return view('form1', compact(
            'plan',
            'projectTypes',
            'jobUnits',
            'goals',
            'subgoals',
            'planSubgoals',
            'rationale',
            'objectives',
            'unitId'
        ))->with('editMode', true);
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Bangkok");

        $request->validate([
            'title' => 'required|string|max:255',
            'project_type' => 'required|string',
            'job_unit_id' => 'required|string',
            'responsible_person' => 'required|string',
            'tel' => 'required|string',
            'email' => 'required|string|email',
            'collaboration' => 'nullable|string',
            'budget_source' => 'required|string',
            'year_long' => 'nullable|integer',
            'month_long' => 'nullable|integer',
            'day_long' => 'nullable|integer',
            'sdate' => 'required|date',
            'edate' => 'required|date|after:sdate',
            'subgoals' => 'required|array|min:1',
        ], [
            'title.required' => 'กรุณากรอกชื่อโครงการ',
            'project_type.required' => 'กรุณาเลือกประเภทโครงการ',
            'job_unit_id.required' => 'กรุณาเลือกหน่วยงาน',
            'responsible_person.required' => 'กรุณากรอกชื่อผู้รับผิดชอบ',
            'tel.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'budget_source.required' => 'กรุณากรอกแหล่งงบประมาณ',
            'sdate.required' => 'กรุณาเลือกวันที่เริ่มต้น',
            'edate.required' => 'กรุณาเลือกวันที่สิ้นสุด',
            'edate.after' => 'วันที่สิ้นสุดต้องหลังวันที่เริ่มต้น',
            'subgoals.required' => 'กรุณาเลือกเป้าหมายย่อยอย่างน้อย 1 รายการ',
            'subgoals.min' => 'กรุณาเลือกเป้าหมายย่อยอย่างน้อย 1 รายการ'
        ]);

        $editId = optional(Auth::user())->id ?? 1;

        Log::info('📥 Form1 update - เริ่มอัปเดตโครงการ', [
            'project_id' => $id,
            'title' => $request->input('title'),
            'edit_id' => $editId
        ]);

        try {
            DB::beginTransaction();

            DB::table('tplan')->where('project_id', $id)->update([
                'title' => $request->input('title'),
                'project_type' => $request->input('project_type'),
                'Dep_id' => $request->input('job_unit_id'),
                'who_present' => $request->input('responsible_person'),
                'tel' => $request->input('tel'),
                'email' => $request->input('email'),
                'cojob' => $request->input('collaboration'),
                'budget_detail' => $request->input('budget_source'),
                'year_long' => $request->input('year_long') ?? 0,
                'month_long' => $request->input('month_long') ?? 0,
                'day_long' => $request->input('day_long') ?? 0,
                'sdate' => $request->input('sdate'),
                'edate' => $request->input('edate'),
                'edit_date' => Carbon::now(),
            ]);

            DB::table('tgoal')->where('project_id', $id)->delete();
            foreach ($request->input('subgoals') as $subgoalId) {
                DB::table('tgoal')->insert([
                    'project_id' => $id,
                    'tgoalsub_id' => $subgoalId,
                    'rec_date' => Carbon::now(),
                    'edit_id' => $editId,
                ]);
            }

            DB::table('tb_reason')->updateOrInsert(
                ['project_id' => $id],
                [
                    'detail' => $request->input('rationale'),
                    'rec_date' => Carbon::now(),
                    'edit_id' => $editId
                ]
            );

            DB::table('tobjective')->where('project_id', $id)->delete();
            $objectives = json_decode($request->input('objective'), true);

            if (is_array($objectives)) {
                foreach ($objectives as $item) {
                    if (!empty($item['detail'])) {
                        DB::table('tobjective')->insert([
                            'project_id' => $id,
                            'detail' => $item['detail'],
                            'rec_date' => Carbon::now(),
                            'edit_id' => $editId,
                        ]);
                    }
                }
            }

            DB::commit();

            Log::info('✅ Form1 update success - อัปเดตโครงการสำเร็จ', [
                'project_id' => $id,
                'edit_id' => $editId
            ]);

            return redirect()->route('form1.edit', ['id' => $id])
                ->with('success', 'อัปเดตข้อมูลโครงการเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Form1 Update Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'project_id' => $id
            ]);
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage()])->withInput();
        }
    }
}
