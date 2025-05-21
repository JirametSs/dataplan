<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Form8Controller extends Controller
{
    public function showForm()
    {
        return view('form8');
    }

    public function store(Request $request)
    {
        $projectId = session('project_id');
        $editId = session('user_id') ?? 0;

        $request->validate([
            'estimations' => 'required|string',
        ]);

        $estimations = json_decode($request->input('estimations'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($estimations)) {
            return back()->withErrors(['estimations' => 'รูปแบบข้อมูลไม่ถูกต้อง'])->withInput();
        }

        DB::table('testimate')->where('project_id', $projectId)->delete();

        foreach ($estimations as $estimate) {
            $detail = $estimate['detail'] ?? '';

            foreach (['2567', '2568', '2569'] as $year) {
                $amount = $estimate["y{$year}"] ?? 0;
                DB::table('testimate')->insert([
                    'project_id' => $projectId,
                    'detail'     => $detail,
                    'year'       => $year,
                    'amount'     => $amount,
                    'rec_date'   => now(),
                    'edit_id'    => $editId,
                ]);
            }
        }

        return redirect()->route('form9.show')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        session(['project_id' => $id]);

        $plan = DB::table('tplan')->where('project_id', $id)->first();

        $records = DB::table('testimate')
            ->where('project_id', $id)
            ->orderBy('detail')
            ->get();

        $grouped = $records->groupBy('detail')->map(function ($group) {
            $row = ['detail' => $group[0]->detail];
            foreach (['2567', '2568', '2569'] as $year) {
                $entry = $group->firstWhere('year', $year);
                $row["y{$year}"] = $entry->amount ?? 0;
            }
            return $row;
        })->values()->toArray();

        Log::info('✅ preloadIncome ส่งเข้า Blade', ['data' => $grouped]);

        return view('form8', [
            'editMode' => true,
            'projectId' => $id,
            'preloadIncome' => $grouped,
            'plan' => $plan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $editId = session('user_id') ?? 0;

        $estimations = json_decode($request->input('estimations'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($estimations)) {
            return back()->withErrors(['estimations' => 'รูปแบบข้อมูลไม่ถูกต้อง'])->withInput();
        }

        try {
            DB::beginTransaction();

            DB::table('testimate')->where('project_id', $id)->delete();

            foreach ($estimations as $estimate) {
                $detail = $estimate['detail'] ?? '';

                foreach (['2567', '2568', '2569'] as $year) {
                    $amount = $estimate["y{$year}"] ?? null;

                    if (!is_null($amount) && $amount !== '') {
                        DB::table('testimate')->insert([
                            'project_id' => $id,
                            'detail'     => $detail,
                            'year'       => $year,
                            'amount'     => $amount,
                            'rec_date'   => now(),
                            'edit_id'    => $editId,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('form8.edit', ['id' => $id])
                ->with('success', 'อัปเดตข้อมูลประมาณการเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ UPDATE testimate failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);
            return back()->withErrors(['update' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล'])->withInput();
        }
    }
}
