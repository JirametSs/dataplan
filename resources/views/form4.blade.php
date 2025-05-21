@extends('layouts.main')

@section('title', 'หน้าฟอร์มสี่')

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

@if (Request::is('form4'))
<div class="step-wrapper">
    <a href="/form1" class="step active">โครงการ</a>
    <a href="/form2" class="step active">ความสอดคล้องกับ OKR</a>
    <a href="/form3" class="step active">ผลลัพธ์ของระบบงาน</a>
    <a href="/form4" class="step active">รายละเอียด/แผนผังการทำงาน</a>
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
            </div>

            @php
            $pid = $projectId ?? session('project_id');
            @endphp

            <form action="{{ isset($plan) ? route('form4.update', $plan->project_id) : route('form4.store') }}" method="POST">
                @csrf
                @if(isset($plan))
                @method('PUT')
                @endif

                <div class="card-body">

                    {{-- ข้อ 12 --}}
                    <div class="mb-4">
                        <label for="system_detail" class="form-label fw-bold text-primary required">
                            12. รายละเอียดของระบบงาน/โครงการ
                        </label><br>
                        <textarea name="system_detail" id="system_detail"
                            class="w-full max-w-5xl mx-auto px-6 py-4 text-base text-gray-800 bg-white border border-[#A5C84A] rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#A5C84A] focus:border-[#A5C84A] transition-all duration-200 ease-in-out"
                            rows="4" placeholder="อธิบายรายละเอียดระบบงานหรือโครงการที่เสนอ...">{{ old('system_detail', $system_detail ?? '') }}</textarea>
                    </div>

                    {{-- ข้อ 13 --}}
                    <div class="mb-4">
                        <label for="old_workflow" class="form-label fw-bold text-primary required">
                            13. แผนผังการทำงานเดิม (Old Work Flow)
                        </label><br>
                        <textarea name="old_workflow" id="old_workflow"
                            class="w-full max-w-5xl mx-auto px-6 py-4 text-base text-gray-800 bg-white border border-[#A5C84A] rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#A5C84A] focus:border-[#A5C84A] transition-all duration-200 ease-in-out"
                            rows="4" placeholder="อธิบายขั้นตอน/แผนผังการทำงานในปัจจุบันที่ต้องการปรับปรุง...">{{ old('old_workflow', $old_workflow ?? '') }}</textarea>
                    </div>

                    {{-- ข้อ 14 --}}
                    <div class="mb-4">
                        <label for="new_workflow" class="form-label fw-bold text-primary required">
                            14. แผนผังการทำงานใหม่ (New Work Flow)
                        </label><br>
                        <textarea name="new_workflow" id="new_workflow"
                            class="w-full max-w-5xl mx-auto px-6 py-4 text-base text-gray-800 bg-white border border-[#A5C84A] rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#A5C84A] focus:border-[#A5C84A] transition-all duration-200 ease-in-out"
                            rows="4" placeholder="ระบุขั้นตอน/แนวทางของแผนผังการทำงานใหม่ที่เสนอ...">{{ old('new_workflow', $new_workflow ?? '') }}</textarea>
                    </div>

                    {{-- ข้อ 15 --}}
                    <div class="mb-5">
                        <label class="form-label fw-bold text-primary required" style="font-size: 18px;">
                            15. ผู้ใช้งานระบบ (เลือกได้มากกว่า 1 ข้อ)
                        </label>

                        <div class="row g-2 mt-3">
                            @php
                            $users = [
                            'อาจารย์ประจำคณะฯ',
                            'นักศึกษาแพทย์',
                            'บุคลากรประจำคณะฯ',
                            'อาจารย์นอกคณะฯ (ใน มช.)',
                            'นักศึกษานอกคณะฯ (ใน มช.)',
                            'บุคคลภายนอกคณะฯ',
                            'บุคคลภายนอก มช. (เช่น ผู้ป่วย ญาติ และอื่น ๆ)',
                            ];

                            $checkedUsers = collect(old('whouse_users', $whouse_users ?? []))
                            ->map(fn($u) => trim($u))
                            ->toArray();
                            @endphp

                            @foreach ($users as $index => $label)
                            <div class="col-md-4 col-sm-6">
                                <div class="form-check">
                                    <input
                                        type="checkbox"
                                        name="whouse_users[]"
                                        id="whouse_{{ $index }}"
                                        value="{{ $label }}"
                                        {{ in_array(trim($label), $checkedUsers) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="whouse_{{ $index }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
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

@section('script')

<!-- Custom Script -->
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/helper.js') }}"></script>
<script src="{{ asset('js/alert.js') }}"></script>
<script src="{{ asset('js/submit.js') }}"></script>


@endsection
