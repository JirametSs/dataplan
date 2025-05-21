<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Options;

class DashboardController extends Controller
{
    public function showForm(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $year   = $request->input('year');
        $users = DB::table('users')->simplePaginate(15);

        $query = DB::table('tplan')
            ->leftJoin('tb_projecttype', 'tplan.project_type', '=', 'tb_projecttype.id')
            ->select(
                'tplan.project_id',
                'tplan.title',
                'tplan.sdate',
                'tplan.edate',
                'tplan.flag',
                'tb_projecttype.name as project_type',
                DB::raw("YEAR(tplan.sdate) as year"),
                DB::raw("CONCAT(tplan.project_id, '-1') as project_number"),
                DB::raw("COALESCE(NULLIF(tplan.approved_result, ''), 'ส่งงานนโยบายและแผน') as owner"),
                DB::raw("DATE_FORMAT(tplan.edate, '%Y-%m-%d') as last_submit_date")
            )
            ->where('tplan.flag', 0);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tplan.title', 'like', '%' . $search . '%')
                    ->orWhere('tplan.project_id', 'like', '%' . $search . '%');
            });
        }

        if ($year) {
            $query->whereYear('tplan.sdate', $year - 543);
        }

        $availableYears = DB::table('tplan')
            ->select(DB::raw('YEAR(sdate) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->map(fn($year) => $year + 543);

        $projects = $query->orderByDesc('tplan.project_id')
            ->get()
            ->map(function ($project) {
                $project->sdate = $project->sdate ? Carbon::parse($project->sdate)->addYears(543)->format('d/m/Y') : null;
                $project->edate = $project->edate ? Carbon::parse($project->edate)->addYears(543)->format('d/m/Y') : null;
                return $project;
            });

        return view('dashboard', compact('projects', 'availableYears'));
    }

    public function edit($id)
    {
        $project = DB::table('tplan')
            ->where('project_id', $id)
            ->first();

        if (!$project) {
            abort(404, 'Project not found');
        }

        $project->sdate = $project->sdate ? Carbon::parse($project->sdate)->format('Y-m-d') : null;
        $project->edate = $project->edate ? Carbon::parse($project->edate)->format('Y-m-d') : null;

        $departments = DB::table('departments')->orderBy('name')->get();
        $projectTypes = DB::table('project_types')->orderBy('name')->get();

        return view('projects.edit', compact('project', 'departments', 'projectTypes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sdate' => 'required|date',
            'edate' => 'required|date|after_or_equal:sdate',
            'type_id' => 'required|exists:project_types,id',
            'department_id' => 'required|exists:departments,id',
            'budget' => 'nullable|numeric',
            'flag' => 'required|in:0,1,2,9'
        ]);

        $validated['sdate'] = Carbon::parse($validated['sdate'])->format('Y-m-d');
        $validated['edate'] = Carbon::parse($validated['edate'])->format('Y-m-d');

        try {
            DB::table('tplan')
                ->where('project_id', $id)
                ->update($validated);

            return redirect()->route('dashboard')
                ->with('success', 'โครงการได้รับการอัปเดตเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการอัปเดตโครงการ: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        date_default_timezone_set("Asia/Bangkok");

        try {
            DB::table('tplan')
                ->where('project_id', $id)
                ->update([
                    'flag' => 9,
                    'cancel_date' => Carbon::now()
                ]);

            return redirect()->route('dashboard')
                ->with('success', 'ลบรายการเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาดในการลบ: ' . $e->getMessage());
        }
    }

    public function exportFullPdf($id)
    {
        $form1 = DB::table('tplan')
            ->leftJoin('temployee', 'tplan.Dep_id', '=', 'temployee.T_Work_id')
            ->leftJoin('tb_period', 'tplan.period_time', '=', 'tb_period.id')
            ->select(
                'tplan.*',
                'temployee.T_Work_name as t_work_name',
                'tb_period.name as period_name'
            )
            ->where('tplan.project_id', $id)
            ->first();
        $goals = DB::table('tb_goal')->orderBy('id')->get();
        $subgoals = DB::table('tb_subgoal')->orderBy('goal_id')->get();
        $form2         = DB::table('temployee')->where('idx', $id)->first();
        $form_goal     = DB::table('tgoal')->where('project_id', $id)->get();
        $form_reason   = DB::table('tb_reason')->where('project_id', $id)->get();
        $form_objective = DB::table('tobjective')->where('project_id', $id)->get();
        $form_okr      = DB::table('tokr')->where('project_id', $id)->get();
        $form3         = DB::table('tresult')->where('project_id', $id)->get();
        $form4         = DB::table('tworkflow')->where('project_id', $id)->get();
        $form4_whouse = DB::table('twhouse')
            ->join('tb_whouse', 'twhouse.whouse_id', '=', 'tb_whouse.id')
            ->where('twhouse.project_id', $id)
            ->select('tb_whouse.name', 'tb_whouse.id as whouse_id')
            ->get();
        $form5         = DB::table('ttarget')->where('project_id', $id)->get();
        $form6         = DB::table('tadvantage')->where('project_id', $id)->get();
        $form7         = DB::table('tindex')->where('project_id', $id)->get();
        $form8         = DB::table('testimate')->where('project_id', $id)->get();
        $form9         = DB::table('timpact')->where('project_id', $id)->get();
        $okrs     = DB::table('tb_okr')->get();
        $subokrs  = DB::table('tb_subokr')->get();
        $form_okr = DB::table('tokr')->where('project_id', $id)->get();
        $projectTypes = DB::table('tb_projecttype')->orderBy('name')->get();

        return view('forms.form_all_pdf', compact(
            'form1',
            'goals',
            'subgoals',
            'form2',
            'form_goal',
            'form_reason',
            'form_objective',
            'form_okr',
            'okrs',
            'subokrs',
            'form3',
            'form4',
            'form4_whouse',
            'form5',
            'form6',
            'form7',
            'form8',
            'form9',
            'projectTypes'
        ));
    }

    public function StreamFullPdf($id)
    {
        $form1 = DB::table('tplan')
            ->leftJoin('temployee', 'tplan.Dep_id', '=', 'temployee.T_Work_id')
            ->leftJoin('tb_period', 'tplan.period_time', '=', 'tb_period.id')
            ->select(
                'tplan.*',
                'temployee.T_Work_name as t_work_name',
                'tb_period.name as period_name'
            )
            ->where('tplan.project_id', $id)
            ->first();

        $goals         = DB::table('tb_goal')->orderBy('id')->get();
        $subgoals      = DB::table('tb_subgoal')->orderBy('goal_id')->get();
        $form2         = DB::table('temployee')->where('idx', $id)->first();
        $form_goal     = DB::table('tgoal')->where('project_id', $id)->get();
        $form_reason   = DB::table('tb_reason')->where('project_id', $id)->get();
        $form_objective = DB::table('tobjective')->where('project_id', $id)->get();
        $form_okr      = DB::table('tokr')->where('project_id', $id)->get();
        $form3         = DB::table('tresult')->where('project_id', $id)->get();
        $form4         = DB::table('tworkflow')->where('project_id', $id)->get();
        $form4_whouse  = DB::table('twhouse')
            ->join('tb_whouse', 'twhouse.whouse_id', '=', 'tb_whouse.id')
            ->where('twhouse.project_id', $id)
            ->select('tb_whouse.name', 'tb_whouse.id as whouse_id')
            ->get();
        $form5         = DB::table('ttarget')->where('project_id', $id)->get();
        $form6         = DB::table('tadvantage')->where('project_id', $id)->get();
        $form7         = DB::table('tindex')->where('project_id', $id)->get();
        $form8         = DB::table('testimate')->where('project_id', $id)->get();
        $form9         = DB::table('timpact')->where('project_id', $id)->get();
        $okrs          = DB::table('tb_okr')->get();
        $subokrs       = DB::table('tb_subokr')->get();
        $projectTypes  = DB::table('tb_projecttype')->orderBy('name')->get();

        $pdf = Pdf::loadView('forms.form_all_pdf', compact(
            'form1',
            'goals',
            'subgoals',
            'form2',
            'form_goal',
            'form_reason',
            'form_objective',
            'form_okr',
            'okrs',
            'subokrs',
            'form3',
            'form4',
            'form4_whouse',
            'form5',
            'form6',
            'form7',
            'form8',
            'form9',
            'projectTypes'
        ));

        return $pdf->stream('project_form_' . $id . '.pdf');
    }
}
