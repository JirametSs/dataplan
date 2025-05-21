@extends('layouts.main')

@section('title', 'หน้าฟอร์มสาม')

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
<script src="https://cdn.tailwindcss.com"></script>

<!-- Form CSS -->
<link rel="stylesheet" href="{{ asset('css/form.css') }}" />

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

@if (Request::is('form3'))
<div class="step-wrapper">
    <a href="/form1" class="step active">โครงการ</a>
    <a href="/form2" class="step active">ความสอดคล้องกับ OKR</a>
    <a href="/form3" class="step active">ผลลัพธ์ของระบบงาน</a>
    <a href="/form4" class="step">รายละเอียด/แผนผังการทำงาน</a>
    <a href="/form5" class="step">เป้าหมายการดำเนินงาน</a>
    <a href="/form6" class="step">ประโยชน์ที่จะได้รับ</a>
    <a href="/form7" class="step">ตัวชี้วัด/ค่าเป้าหมาย</a>
    <a href="/form8" class="step">ประมาณการรายรับ/รายจ่าย</a>
    <a href="/form9" class="step">ผลกระทบที่คาดว่าจะได้รับ</a>
</div>
@endif

<div class="alert-custom">
    <i class="fa-solid fa-circle-info me-1"></i>
    <strong class="text-danger">*</strong> หลังข้อ หมายถึงให้ใส่รายละเอียดข้อมูลด้วย และกรุณาบันทึกข้อมูลด้านล่างก่อนทุกครั้งเมื่อมีการแก้ไขหรือบันทึกข้อมูลเพิ่มเติม
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

<button class="side-btn-open btn border-0 shadow-sm bg-white rounded-circle"
    type="button"
    onclick="btn_open()"
    style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; transition: background-color 0.3s;">
    <i class="fa-solid fa-bars text-secondary"></i>
</button>

<div class="d-flex justify-content-center align-items-start py-5" style="min-height: 100vh;">
    <div class="container" style="max-width: 1250px;margin-right:10000px;margin-left:150px;">
        <div class="card shadow">
            <div class="card-header bg-warning text-white">
                <h4 class="mb-0">แบบฟอร์มผลลัพธ์ของระบบงาน</h4>
            </div>

            <form id="resultForm" action="{{ isset($plan) ? route('form3.update', $plan->project_id) : route('form3.store') }}" method="POST">
                @csrf
                @if(isset($plan))
                @method('PUT')
                @endif

                <!-- ข้อมูลใน Tabulator -->
                <input type="hidden" name="results" id="resultsInput">

                <div class="card-body">

                    {{-- หัวข้อ 11 --}}
                    <div class="mb-5">
                        <label class="form-label fs-5 d-block text-success required">
                            11. ผลลัพธ์ของระบบงาน/โครงการมีข้อมูลสนับสนุนตัวชี้วัดตัวผลสัมฤทธิ์ของแผนกลยุทธ์ข้อใด
                            (สามารถดูข้อมูลจาก
                            <a href="https://cmu.to/MedCMUStrategicPlan4IT-Req" target="_blank" style="display: inline-block; text-decoration: underline;">
                                https://cmu.to/MedCMUStrategicPlan4IT-Req
                            </a> โดย Login ด้วย CMU IT Account เพื่อเข้าดู และนำตัวชี้วัดผลสัมฤทธิ์นั้นๆ มากรอกในตาราง)
                        </label>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button id="reactivity-add" type="button" class="btn btn-outline-success">
                                <i class="fa-solid fa-plus me-1"></i> เพิ่ม
                            </button>

                            <button id="reactivity-delete" type="button" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-minus me-1"></i> ลบ
                            </button>
                        </div>

                        <div id="results" class="rounded p-2" style="background-color: #f6fff9;"></div>
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
    window.preloadResults = @json($results ?? []);
</script>

@section('script')
<!-- Custom Script -->
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/helper.js') }}"></script>
<script src="{{ asset('js/alert.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the Tabulator table
        let table = new Tabulator("#results", {
            layout: "fitColumns",
            placeholder: "ไม่พบข้อมูล กรุณาเพิ่มรายการ",
            data: window.preloadResults || [],
            columns: [{
                    title: "ตัวชี้วัดผลสัมฤทธิ์",
                    field: "detail",
                    editor: "textarea",
                    headerFilter: true,
                    width: 400
                },
                {
                    title: "หน่วย",
                    field: "unit",
                    editor: "input",
                    headerFilter: true
                }
            ],
            pagination: true,
            paginationSize: 10
        });

        // Add row button
        document.getElementById('reactivity-add').addEventListener('click', function() {
            table.addRow({}, true);
        });

        // Delete selected rows
        document.getElementById('reactivity-delete').addEventListener('click', function() {
            const selectedRows = table.getSelectedRows();
            if (selectedRows.length > 0) {
                selectedRows.forEach(row => row.delete());
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'โปรดเลือกรายการ',
                    text: 'กรุณาเลือกรายการที่ต้องการลบก่อน',
                    confirmButtonText: 'ตกลง'
                });
            }
        });

        // Submit form button
        document.getElementById('submitFormBtn').addEventListener('click', function() {
            // End any active cell editing
            table.getEditedCells().forEach(cell => cell.cancelEdit());

            // Get table data
            const data = table.getData().map(row => ({
                detail: row.detail || "",
                unit: row.unit || ""
            }));

            // Validate there's at least one row with detail
            const hasValidData = data.some(row => row.detail && row.detail.trim() !== "");

            if (!hasValidData) {
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อมูลไม่ครบถ้วน',
                    text: 'กรุณากรอกข้อมูลตัวชี้วัดผลสัมฤทธิ์อย่างน้อย 1 รายการ',
                    confirmButtonText: 'ตกลง'
                });
                return;
            }

            // Update hidden input with JSON data
            const resultInput = document.getElementById("resultsInput");
            resultInput.value = JSON.stringify(data);

            // Confirm submission
            Swal.fire({
                title: 'ยืนยันการบันทึก?',
                text: 'ข้อมูลจะถูกบันทึกลงในระบบ',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form
                    document.getElementById('resultForm').submit();
                }
            });
        });

        // Reset confirmation
        window.confirmReset = function() {
            Swal.fire({
                title: 'ยืนยันการล้างข้อมูล?',
                text: 'ข้อมูลทั้งหมดในฟอร์มจะหายไป',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ล้างข้อมูล',
                cancelButtonText: 'ยกเลิก',
            }).then((result) => {
                if (result.isConfirmed) {
                    table.clearData();
                }
            });
        };
    });