<html lang="th">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'THSarabunNew', 'Times New Roman', serif;
            font-size: 16px;
            line-height: 1.5;
            margin: 0 auto;
            padding: 0;
            color: #000;
            background: white;
            width: 210mm;
            min-height: 297mm;
            box-sizing: border-box;
        }

        .container {
            width: 100%;
            padding: 0 1.5cm;
            box-sizing: border-box;
        }

        .section {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            page-break-inside: avoid;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px 10px;
            vertical-align: top;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        ol,
        ul {
            padding-left: 25px;
            margin: 10px 0;
        }

        .signature-block {
            margin-top: 50px;
            text-align: center;
            page-break-inside: avoid;
        }

        .signature-line {
            display: inline-block;
            width: 260px;
            border-bottom: 1px solid #000;
            margin: 20px auto 5px auto;
        }

        .checkbox {
            display: inline-block;
            width: 16px;
            height: 16px;
            margin-right: 8px;
            border: 1.5px solid #000;
            border-radius: 2px;
            position: relative;
            box-sizing: border-box;
            vertical-align: middle;
        }

        .checkbox.checked {
            background-color: #ffffff;
            border-color: #000000;
        }

        .checkbox.checked::after {
            content: '✓';
            color: #000000;
            font-size: 12px;
            font-weight: bold;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .subgoals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }

        .subgoal-item {
            display: flex;
            align-items: center;
        }

        .goal-name {
            margin: 12px 0 8px 0;
            color: #000;
            font-size: 16px;
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
            height: 0;
            display: block;
        }

        /* Additional formal document elements */
        .header {
            text-align: center;
            margin-bottom: 2cm;
            page-break-after: avoid;
        }

        .footer {
            text-align: center;
            margin-top: 1cm;
            font-size: 12px;
            page-break-before: avoid;
        }

        .date {
            text-align: right;
            margin: 15px 0 25px;
        }

        .letterhead {
            text-align: center;
            margin-bottom: 1cm;
        }

        .reference-number {
            margin-bottom: 0.5cm;
        }

        /* Print-specific optimizations */
        @media print {
            a {
                text-decoration: none;
                color: #000;
            }

            table,
            figure,
            img {
                page-break-inside: avoid;
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                page-break-after: avoid;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <center>
        <header>
            แบบเสนอแผนดำเนินงาน/โครงการ<br>
            ประกอบการขอพัฒนา/ปรับปรุงระบบเทคโนโลยีสารสนเทศ<br>
            ประจำปีงบประมาณ พ.ศ. 2568<br>
            คณะแพทยศาสตร์ มหาวิทยาลัยเชียงใหม่<br>
            *********************************************
        </header>
    </center>

    {{-- FORM1 --}}
    <div class="section">
        <p>1. ชื่อโครงการ/โครงการ: &nbsp;{{ $form1->title ?? '-' }}</p>
        @php
        $selectedType = $projectTypes->firstWhere('id', $form1->project_type)->name ?? null;
        @endphp

        <p style=" margin-bottom: 10px;">2. ลักษณะระบบงาน/โครงการ:</p>

        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-left: 20px;">
            @foreach ($projectTypes as $type)
            <div style="display: flex; align-items: center; min-width: 200px;">
                <span class="checkbox {{ $form1->project_type == $type->id ? 'checked' : '' }}"></span>
                <span style="font-size: 16px;">{{ $type->name }}</span>
            </div>
            @endforeach
        </div>

        <p>3. หน่วยงานที่รับผิดชอบ:&nbsp;{{ $form1->t_work_name ?? '-' }}</p>
        <p>ผู้รับผิดชอบ:</strong> {{ $form1->who_present ?? '-' }} &nbsp;&nbsp; โทร: {{ $form1->tel ?? '-' }} &nbsp;&nbsp; Email: {{ $form1->email ?? '-' }}</p>
        <p>4. ความร่วมมือกับหน่วยงานอื่น: {{ $form1->cojob ?? '-' }}</p>
        <p>5. งบประมาณและแหล่งที่มา:{{ $form1->budget_detail ?? '-' }}</p>
    </div>
    <div class="strategy-section">
        <p>
            6. ระบบงาน/โครงการนี้สอดคล้องกับแผนกลยุทธ์ MEDCMU วัตถุประสงค์เชิงกลยุทธ์วาระบริหาร 2564-2568:
        </p>

        @php
        $selectedSubgoals = $form_goal->pluck('tgoalsub_id')->toArray();
        @endphp

        @foreach($goals as $goal)
        @php
        // ดึงเฉพาะ subgoal ที่อยู่ใน group นี้ และถูกเลือกไว้
        $selectedSubsInGroup = $subgoals
        ->where('goal_id', $goal->id)
        ->filter(fn($sub) => in_array($sub->id, $selectedSubgoals));
        @endphp

        @if($selectedSubsInGroup->count())
        <h3 class="goal-name">{{ $goal->name }}</h3>

        <div class="subgoals-grid">
            @foreach($selectedSubsInGroup as $sub)
            <div class="subgoal-item">
                <span class="checkbox checked"></span>
                <span class="subgoal-name">{{ $sub->name }}</span>
            </div>
            @endforeach
        </div>
        @endif
        @endforeach
    </div>

    <p>7. หลักการและเหตุผล:
        @foreach($form_reason as $item)
        {{ $item->detail }}
    </p>
    @endforeach
    <p>8. วัตถุประสงค์ของโครงการ:</p>
    <!-- <ol>
        @foreach($form_objective as $item)
        <li>{{ $item->detail }}</li>
        @endforeach
    </ol> -->
    <table>
        <thead>
            <tr>
                <th>รายละเอียด</th>
            </tr>
        </thead>
        <tbody>
            @foreach($form_objective as $item)
            <tr>
                <td>{{ $item->detail }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p>9. ระยะเวลาดำเนินการ:

        เริ่ม: {{ $form1->sdate }} &nbsp;&nbsp;
        สิ้นสุด: {{ $form1->edate }}
        ระยะเวลา:
        {{
        ($form1->year_long * 365) +
        ($form1->month_long * 30) +
        ($form1->day_long)
    }} วัน
    </p>

    {{-- FORM2 --}}
    <p>10. ระบบงาน/โครงการนี้สอดคล้องกับ OKR ของคณะในข้อใดบ้าง:</p>
    @php
    $selectedSubokrs = $form_okr->pluck('okr_id')->toArray(); // ดึงรายการที่เลือกไว้จาก tokr
    @endphp

    @foreach($okrs as $okr)
    @php
    // ดึง subokr ภายใต้ okr ที่เลือกไว้ และอยู่ในรายการ selected
    $selectedSubsInGroup = $subokrs
    ->where('okr_id', $okr->id)
    ->filter(fn($sub) => in_array($sub->id, $selectedSubokrs));
    @endphp

    @if($selectedSubsInGroup->isNotEmpty())
    <h4 class="goal-name">{{ $okr->name }}</h4>
    <div class="subgoals-grid">
        @foreach($selectedSubsInGroup as $sub)
        <div class="subgoal-item">
            <span class="checkbox checked"></span>
            <span class="subgoal-name">{{ $sub->name }}</span>
        </div>
        @endforeach
    </div>
    @endif
    @endforeach


    {{-- FORM3 --}}
    <div class="section">

        <p>11. ผลลัพธ์ของระบบงาน/โครงการมีข้อมูลสนับสนุนตัวชี้วัดตัวผลสัมฤทธิ์ของแผนกลยุทธ์ข้อใด (สามารถดูข้อมูลจาก
            https://cmu.to/MedCMUStrategicPlan4IT-Req โดย Login ด้วย CMU IT Account เพื่อเข้าดู และน าตัวชี้วัดผลสัมฤทธิ์
            นั้นๆ มากรอกในตาราง)</p>
        <table>
            <thead>
                <tr>
                    <th>ตัววัดผลสัมฤทธิ์ของแผน </th>
                    <th>หน่วยวัด</th>
                </tr>
            </thead>
            <tbody>
                @foreach($form3 as $r)
                <tr>
                    <td>{{ $r->detail }}</td>
                    <td>{{ $r->unit }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- FORM4 --}}
    <div class="section">

        @php $workflow = $form4->first(); @endphp
        <p>12. รายละเอียดของระบบงาน/โครงการ:&nbsp; {{ $workflow->workflow ?? '-' }}</p>
        <p>13. แผนผังการทำงานเดิม (Old Work Flow):&nbsp; {{ $workflow->old_workflow ?? '-' }}</p>
        <p>14. แผนผังการทำงานใหม่ (New Work Flow):&nbsp; {{ $workflow->new_workflow ?? '-' }}</p>
        <p style="margin-bottom: 10px;">15. ผู้ใช้งานระบบ:</p>
        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-left: 20px;">
            @foreach($form4_whouse as $w)
            <div style="display: flex; align-items: center; min-width: 200px;">
                <span class="checkbox checked"></span>
                <span style="font-size: 16px;">{{ $w->name }}</span>
            </div>
            @endforeach
        </div>

    </div>

    {{-- FORM5 --}}
    <div class="section">
        <p>16. เป้าหมายการดำเนินงาน</p>
        <!-- <ol>
            @foreach($form5 as $target)
            <li>{{ $target->detail }}</li>
            @endforeach
        </ol> -->
        <table>
            <thead>
                <tr>
                    <th>รายละเอียด</th>
                </tr>
            </thead>
            <tbody>
                @foreach($form5 as $target)
                <tr>
                    <td>{{ $target->detail }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- FORM6 --}}
    <div class="section">
        {{-- ข้อ 17 --}}
        <p>17. ประโยชน์ที่หน่วยงานจะได้รับจากระบบงาน/โครงการนี้:</p>
        @foreach(collect($form6)->pluck('job_advantage')->filter(fn($v) => trim($v) !== '') as $item)
        <p class="ms-4">- {{ $item }}</p>
        @endforeach

        {{-- ข้อ 18 --}}
        <p>18. ประโยชน์ที่คณะฯ จะได้รับจากระบบงาน/โครงการนี้:</p>
        @foreach(collect($form6)->pluck('depart_advantage')->filter(fn($v) => trim($v) !== '') as $item)
        <p class="ms-4">- {{ $item }}</p>
        @endforeach

        {{-- ข้อ 19 --}}
        <p>19. ประโยชน์ที่ประชาชนจะได้รับจากระบบงาน/โครงการนี้:</p>
        @foreach(collect($form6)->pluck('people_advantage')->filter(fn($v) => trim($v) !== '') as $item)
        <p class="ms-4">- {{ $item }}</p>
        @endforeach
    </div>

    {{-- FORM7 --}}
    <div class="section">
        <p>20. ตัวชี้วัด/ค่าเป้าหมาย</p>
        <table>
            <thead>
                <tr>
                    <th>ประเภท</th>
                    <th>รายละเอียด</th>
                    <th>ค่าเป้าหมาย</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($form7 as $index)
                <tr>
                    <td>{{ $index->index_id }}</td>
                    <td>{{ $index->detail }}</td>
                    <td>{{ $index->index_value }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- FORM8 --}}
    <div class="section">
        <p>21. ประมาณการรายรับที่เกิดขึ้น หรือรายจ่ายจากงานประจำที่สามารถลดลงได้ (หากประมาณได้)</p>
        <table>
            <thead>
                <tr>
                    <th>ปี</th>
                    <th>รายละเอียด</th>
                    <th>จำนวนเงิน (บาท)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($form8 as $est)
                <tr>
                    <td>{{ $est->year }}</td>
                    <td>{{ $est->detail }}</td>
                    <td style="text-align: right;">{{ number_format($est->amount ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- FORM9 --}}
    <div class="section">
        <p>22. ผลกระทบ (Impact) ทั้งด้านบวกและด้านลบที่คาดว่าจะได้รับจากระบบงาน/โครงการนี้</p>
        <ol>
            @foreach($form9 as $impact)
            <li>{{ $impact->detail }}</li>
            @endforeach
        </ol>

        <p>23. การประเมินผลและระยะเวลาของการประเมินโครงการ</p>
        <p>ระยะเวลา: {{ $form1->period_time ?? '-' }}</p>
    </div>

    <!-- {{-- SIGNATURE & APPROVAL --}}
    <div class="section signature-block">
        <p>ลงชื่อ <span class="signature-line"></span> ผู้เสนอโครงการ</p>
        <p>( {{ $form1->who_present ?? '..................................' }} )</p>

        <p>ลงชื่อ <span class="signature-line"></span> หัวหน้าภาค/ฝ่าย/งาน</p>
        <p>( .................................. )</p>

        <p>ลงชื่อ <span class="signature-line"></span> ผู้บริหารที่รับผิดชอบ</p>
        <p>( .................................. )</p>



        <p style="margin-top: 30px;">ลงชื่อ.......................................................... (..................................................)<br>คณบดีคณะแพทยศาสตร์</p>
    </div> -->

</body>

</html>