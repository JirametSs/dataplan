@extends('layouts.main')

@section('title', 'หน้าฟอร์มแรก')

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
@vite(['resources/css/app.css', 'resources/js/app.js'])
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

@if (Request::is('form1'))
<div class="step-wrapper">
    <a href="/form1" class="step active">โครงการ</a>
    <a href="/form2" class="step">ความสอดคล้องกับ OKR</a>
    <a href="/form3" class="step">ผลลัพธ์ของระบบงาน</a>
    <a href="/form4" class="step">รายละเอียด/แผนผังการทำงาน</a>
    <a href="/form5" class="step">เป้าหมายการดำเนินงาน</a>
    <a href="/form6" class="step">ประโยชน์ที่จะได้รับ</a>
    <a href="/form7" class="step">ตัวชี้วัด/ค่าเป้าหมาย</a>
    <a href="/form8" class="step">ประมาณการรายรับ/รายจ่าย</a>
    <a href="/form9" class="step">ผลกระทบที่คาดว่าจะได้รับ</a>
</div>
@endif

@if (Request::is('/'))
<div class="step-wrapper">
    <a href="/form1" class="step active">โครงการ</a>
    <a href="/form2" class="step">ความสอดคล้องกับ OKR</a>
    <a href="/form3" class="step">ผลลัพธ์ของระบบงาน</a>
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

<div class="mb-4">
    @if (isset($editMode) && $editMode)
    <a href="{{ route('dashboard.show') }}"
        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded hover:bg-gray-300 transition duration-150 ease-in-out shadow">
        <i class="fas fa-arrow-left mr-2"></i> ย้อนกลับไปหน้า Dashboard
    </a>
    @endif
