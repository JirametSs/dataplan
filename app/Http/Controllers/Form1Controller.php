<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Tplan;
use Carbon\Carbon;

class Form1Controller extends Controller
{
    public function showForm()
    {
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
                    'id'    => "{$e->fname} {$e->lname}",
                    'text'  => "{$e->fname} {$e->lname}",
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
            'title'              => 'required|string|max:255',
            'project_type'       => 'required|string',
            'job_unit_id'        => 'required|string',
            'responsible_person' => 'required|string',
            'tel'                => 'required|string',
            'email'              => 'required|string',
            'year_long'          => 'nullable|integer',
            'month_long'         => 'nullable|integer',
            'day_long'           => 'nullable|integer',
            'sdate'              => 'required|date',
            'edate'              => 'required|date',
            'subgoals'           => 'required|array|min:1',
        ]);

        $plan = Tplan::create([
            'title'          => $request->input('title'),
            'project_type'   => $request->input('project_type'),
            'Dep_id'         => $request->input('job_unit_id'),
            'who_present'    => $request->input('responsible_person'),
            'tel'            => $request->input('tel'),
            'email'          => $request->input('email'),
            'cojob'          => $request->input('collaboration'),
            'budget_detail'  => $request->input('budget_source'),
            'year_long'      => $request->input('year_long') ?? 0,
            'month_long'     => $request->input('month_long') ?? 0,
            'day_long'       => $request->input('day_long') ?? 0,
            'add_date'       => date('Y-m-d H:i:s'),
            'sdate'          => $request->input('sdate'),
            'edate'          => $request->input('edate'),
            'flag'           => 0,
        ]);

        $projectId = $plan->id;
        $editId = optional(Auth::user())->id ?? 1;

        session([
            'project_id' => $projectId,
            'admin_id'   => $editId,
        ]);

        foreach ($request->input('subgoals') as $subgoalId) {
            DB::table('tgoal')->insert([
                'project_id'   => $projectId,
                'tgoalsub_id'  => $subgoalId,
                'rec_date'     => Carbon::now(),
                'edit_id'      => $editId,
            ]);
        }

        if ($request->filled('rationale')) {
            DB::table('tb_reason')->insert([
                'project_id' => $projectId,
                'detail'     => $request->input('rationale'),
                'rec_date'   => Carbon::now(),
                'edit_id'    => $editId,
            ]);
        }

        $objectives = json_decode($request->input('objective'), true);
        Log::info('📥 Objective Raw', ['objective' => $request->input('objective')]);
        Log::info('🛠 INSERT OBJECTIVE', ['decoded' => $objectives]);

        if (is_array($objectives) && count($objectives) > 0) {
            foreach ($objectives as $item) {
                if (!empty($item['detail'])) {
                    DB::table('tobjective')->insert([
                        'project_id' => $projectId,
                        'detail'     => $item['detail'],
                        'rec_date'   => Carbon::now(),
                        'edit_id'    => $editId,
                    ]);
                }
            }
        }

        return redirect()->route('form2.show')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $plan = DB::table('tplan')->where('project_id', $id)->first();

        if (!$plan) {
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
                'id'     => $item->id,
                'detail' => $item->detail
            ])
            ->values()
            ->toArray();

        session([
            'project_id' => $id,
            'admin_id'   => Auth::check() ? Auth::user()->id : 1
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
            'title'              => 'required|string|max:255',
            'project_type'       => 'required|string',
            'job_unit_id'        => 'required|string',
            'responsible_person' => 'required|string',
            'tel'                => 'required|string',
            'email'              => 'required|string',
            'collaboration'      => 'nullable|string',
            'budget_source'      => 'required|string',
            'year_long'          => 'nullable|integer',
            'month_long'         => 'nullable|integer',
            'day_long'           => 'nullable|integer',
            'sdate'              => 'required|date',
            'edate'              => 'required|date',
            'subgoals'           => 'required|array|min:1',
        ]);

        $editId = optional(Auth::user())->id ?? 1;

        DB::table('tplan')->where('project_id', $id)->update([
            'title'          => $request->input('title'),
            'project_type'   => $request->input('project_type'),
            'Dep_id'         => $request->input('job_unit_id'),
            'who_present'    => $request->input('responsible_person'),
            'tel'            => $request->input('tel'),
            'email'          => $request->input('email'),
            'cojob'          => $request->input('collaboration'),
            'budget_detail'  => $request->input('budget_source'),
            'year_long'      => $request->input('year_long') ?? 0,
            'month_long'     => $request->input('month_long') ?? 0,
            'day_long'       => $request->input('day_long') ?? 0,
            'sdate'          => $request->input('sdate'),
            'edate'          => $request->input('edate'),
            'edit_date'      => now(),
        ]);

        DB::table('tgoal')->where('project_id', $id)->delete();
        foreach ($request->input('subgoals') as $subgoalId) {
            DB::table('tgoal')->insert([
                'project_id'  => $id,
                'tgoalsub_id' => $subgoalId,
                'rec_date'    => now(),
                'edit_id'     => $editId,
            ]);
        }

        DB::table('tb_reason')->updateOrInsert(
            ['project_id' => $id],
            ['detail' => $request->input('rationale'), 'rec_date' => now(), 'edit_id' => $editId]
        );

        DB::table('tobjective')->where('project_id', $id)->delete();
        $objectives = json_decode($request->input('objective'), true);
        if (is_array($objectives)) {
            foreach ($objectives as $item) {
                if (!empty($item['detail'])) {
                    DB::table('tobjective')->insert([
                        'project_id' => $id,
                        'detail'     => $item['detail'],
                        'rec_date'   => now(),
                        'edit_id'    => $editId,
                    ]);
                }
            }
        }

        return redirect()->route('form1.edit', ['id' => $id])->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
    }
}
