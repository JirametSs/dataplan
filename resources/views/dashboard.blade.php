@extends('layouts.main')

@section('title', 'แดชบอร์ดโครงการ')

@section('head')

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- Tailwind Elements -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tw-elements/dist/css/tw-elements.min.css">

<!-- Jquery JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Custom CSS -->
<link rel="stylesheet" href="{{ asset('css/custom.css') }}" />

@endsection

@section('content')

<div class="min-h-screen px-4 py-8" style="margin-right:250px">
    <div class="max-w-7xl mx-auto">
        <!-- Header with breadcrumb -->
        <div class="mb-6">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="{{ url('/form1') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <i class="fas fa-home mr-2"></i>
                            หน้าหลัก
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">แดชบอร์ดโครงการ</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">แดชบอร์ดโครงการ</h1>
                    <p class="text-gray-600 mt-1 text-sm md:text-base">จัดการโครงการทั้งหมดของคุณที่นี่</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ url('/form1') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-all duration-200">
                        <i class="fas fa-plus-circle mr-2"></i> สร้างโครงการใหม่
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">โครงการทั้งหมด</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ count($projects) }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-list-check text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">โครงการที่ดำเนินการ</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $projects->where('flag', 1)->count() }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-spinner text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">ส่งกลับเพื่อแก้ไข</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $projects->where('flag', 9)->count() }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-exclamation-circle text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">รอดำเนินการ</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $projects->where('flag', 0)->count() }}</p>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-full">
                        <i class="fas fa-clock text-gray-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Responsive Table Container -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden table-container">
            <div class="p-4 border-b border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                    <!-- หัวข้อ -->
                    <div class="text-left w-full md:w-auto">
                        <h2 class="text-lg font-semibold text-gray-800">รายการโครงการทั้งหมด</h2>
                    </div>

                    <!-- ช่องค้นหา DataTables -->
                    <div id="custom-search-container" class="w-full md:w-auto text-right"></div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table id="project-table" class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">ปี</th>
                        <th scope="col" class="px-6 py-3">เลขที่โครงการ</th>
                        <th scope="col" class="px-6 py-3">ชื่อระบบ/โครงการ</th>
                        <th scope="col" class="px-6 py-3">วันที่เริ่มต้น</th>
                        <th scope="col" class="px-6 py-3">วันที่สิ้นสุด</th>
                        <th scope="col" class="px-6 py-3 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                    <tr class="bg-white border-b hover:bg-gray-50 table-row-hover">

                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-4 h-4 text-blue-500 inline-block mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18
                                    0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18
                                    0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25
                                    2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001
                                    4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25
                                    0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0
                                    2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
                            </svg>

                            {{ $project->year + 543 }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $project->project_id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 mr-3">
                                    <div class="h-full w-full rounded-md bg-blue-100 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3b82f6" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                                        </svg>

                                    </div>
                                </div>
                                <div>
                                    <div class="text-base font-medium text-gray-900">{{ $project->title }}</div>
                                    <div class="text-sm text-gray-500">ประเภท: {{ $project->project_type ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($project->sdate)
                            <div class="text-gray-900">{{ $project->sdate }}</div>
                            @else
                            <span class="text-gray-400">ยังไม่มีการส่ง</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($project->edate)
                            <div class="text-gray-900">{{ $project->edate }}</div>
                            @else
                            <span class="text-gray-400">ยังไม่มีการส่ง</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="action-container">
                                {{-- ปุ่มแก้ไขโครงการ --}}
                                <a href="{{ url('/form1/edit/'.$project->project_id) }}"
                                    class="action-btn bg-green-100 text-green-600 hover:bg-green-200"
                                    title="แก้ไขโครงการ">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </a>

                                {{-- ปุ่มยกเลิกโครงการ --}}
                                <form id="cancel-form-{{ $project->project_id }}" action="{{ route('project.cancel', $project->project_id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button"
                                        class="action-btn bg-red-100 text-red-600 hover:bg-red-200"
                                        title="ยกเลิกโครงการ"
                                        onclick="confirmCancel('{{ $project->project_id }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107
                                                1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0
                                                1-2.244 2.077H8.084a2.25 2.25 0 0
                                                1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108
                                                0 0 0-3.478-.397m-12
                                                .562c.34-.059.68-.114
                                                1.022-.165m0 0a48.11 48.11 0 0
                                                1 3.478-.397m7.5
                                                0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964
                                                51.964 0 0 0-3.32
                                                0c-1.18.037-2.09 1.022-2.09
                                                2.201v.916m7.5 0a48.667 48.667
                                                0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </form>

                                {{-- ปุ่มปริ้นโครงการ --}}
                                <button type="button"
                                    onclick="window.open('{{ route('project.export.pdf', $project->project_id) }}', '_blank')"
                                    class="action-btn bg-yellow-100 text-yellow-600 hover:bg-yellow-200"
                                    title="พิมพ์โครงการ">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 9V3h12v6m-1.5 6.75h.008v.008H16.5v-.008zM6 15h12v6H6v-6zM3 13.5V10.2A2.2 2.2 0 0 1 5.2 8h13.6a2.2 2.2 0 0 1 2.2 2.2v3.3a.5.5 0 0 1-.5.5H3.5a.5.5 0 0 1-.5-.5z" />
                                    </svg>
                                </button>

                                {{-- ปุ่ม PDF  --}}
                                <button type="button"
                                    onclick="window.open('{{ route('project.pdf', $project->project_id) }}')"
                                    class="action-btn bg-red-100 text-red-600 hover:bg-red-200"
                                    title="เปิดไฟล์ PDF">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="#f97316"
                                        style="stroke: #f97316;"
                                        class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25
                                            0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0
                                            .621.504 1.125 1.125 1.125h12.75c.621 0
                                            1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col md:flex-row items-center justify-between text-sm text-gray-500">
                <div class="mb-2 md:mb-0">
                    แสดง <span class="font-semibold text-gray-900">1</span> ถึง <span class="font-semibold text-gray-900">10</span> จาก <span class="font-semibold text-gray-900">{{ count($projects) }}</span> โครงการ
                </div>
                <div class="inline-flex gap-1">
                    <button class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-500 hover:bg-gray-100">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="px-3 py-1 rounded-md bg-blue-600 text-white">1</button>
                    <button class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-500 hover:bg-gray-100">2</button>
                    <button class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-500 hover:bg-gray-100">3</button>
                    <button class="px-3 py-1 rounded-md bg-white border border-gray-300 text-gray-500 hover:bg-gray-100">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmCancel(projectId) {
        Swal.fire({
            title: 'ยืนยันการยกเลิก?',
            text: "คุณแน่ใจหรือไม่ว่าต้องการยกเลิกโครงการนี้",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            customClass: {
                confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium px-4 py-2 rounded',
                cancelButton: 'bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium px-4 py-2 rounded ml-2'
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`cancel-form-${projectId}`).submit();
            }
        });
    }
</script>
@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        const table = $('#project-table').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
            },
            dom: `
                <'flex flex-col md:flex-row justify-between items-start md:items-center mb-4'
                    <'flex items-center space-x-2'l>
                    <'flex items-center space-x-2'f>
                >
                t
                <'flex flex-col md:flex-row justify-between items-center mt-4'ip>
            `,
            drawCallback: function() {
                // ดึงช่องค้นหาออกมาไว้ใน container ด้านนอก
                if (!$('#custom-search-container').hasClass('filled')) {
                    $('#custom-search-container')
                        .html($('#project-table_filter').html())
                        .addClass('filled');
                    $('#project-table_filter').remove();
                }
            }
        });

        // ✅ ฟิลเตอร์สถานะ
        $('#status-filter').on('change', function() {
            const status = $(this).val();
            if (status === '') {
                table.columns(5).search('').draw();
            } else {
                table.columns(5).search('^' + status + '$', true, false).draw();
            }
        });
    });
</script>
@endsection