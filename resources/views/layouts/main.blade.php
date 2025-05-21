<!DOCTYPE html>
<html lang="th">
@vite(['resources/css/app.css', 'resources/js/app.js'])

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ระบบงานนโยบายและแผน')</title>



    <!-- Default CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- SideBar CSS -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

    <!-- Header CSS -->
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">

    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

    <link href="https://unpkg.com/tabulator-tables@5.4.4/dist/css/tabulator.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@5.4.4/dist/js/tabulator.min.js"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    @yield('head')
</head>

<body class="bg-light">

    {{-- Header --}}
    <header class="custom-header">
        {{-- Burger menu --}}
        <a href="#" id="burgermenu" class="burger-icon">

        </a>



        {{-- Profile dropdown --}}
        <div class="profile-dropdown">
            <button type="button" class="profile-button" id="user-menu-button">
                <span class="user-name">@yield('user_name', 'ชื่อผู้ใช้')</span>
                <i class="fa-solid fa-chevron-down"></i>
            </button>

            {{-- Dropdown --}}
            <div class="profile-menu d-none" id="user-detail">
                <div class="profile-info">
                    <h6>👤 ผู้ใช้งาน</h6>
                    <p><strong>ชื่อ:</strong> @yield('user_name', 'ชื่อผู้ใช้')</p>
                    <p><strong>หน่วยงาน:</strong> @yield('user_unit', 'ไม่ระบุ')</p>
                    <p><strong>สิทธิ:</strong> @yield('user_role', 'ผู้ใช้ทั่วไป')</p>
                </div>
                <div class="profile-links">
                    <a href="#"><i class="fa-solid fa-user-pen text-primary"></i> แก้ไขโปรไฟล์</a>
                    <a href="#"><i class="fa-solid fa-gear text-secondary"></i> การตั้งค่า</a>
                    <a href="#"><i class="fa-solid fa-circle-question text-info"></i> ศูนย์ช่วยเหลือ</a>
                    <a href="{{ env('APP_URL') }}/api/logout" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> ออกจากระบบ</a>
                </div>
            </div>
        </div>
    </header>


    {{-- SideBar --}}
    <aside id="sidebar"
        class="sidebar-container bg-white border-end vh-100 position-fixed top-0 start-0 px-3 sidebar-hidden"
        style="width: 280px; transition: transform 0.3s ease-in-out; z-index: 1040;">

        <div class="sidebar-wrapper d-flex flex-column h-100 py-4">

            {{-- โลโก้ด้านบน --}}
            <div class="text-center mb-4">
                <a href="{{ url('/form1') }}" class="d-block text-decoration-none">
                    <div style="display: flex; justify-content: center;">
                        <img src="{{ asset('/images/planprj_logo.png') }}" alt="logo"
                            style="height: 150px; width: auto;" class="mb-2">
                    </div>
                    <div class="fw-bold text-dark" style="font-size: clamp(14px, 1.6vw, 18px);">
                        ระบบงานนโยบายและแผน
                    </div>
                </a>
            </div>

            {{-- ช่องว่างเมนูกลาง (ยืด) --}}
            <div class="flex-grow-1"></div>

            {{-- เมนูด้านล่างติดขอบ --}}
            <div class="sidebar-bottom text-center">
                <a href="/" class="menu-icon-vertical d-block py-3 {{ request()->is('/') ? 'active' : '' }}">
                    <i class="fa-solid fa-house fa-xl"></i>
                    <div>หน้าแรก</div>
                </a>
                <a href="{{ env('APP_URL') }}/api/logout" class="menu-icon-vertical d-block py-3">
                    <i class="fa-solid fa-right-from-bracket fa-xl"></i>
                    <div>ออกจากระบบ</div>
                </a>
            </div>

        </div>
    </aside>

    {{-- Main Content --}}
    <main class="main-content p-4" style="margin-left: 280px;">
        @yield('sidebar') {{-- For forms or views with extra content --}}
        @yield('content')
    </main>

    {{-- Scripts --}}

    @stack('scripts')

    <script src="{{ asset('js/custom.js') }}"></script>

    <script src="{{ asset('js/table.js') }}"></script>

    <script src="{{ asset('js/alert.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('user-menu-button');
            const dropdown = document.getElementById('user-detail');

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('d-none');
            });

            document.addEventListener('click', function(event) {
                const isClickInside = btn.contains(event.target) || dropdown.contains(event.target);
                if (!isClickInside) {
                    dropdown.classList.add('d-none');
                }
            });
        });
    </script>
</body>

</html>
