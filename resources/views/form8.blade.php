<!DOCTYPE html>

@extends('layouts.main')

@section('title', 'หน้าฟอร์มแปด')

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

@if (Request::is('form8'))
<div class="step-wrapper">
    <a href="/form1" class="step active">โครงการ</a>
    <a href="/form2" class="step active">ความสอดคล้องกับ OKR</a>
    <a href="/form3" class="step active">ผลลัพธ์ของระบบงาน</a>
    <a href="/form4" class="step active">รายละเอียด/แผนผังการทำงาน</a>
    <a href="/form5" class="step active">เป้าหมายการดำเนินงาน</a>
    <a href="/form6" class="step active">ประโยชน์ที่จะได้รับ</a>
    <a href="/form7" class="step active">ตัวชี้วัด/ค่าเป้าหมาย</a>
    <a href="/form8" class="step active">ประมาณการรายรับ/รายจ่าย</a>
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
                    <h4 class="mb-0">ประมาณรายรับ/รายจ่าย</h4>
                </div>
            </center>

            <form action="{{ isset($plan) ? route('form8.update', $plan->project_id) : route('form8.store') }}" method="POST">
                @csrf
                @if(isset($plan))
                @method('PUT')
                @endif
                <div class="card-body">

                    <input type="hidden" name="estimations" id="estimationsInput">


                    {{-- ข้อ 21 --}}
                    <div class="mb-5">
                        <label class="form-label fw-semibold fs-5 d-block text-success required">
                            21. ประมาณการรายรับที่เกิดขึ้น หรือรายจ่ายจากงานประจำที่สามารถลดลงได้ (หากประมาณได้)
                        </label><br><br>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button id="re-add" type="button" class="btn btn-outline-success">
                                <i class="fa-solid fa-plus me-1"></i> เพิ่ม
                            </button>

                            <button id="re-delete" type="button" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-minus me-1"></i> ลบ
                            </button>
                        </div>

                        <div id="dataincome" class="rounded p-2" style="background-color: #f6fff9;"></div>
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
    // ข้อมูลเริ่มต้นจากเซิร์ฟเวอร์
    window.preloadIncome = @json($preloadIncome ?? []);

    document.addEventListener("DOMContentLoaded", function() {
        const tableContainer = document.querySelector("#dataincome");
        if (!tableContainer) {
            console.error("ไม่พบตาราง #dataincome");
            Swal.fire("ผิดพลาด", "ไม่พบตารางข้อมูลในหน้าเว็บ", "error");
            return;
        }

        const currentYear = new Date().getFullYear() + 543;

        // ✅ เตรียมข้อมูล พร้อม index สำหรับทุกแถว
        const initialData = (Array.isArray(window.preloadIncome) && window.preloadIncome.length > 0 ?
            window.preloadIncome : [{
                detail: "",
                y2567: 0,
                y2568: 0,
                y2569: 0
            }]
        ).map((row, i) => ({
            ...row,
            index: i + 1
        }));

        console.log("📦 preloadIncome", initialData);

        try {
            window.incomeTable = new Tabulator(tableContainer, {
                layout: "fitColumns",
                height: "200px",
                data: initialData,
                reactiveData: true,
                placeholder: "ยังไม่มีข้อมูลประมาณการ",
                columns: [{
                        title: "รายละเอียด",
                        field: "detail",
                        editor: "input",
                        hozAlign: "left",
                        cellEdited: updateHiddenInput
                    },
                    {
                        title: "ปี " + (currentYear - 1),
                        field: "y2567",
                        editor: "number",
                        hozAlign: "right",
                        bottomCalc: "sum",
                        cellEdited: updateHiddenInput
                    },
                    {
                        title: "ปี " + (currentYear),
                        field: "y2568",
                        editor: "number",
                        hozAlign: "right",
                        bottomCalc: "sum",
                        cellEdited: updateHiddenInput
                    },
                    {
                        title: "ปี " + (currentYear + 1),
                        field: "y2569",
                        editor: "number",
                        hozAlign: "right",
                        bottomCalc: "sum",
                        cellEdited: updateHiddenInput
                    },

                ]
            });
        } catch (error) {
            console.error("สร้างตารางล้มเหลว:", error);
            Swal.fire("ผิดพลาด", "ไม่สามารถสร้างตารางข้อมูลได้", "error");
            return;
        }

        // ✅ อัปเดต input hidden
        function updateHiddenInput() {
            const filtered = incomeTable.getData().filter(row =>
                row.detail || row.y2567 || row.y2568 || row.y2569
            );
            const hidden = document.getElementById("estimationsInput");
            if (hidden) hidden.value = JSON.stringify(filtered);
        }

        document.getElementById("re-add")?.addEventListener("click", function() {
            const last = incomeTable.getData().at(-1);
            if (!last || last.detail || last.y2567 || last.y2568 || last.y2569) {
                incomeTable.addRow({
                    detail: "",
                    y2567: 0,
                    y2568: 0,
                    y2569: 0
                });
                updateHiddenInput();
            }
        });

        document.getElementById("re-delete")?.addEventListener("click", function() {
            const rows = incomeTable.getRows();
            if (rows.length > 0) {
                rows.at(-1).delete();
                updateHiddenInput();
            }
        });

        document.getElementById("submitFormBtn")?.addEventListener("click", async function() {
            try {
                incomeTable.getEditedCells().forEach(cell => cell.cancelEdit());
                const validData = incomeTable.getData().filter(row =>
                    row.detail || row.y2567 || row.y2568 || row.y2569
                );

                if (validData.length === 0) {
                    throw new Error("กรุณากรอกข้อมูลประมาณการอย่างน้อย 1 รายการ");
                }

                const res = await Swal.fire({
                    title: "ยืนยันการบันทึก?",
                    text: "คุณแน่ใจต้องการบันทึกข้อมูลนี้หรือไม่?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "บันทึก",
                    cancelButtonText: "ยกเลิก"
                });

                if (res.isConfirmed) {
                    document.getElementById("estimationForm").submit();
                }
            } catch (error) {
                Swal.fire("ผิดพลาด", error.message, "error");
                console.error("⛔ ERROR:", error);
            }
        });

        window.confirmReset = async function() {
            const res = await Swal.fire({
                title: "ยืนยันการล้างข้อมูล?",
                text: "ข้อมูลทั้งหมดจะถูกลบและกลับไปเป็นค่าเริ่มต้น",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "ล้างข้อมูล",
                cancelButtonText: "ยกเลิก"
            });

            if (res.isConfirmed) {
                incomeTable.setData([{
                    detail: "",
                    y2567: 0,
                    y2568: 0,
                    y2569: 0
                }]);
                updateHiddenInput();
            }
        };

        updateHiddenInput();
    });
</script>
@section('script')

<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/helper.js') }}"></script>
<script src="{{ asset('js/alert.js') }}"></script>
<script src="{{ asset('js/table.js') }}"></script>
<script src="{{ asset('js/submit.js') }}"></script>

@endsection