</div>

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

            <form action="{{ isset($plan) ? route('form1.edit', $plan->project_id) : route('form1.store') }}" method="POST">
                @csrf
                @if(isset($plan))
                @method('PUT')
                @endif

                <input type="hidden" name="objective" id="objectiveInput">

                <div class="card-body">
                    @php
                    $plan = $plan ?? null;
                    @endphp
                    {{-- หัวข้อ 1 --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold d-flex flex-wrap align-items-center gap-2 mb-2 required">
                            1. ชื่อระบบงาน/โครงการ
                        </label>
                        <input
                            type="text"
                            name="title"
                            class="form-control full-width-input"
                            style="max-width: 100%;"
                            placeholder="ชื่อระบบงาน/โครงการ"
                            value="{{ old('title', $plan->title ?? '') }}">
                    </div>


                    {{-- ข้อ 2 --}}
                    <div class="mb-3">
                        <label for="project_type" class="form-label fw-bold required">2. ประเภทโครงการ</label>
                        <select name="project_type" id="project_type" class="form-select">
                            <option value="">-- เลือกประเภทโครงการ --</option>
                            @foreach ($projectTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ old('project_type', $plan->project_type ?? '') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- ข้อ 3 --}}
                    <div class="mb-3">
                        <label for="job_unit_id" class="form-label fw-bold required">3. หน่วยงานที่รับผิดชอบ</label>

                        {{-- ฟิกซ์ค่า 101-1400-0 --}}
                        <input type="hidden" name="job_unit_id" id="job_unit_id" value="101-1400-0">
                        <input type="text" class="form-control" value="งานเทคโนโลยีสารสนเทศ" disabled>

                        <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
                            {{-- ผู้รับผิดชอบ --}}
                            <div class="mb-3" style="flex: 1;">
                                <label for="responsible_person" class="mb-0">ผู้รับผิดชอบ</label><br>
                                @php
                                $selectedPerson = old('responsible_person', optional($plan)->who_present);
                                @endphp
                                <select id="responsible_person"
                                    name="responsible_person"
                                    class="form-select"
                                    style="width: 100%; font-size: 14px;">
                                    @if($selectedPerson)
                                    <option value="{{ $selectedPerson }}" selected>{{ $selectedPerson }}</option>
                                    @else
                                    <option class="js-example-basic-single js-states form-control">-- เลือกผู้รับผิดชอบ --</option>
                                    @endif
                                </select>
                            </div>

                            {{-- โทรศัพท์ --}}
                            <div style="flex: 1;">
                                <label class="mb-0">โทรศัพท์</label>
                                <input type="text" id="phone" name="tel" class="full-width-input"
                                    value="{{ old('tel', $plan->tel ?? '') }}" placeholder="โทรศัพท์">
                            </div><br>

                            {{-- อีเมล --}}
                            <div style="flex: 1;">
                                <label class="mb-0">อีเมล</label>
                                <input type="text" id="email" name="email" class="full-width-input"
                                    value="{{ old('email', $plan->email ?? '') }}" placeholder="E-mail address">
                            </div>
                        </div>
                    </div>

                    {{-- หัวข้อ 4 --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold ">
                            4. ความร่วมมือกับหน่วยงานอื่น <span class="text-muted">(ถ้ามี กรุณาระบุ)</span>
                        </label>
                        <input type="text" name="collaboration" class="full-width-input"
                            value="{{ old('collaboration', $plan->cojob ?? '') }}"
                            placeholder="ระบุชื่อหน่วยงานที่เกี่ยวข้อง">

                    </div>

                    {{-- หัวข้อ 5 --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold ">
                            5. งบประมาณ และแหล่งที่มาของงบประมาณ <span class="text-muted">(ถ้ามี)</span>
                        </label>
                        <input type="text" name="budget_source" class="full-width-input"
                            value="{{ old('budget_source', $plan->budget_detail ?? '') }}"
                            placeholder="ระบุจำนวนงบประมาณ และแหล่งที่มา">

                    </div>

                    {{-- ข้อ 6 --}}
                    @php
                    $selectedSubgoals = old('subgoals', $planSubgoals ?? []);
                    @endphp

                    <div class="mb-4">
                        <label class="form-label fw-bold d-block mb-2 required">
                            6. ระบบงาน/โครงการนี้สอดคล้องกับแผนกลยุทธ์ MEDCMU วัตถุประสงค์เชิงกลยุทธ์วาระบริหาร 2564–2568
                        </label>

                        @foreach($goals as $goal)
                        <div class="mb-4">
                            {{-- ชื่อหัวข้อหมวดหลัก --}}
                            <div class="fw-semibold text-dark mb-2">
                                <b>{{ $goal->name }}</b>
                            </div>

                            {{-- ปุ่ม Subgoal --}}
                            <div class="row g-2">
                                @foreach($subgoals->where('goal_id', $goal->id) as $subgoal)
                                <div class="col-md-4 col-sm-6">
                                    <input type="checkbox" class="btn-check"
                                        name="subgoals[]" id="subgoal{{ $subgoal->id }}"
                                        value="{{ $subgoal->id }}"
                                        {{ in_array($subgoal->id, $selectedSubgoals) ? 'checked' : '' }}
                                        autocomplete="off">

                                    <label class="btn btn-outline-success w-100 text-start"
                                        for="subgoal{{ $subgoal->id }}" style="font-size: 18px;">
                                        {{ $subgoal->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- หัวข้อ 7 --}}
                    @php
                    $rationaleValue = old('rationale', $rationale ?? '');
                    @endphp

                    <div class="mb-4">
                        <label for="rationale" class="form-label fw-bold required">
                            7. หลักการและเหตุผล
                        </label><br>
                        <textarea name="rationale" id="rationale" rows="6"
                            class="w-full max-w-5xl mx-auto px-6 py-4 text-base text-gray-800 bg-white border border-[#A5C84A] rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#A5C84A] focus:border-[#A5C84A] transition-all duration-200 ease-in-out"
                            placeholder="กรอกหลักการและเหตุผลของโครงการที่นี่...">{{ $rationaleValue }}</textarea>
                    </div>


                    {{-- หัวข้อ 8 --}}
                    <div class="mb-5 objective-section">
                        <label class="form-label fw-semibold fs-5 d-block text-gradient-primary mb-4 required">
                            8. วัตถุประสงค์
                        </label>

                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <button id="reactivity-add" type="button" class="btn btn-primary-gradient shadow-lg btn-hover-effect">
                                <i class="fa-solid fa-plus me-2"></i> เพิ่มวัตถุประสงค์
                            </button>

                            <button id="reactivity-delete" type="button" class="btn btn-danger-gradient shadow-lg btn-hover-effect">
                                <i class="fa-solid fa-minus me-2"></i> ลบวัตถุประสงค์
                            </button>
                        </div>

                        <div id="objective" class="tabulator-objective-premium"></div>
                    </div>

                    {{-- หัวข้อ 9 --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block mb-2 required">9. ระยะเวลาดำเนินการ:</label>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <label class="mb-0">เริ่ม</label>
                            <input type="date" id="sdate" class="form-control" name="sdate" style="width: 200px;"
                                value="{{ old('sdate', $plan->sdate ?? '') }}">

                            <label class="mb-0 ms-3">สิ้นสุด</label>
                            <input type="date" id="edate" class="form-control" name="edate" style="width: 200px;"
                                value="{{ old('edate', $plan->edate ?? '') }}">
                        </div><br>

                        <div class="d-flex flex-wrap align-items-center">
                            <input type="number" id="year_long" name="year_long" class="form-control me-2" placeholder="ปี" min="0" style="width: 80px;" readonly
                                value="{{ old('year_long', $plan->year_long ?? 0) }}">
                            <span class="me-3">ปี</span>

                            <input type="number" id="month_long" name="month_long" class="form-control me-2" placeholder="เดือน" min="0" max="12" style="width: 80px;" readonly
                                value="{{ old('month_long', $plan->month_long ?? 0) }}">
                            <span class="me-3">เดือน</span>

                            <input type="number" id="day_long" name="day_long" class="form-control me-2" placeholder="วัน" min="0" max="31" style="width: 80px;" readonly
                                value="{{ old('day_long', $plan->day_long ?? 0) }}">
                            <span class="me-4">วัน</span>
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

<script>
    window.preloadObjectives = @json($objectives ?? []);
</script>

@section('script')
<!-- Custom Script -->
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/helper.js') }}"></script>
<script src="{{ asset('js/alert.js') }}"></script>
<script src="{{ asset('js/submit.js') }}"></script>

@endsection
