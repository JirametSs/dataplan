<!DOCTYPE html>

@extends('layouts.main')

@section('title', 'หน้าฟอร์มหก')

@section('head')

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<!-- Google Fonts : TH Sara bun -->
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />

<!-- Custom CSS -->
<link rel="stylesheet" href="{{ asset('css/custom.css') }}" />

<!-- Tailwind CSS -->
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<!-- Table Tabulator -->
<link href="https://unpkg.com/tabulator-tables@6.3.1/dist/css/tabulator.min.css" rel="stylesheet">

<!-- Table Tabulator JS-->
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.1/dist/js/tabulator.min.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Jquery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Sweetalert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


@endsection

@section('content')

@if (Request::is('form1/edit/*') || Request::is('form2/edit/*') || Request::is('form3/edit/*') ||
Request::is('form4/edit/*') || Request::is('form5/edit/*') || Request::is('form6/edit/*') ||
Request::is('form7/edit/*') || Request::is('form8/edit/*') || Request::is('form9/edit/*'))

<center>
    <button class="btn-edit-custom" style="margin-right: 300px;">แก้ไข</button>
</center>

@endif

@php
$projectId = $plan->project_id ?? null;
@endphp

@if (isset($projectId))
<div class="step-wrapper">
    <a href="/form1/edit/{{ $projectId }}" class="step {{ Request::is('form1/edit/*') ? 'active' : '' }}">โครงการ</a>
    <a href="/form2/edit/{{ $projectId }}" class="step {{ Request::is('form2/edit/*') ? 'active' : '' }}">ความสอดคล้องกับ OKR</a>
    <a href="/form3/edit/{{ $projectId }}" class="step {{ Request::is('form3/edit/*') ? 'active' : '' }}">ผลลัพธ์ของระบบงาน</a>
    <a href="/form4/edit/{{ $projectId }}" class="step {{ Request::is('form4/edit/*') ? 'active' : '' }}">รายละเอียด/แผนผังการทำงาน</a>
    <a href="/form5/edit/{{ $projectId }}" class="step {{ Request::is('form5/edit/*') ? 'active' : '' }}">เป้าหมายการดำเนินงาน</a>
    <a href="/form6/edit/{{ $projectId }}" class="step {{ Request::is('form6/edit/*') ? 'active' : '' }}">ประโยชน์ที่จะได้รับ</a>
    <a href="/form7/edit/{{ $projectId }}" class="step {{ Request::is('form7/edit/*') ? 'active' : '' }}">ตัวชี้วัด/ค่าเป้าหมาย</a>
    <a href="/form8/edit/{{ $projectId }}" class="step {{ Request::is('form8/edit/*') ? 'active' : '' }}">ประมาณการรายรับ/รายจ่าย</a>
    <a href="/form9/edit/{{ $projectId }}" class="step {{ Request::is('form9/edit/*') ? 'active' : '' }}">ผลกระทบที่คาดว่าจะได้รับ</a>
</div>
@endif

@if (Request::is('form6'))
<div class="step-wrapper">
    <a href="/form1" class="step active">โครงการ</a>
    <a href="/form2" class="step active">ความสอดคล้องกับ OKR</a>
    <a href="/form3" class="step active">ผลลัพธ์ของระบบงาน</a>
    <a href="/form4" class="step active">รายละเอียด/แผนผังการทำงาน</a>
    <a href="/form5" class="step active">เป้าหมายการดำเนินงาน</a>
    <a href="/form6" class="step active">ประโยชน์ที่จะได้รับ</a>
    <a href="/form7" class="step">ตัวชี้วัด/ค่าเป้าหมาย</a>
    <a href="/form8" class="step">ประมาณการรายรับ/รายจ่าย</a>
    <a href="/form9" class="step">ผลกระทบที่คาดว่าจะได้รับ</a>
</div>
@endif

<div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
    <div class="alert-custom">
        <i class="fa-solid fa-circle-info me-1"></i>
        <strong class="text-danger">*</strong> หลังข้อ หมายถึงให้ใส่รายละเอียดข้อมูลด้วย และกรุณาบันทึกข้อมูลด้านล่างก่อนทุกครั้งเมื่อมีการแก้ไขหรือบันทึกข้อมูลเพิ่มเติม
    </div>
