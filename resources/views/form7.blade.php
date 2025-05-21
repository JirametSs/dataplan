@extends('layouts.main')

@section('title', 'หน้าฟอร์มเจ็ด')

@section('head')
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
<!-- Custom CSS -->
<link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
<link rel="stylesheet" href="{{ asset('css/form.css') }}" />
<!-- Tailwind -->
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<!-- Tabulator -->
<link href="https://unpkg.com/tabulator-tables@6.3.1/dist/css/tabulator.min.css" rel="stylesheet">
<script src="https://unpkg.com/tabulator-tables@6.3.1/dist/js/tabulator.min.js"></script>
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert2 -->
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

@if (Request::is('form7'))
<div class="step-wrapper">
    <a href="/form1" class="step active">โครงการ</a>
    <a href="/form2" class="step active">ความสอดคล้องกับ OKR</a>
    <a href="/form3" class="step active">ผลลัพธ์ของระบบงาน</a>
    <a href="/form4" class="step active">รายละเอียด/แผนผังการทำงาน</a>
    <a href="/form5" class="step active">เป้าหมายการดำเนินงาน</a>
    <a href="/form6" class="step active">ประโยชน์ที่จะได้รับ</a>
    <a href="/form7" class="step active">ตัวชี้วัด/ค่าเป้าหมาย</a>
    <a href="/form8" class="step">ประมาณการรายรับ/รายจ่าย</a>
    <a href="/form9" class="step">ผลกระทบที่คาดว่าจะได้รับ</a>
</div>
@endif

<div class="alert-custom">
    <i class="fa-solid fa-circle-info me-1"></i>
    <strong class="text-danger">*</strong> กรุณาบันทึกข้อมูลด้านล่างก่อนทุกครั้งเมื่อมีการแก้ไขหรือเพิ่มข้อมูล
</div>

@if ($errors->any())
<div class="flex justify-center mt-5">
    <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4" style="margin-right: 300px;">
        <div class="flex items-center text-red-800 font-semibold text-lg">
            <i class="fas fa-exclamation-triangle mr-2"></i>
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
    type="button" onclick="btn_open()" style="width: 48px; height: 48px;">
    <i class="fa-solid fa-bars text-secondary"></i>
</button>

<div class="d-flex justify-content-center align-items-start py-5" style="min-height: 100vh;">
    <div class="container" style="max-width: 1250px;margin-left:150px;">
        <div class="card shadow">
            <div class="card-header bg-warning text-white"></div>

            <form action="{{ isset($plan) ? route('form7.update', $plan->project_id) : route('form7.store') }}" method="POST">
                @csrf
                @if(isset($plan))
                @method('PUT')
                @endif

                <input type="hidden" name="indicators" id="indicatorsInput">

                <div class="card-body">
                    <div class="mb-5">
                        <label class="form-label fw-semibold fs-5 d-block text-success required">20. ตัวชี้วัด/ค่าเป้าหมาย</label>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button id="reactivity-add" type="button" class="btn btn-outline-success">
                                <i class="fa-solid fa-plus me-1"></i> เพิ่ม
                            </button>

                            <button id="reactivity-delete" type="button" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-minus me-1"></i> ลบ
                            </button>
                        </div>

                        <div id="datagoal" class="rounded p-2" style="background-color: #f6fff9;"></div>
                    </div>

                    <div class="text-center">
                        <button id="submitFormBtn" type="button"
                            class="bg-green-200 hover:bg-green-300 text-green-900 font-bold py-2 px-4 border-b-4 border-green-300 hover:border-green-400 rounded-full shadow-sm d-flex align-items-center gap-2">
                            <i class="fas fa-save"></i> <span>บันทึกข้อมูล</span>
                        </button>

                        <button type="button" id="reset-indicators"
                            class="bg-yellow-200 hover:bg-yellow-300 text-yellow-900 font-bold py-2 px-4 border-b-4 border-yellow-300 hover:border-yellow-400 rounded-full shadow-sm d-flex align-items-center gap-2">
                            <i class="fas fa-eraser"></i> <span>ล้างข้อมูล</span>
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
    window.preloadIndicators = @json($preloadIndicators ?? []);

    document.addEventListener("DOMContentLoaded", function() {
        // 1. การเตรียมข้อมูลเริ่มต้น
        const prepareTableData = (data, fallback) => {
            return Array.isArray(data) && data.length > 0 ? data : fallback;
        };

        // 2. ตรวจสอบ element ใน DOM
        const tableContainer = document.querySelector("#datagoal");
        if (!tableContainer) {
            console.error("ไม่พบตาราง #datagoal");
            Swal.fire("ผิดพลาด", "ไม่พบตารางข้อมูลในหน้าเว็บ", "error");
            return;
        }

        // 3. ข้อมูลเริ่มต้น
        const initialData = prepareTableData(window.preloadIndicators, [{
                type: "เชิงปริมาณ",
                detail: "",
                target: ""
            },
            {
                type: "เชิงคุณภาพ",
                detail: "",
                target: ""
            }
        ]);

        // 4. สร้างตาราง Tabulator
        try {
            window.indexTable = new Tabulator(tableContainer, {
                layout: "fitColumns",
                reactiveData: true,
                height: "150px",
                data: initialData,
                columns: [{
                        title: "ประเภท",
                        field: "type",
                        hozAlign: "center",
                        width: 150,
                        editor: false // ห้ามแก้ไขคอลัมน์ประเภท
                    },
                    {
                        title: "รายละเอียด",
                        field: "detail",
                        editor: "input",
                        hozAlign: "left",
                        cellEdited: function(cell) {
                            validateAndPrepareData();
                        }
                    },
                    {
                        title: "ค่าเป้าหมาย",
                        field: "target",
                        editor: "input",
                        hozAlign: "center",
                        width: 150,
                        cellEdited: function(cell) {
                            validateAndPrepareData();
                        }
                    }
                ]
            });
        } catch (error) {
            console.error("สร้างตารางล้มเหลว:", error);
            Swal.fire("ผิดพลาด", "ไม่สามารถสร้างตารางข้อมูลได้", "error");
            return;
        }

        // 5. ฟังก์ชันตรวจสอบและเตรียมข้อมูล
        const validateAndPrepareData = () => {
            const data = indexTable.getData();
            const formatted = data.map(row => ({
                index_id: row.type,
                detail: row.detail?.trim() || "",
                index_value: row.target?.trim() || ""
            })).filter(item => item.detail || item.index_value);

            document.getElementById("indicatorsInput").value = JSON.stringify(formatted);
            return formatted;
        };

        // 6. การจัดการเหตุการณ์ต่างๆ
        // เพิ่มแถว
        document.getElementById("reactivity-add")?.addEventListener("click", () => {
            indexTable.addRow({
                type: "เชิงปริมาณ",
                detail: "",
                target: ""
            });
            indexTable.addRow({
                type: "เชิงคุณภาพ",
                detail: "",
                target: ""
            });
            validateAndPrepareData();
        });

        // ลบแถว
        document.getElementById("reactivity-delete")?.addEventListener("click", () => {
            const rows = indexTable.getRows();
            if (rows.length > 2) {
                rows.slice(-2).forEach(row => row.delete());
                validateAndPrepareData();
            }
        });

        // ปุ่มบันทึก
        document.getElementById("submitFormBtn")?.addEventListener("click", async () => {
            try {
                // ยกเลิกการแก้ไขทั้งหมด
                indexTable.getEditedCells().forEach(cell => cell.cancelEdit());

                // เตรียมข้อมูล
                const formatted = validateAndPrepareData();

                // ตรวจสอบข้อมูล
                if (formatted.length < 2) {
                    throw new Error("กรุณากรอกข้อมูลตัวชี้วัดอย่างน้อย 1 คู่ (เชิงปริมาณและเชิงคุณภาพ)");
                }

                // ยืนยันการบันทึก
                const {
                    isConfirmed
                } = await Swal.fire({
                    title: "ยืนยันการบันทึก?",
                    text: "คุณแน่ใจต้องการบันทึกข้อมูลนี้หรือไม่?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "บันทึก",
                    cancelButtonText: "ยกเลิก"
                });

                if (isConfirmed) {
                    // ส่งฟอร์ม
                    document.getElementById("indicatorForm").submit();
                }
            } catch (error) {
                Swal.fire("ผิดพลาด", error.message, "error");
                console.error("เกิดข้อผิดพลาด:", error);
            }
        });

        // ปุ่มล้างข้อมูล
        document.getElementById("reset-indicators")?.addEventListener("click", async () => {
            const {
                isConfirmed
            } = await Swal.fire({
                title: "ยืนยันการล้างข้อมูล?",
                text: "ข้อมูลทั้งหมดจะถูกลบและกลับไปเป็นค่าเริ่มต้น",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "ล้างข้อมูล",
                cancelButtonText: "ยกเลิก"
            });

            if (isConfirmed) {
                indexTable.setData([{
                        type: "เชิงปริมาณ",
                        detail: "",
                        target: ""
                    },
                    {
                        type: "เชิงคุณภาพ",
                        detail: "",
                        target: ""
                    }
                ]);
                validateAndPrepareData();
            }
        });

        // เตรียมข้อมูลครั้งแรก
        validateAndPrepareData();
    });
</script>

@section('script')
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/helper.js') }}"></script>
<script src="{{ asset('js/alert.js') }}"></script>
<script src="{{ asset('js/table.js') }}"></script>
@endsection