</div>

@if ($errors->any())
<div class="flex justify-center mt-5">
    <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4" style="margin-right: 300px;">
        <div class="flex items-center text-red-800 font-semibold text-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            กรุณาตรวจสอบข้อมูลให้ถูกต้อง
        </div>
        <ul class="mt-2 text-red-700 pl-5 list-disc">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif


<button class="side-btn-open-minimal" type="button" onclick="btn_open()">
    <i class="fa-solid fa-bars"></i>
</button>



<div class="d-flex justify-content-center align-items-start py-5" style="min-height: 100vh;">
    <div class="container">
        <div class="card shadow">
            <center>
                <div class="card-header bg-warning text-green-800">
                    <h4 class="mb-0">ประโยชน์ที่จะได้รับ</h4>
                </div>
            </center>

            <form action="{{ isset($plan) ? route('form6.update', $plan->project_id) : route('form6.store') }}" method="POST">
                @csrf
                @if(isset($plan))
                @method('PUT')
                @endif
                <div class="card-body">

                    <input type="hidden" name="job_advantage" id="jobAdvantagesInput">
                    <input type="hidden" name="depart_advantage" id="departAdvantagesInput">
                    <input type="hidden" name="people_advantage" id="peopleAdvantagesInput">

                    {{-- ข้อ 17 --}}
                    <div class="mb-5">
                        <label class="form-label fw-semibold fs-5 d-block text-success required">
                            17. ประโยชน์ที่หน่วยงานจะได้รับจากระบบงาน/โครงการนี้
                        </label>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button id="react1-add" type="button" class="btn btn-outline-success">
                                <i class="fa-solid fa-plus me-1"></i> เพิ่ม
                            </button>

                            <button id="react1-delete" type="button" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-minus me-1"></i> ลบ
                            </button>
                        </div>

                        <div id="goal1" class="rounded p-2" style="background-color: #f6fff9;"></div>
                    </div>
                    {{-- ข้อ 18 --}}
                    <div class="mb-5">
                        <label class="form-label fw-semibold fs-5 d-block text-success required">
                            18. ประโยชน์ที่คณะฯ จะได้รับจากระบบงาน/โครงการนี้
                        </label>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button id="react2-add" type="button" class="btn btn-outline-success">
                                <i class="fa-solid fa-plus me-1"></i> เพิ่ม
                            </button>

                            <button id="react2-delete" type="button" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-minus me-1"></i> ลบ
                            </button>
                        </div>

                        <div id="goal2" class="rounded p-2" style="background-color: #f6fff9;"></div>
                    </div>
                    {{-- ข้อ 19 --}}
                    <div class="mb-5">
                        <label class="form-label fw-semibold fs-5 d-block text-success required">
                            19. ประโยชน์ที่ประชาชนจะได้รับจากระบบงาน/โครงการนี้
                        </label>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button id="react3-add" type="button" class="btn btn-outline-success">
                                <i class="fa-solid fa-plus me-1"></i> เพิ่ม
                            </button>

                            <button id="react3-delete" type="button" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-minus me-1"></i> ลบ
                            </button>
                        </div>

                        <div id="goal3" class="rounded p-2" style="background-color: #f6fff9;"></div>
                    </div>
                    {{-- ปุ่มบันทึก --}}
                    <div class="text-center">
                        <button id="submitFormBtn" type="button"
                            class="bg-green-200 hover:bg-green-300 text-green-900 font-bold py-2 px-4 border-b-4 border-green-300 hover:border-green-400 rounded-full shadow-sm d-flex align-items-center gap-2">
                            <i class="fas fa-save"></i> <span>บันทึกข้อมูล</span>
                        </button>
                        <button type="button"
                            class="bg-yellow-200 hover:bg-yellow-300 text-yellow-900 font-bold py-2 px-4 border-b-4 border-yellow-300 hover:border-yellow-400 rounded-full shadow-sm d-flex align-items-center gap-2"
                            onclick="confirmReset()">
                            <i class="fas fa-eraser"></i>
                            <span>ล้างข้อมูล</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
    window.addEventListener("DOMContentLoaded", function() {
        const preloadGoal1 = @json($goalJob ?? []);
        const preloadGoal2 = @json($goalDepart ?? []);
        const preloadGoal3 = @json($goalPeople ?? []);

        function prepareInitialData(data) {
            return Array.isArray(data) ? data : [];
        }

        function initTable(selector, data, addBtnId, delBtnId, tableKey) {
            const el = document.querySelector(selector);
            if (!el) return;

            window[tableKey] = new Tabulator(el, {
                layout: "fitColumns",
                reactiveData: true,
                height: "200px",
                data: data,
                columns: [{
                        title: "ลำดับ",
                        width: 80,
                        hozAlign: "center",
                        headerSort: false,
                        formatter: function(cell) {
                            return (cell.getRow().getPosition(true) + 0) + ".";
                        }
                    },
                    {
                        title: "รายละเอียด",
                        field: "detail",
                        editor: "input",
                        hozAlign: "left"
                    }
                ],
                rowMoved: () => window[tableKey].redraw(true),
            });

            // ปุ่มเพิ่ม
            document.getElementById(addBtnId)?.addEventListener("click", function() {
                if (window.isEditMode) {
                    Swal.fire("โหมดแก้ไข", "ไม่สามารถเพิ่มรายการได้ในโหมดแก้ไข", "info");
                    return;
                }

                window[tableKey].addRow({
                    detail: ""
                });
            });

            // ปุ่มลบ
            document.getElementById(delBtnId)?.addEventListener("click", function() {
                if (window.isEditMode) {
                    Swal.fire("โหมดแก้ไข", "ไม่สามารถลบรายการได้ในโหมดแก้ไข", "info");
                    return;
                }

                const rows = window[tableKey].getRows();
                if (rows.length > 0) {
                    rows.at(-1).delete();
                }
            });
        }

        initTable("#goal1", prepareInitialData(preloadGoal1), "react1-add", "react1-delete", "table1");
        initTable("#goal2", prepareInitialData(preloadGoal2), "react2-add", "react2-delete", "table2");
        initTable("#goal3", prepareInitialData(preloadGoal3), "react3-add", "react3-delete", "table3");

        // ปุ่มบันทึก
        document.getElementById("submitFormBtn")?.addEventListener("click", function() {
            [table1, table2, table3].forEach(table => {
                table.getEditedCells().forEach(cell => cell.cancelEdit());
            });

            const data1 = table1.getData().map(r => r.detail?.trim()).filter(Boolean);
            const data2 = table2.getData().map(r => r.detail?.trim()).filter(Boolean);
            const data3 = table3.getData().map(r => r.detail?.trim()).filter(Boolean);

            document.getElementById("jobAdvantagesInput").value = JSON.stringify(data1);
            document.getElementById("departAdvantagesInput").value = JSON.stringify(data2);
            document.getElementById("peopleAdvantagesInput").value = JSON.stringify(data3);

            Swal.fire({
                title: "ยืนยันการบันทึก?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "บันทึก",
                cancelButtonText: "ยกเลิก"
            }).then(result => {
                if (result.isConfirmed) {
                    this.closest("form").submit();
                }
            });
        });

        // ปุ่มล้าง
        window.confirmReset = function() {
            Swal.fire({
                title: "ยืนยันการล้างข้อมูล?",
                text: "ข้อมูลทั้งหมดจะถูกลบ",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "ล้างข้อมูล",
                cancelButtonText: "ยกเลิก"
            }).then(result => {
                if (result.isConfirmed) {
                    [table1, table2, table3].forEach(table => table.clearData());
                    Swal.fire("ล้างข้อมูลเรียบร้อยแล้ว", "", "success");
                }
            });
        };
    });
</script>

@section('script')

<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/helper.js') }}"></script>
<script src="{{ asset('js/alert.js') }}"></script>
<script src="{{ asset('js/table.js') }}"></script>
<script src="{{ asset('js/submit.js') }}"></script>

@endsection