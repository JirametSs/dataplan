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



<!-- {{-- Header --}}
    <header class="custom-header">
        {{-- Burger menu --}}
        <a href="#" id="burgermenu" class="burger-icon">

        </a> -->
<!-- {{-- Profile dropdown --}}
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
</div> -->
</header>

<aside id="sidebar"
    class="sidebar-container position-fixed top-0 start-0 vh-100 px-2"
    style="width: 180px;
           z-index: 1040;
           background: linear-gradient(180deg, #065F46 0%, #064E3B 100%);
           border-right: 1px solid rgba(52, 211, 153, 0.2);
           box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
           transition: all 0.3s ease-in-out;">

    <div class="sidebar-wrapper d-flex flex-column h-100 py-2">
        {{-- โลโก้ --}}
        <div class="text-center mb-2">
            <a href="{{ url('/form1') }}" class="d-block text-decoration-none logo-hover">
                <div class="logo-container position-relative mb-2"
                    style="height: 72px; width: 72px; margin: 0 auto;">
                    <img src="{{ asset('/images/planprj_logo.png') }}"
                        alt="logo"
                        class="logo-img"
                        style="height: 100%; width: 100%; object-fit: contain; transition: transform 0.2s ease;">
                    <div class="logo-overlay"
                        style="position: absolute;
                               top: 0; left: 0;
                               width: 100%; height: 100%;
                               background: radial-gradient(circle at center, rgba(52, 211, 153, 0.12) 0%, transparent 35%);
                               background-size: 70% 70%;
                               background-repeat: no-repeat;
                               background-position: center;
                               opacity: 0;
                               transition: opacity 0.2s ease;">
                    </div>
                </div>
                <div class="fw-semibold"
                    style="color: #F0FDF4;
                           font-size: 0.8rem;
                           line-height: 1.2;
                           text-shadow: 0 1px 1px rgba(0,0,0,0.1);">
                    ระบบโครงการ<br>MED CMU
                </div>
            </a>
        </div>

        <div class="divider mb-2"
            style="height: 1px;
                   background: linear-gradient(90deg, transparent 0%, #34D399 50%, transparent 100%);">
        </div>

        {{-- เมนู --}}
        <div class="sidebar-bottom text-center pt-2">
            <a href="/"
                class="menu-icon-vertical d-block py-1 px-2 mb-2 rounded-3"
                style="font-size: 0.75rem;
                       color: white;
                       text-decoration: none;
                       background: rgba(255, 255, 255, 0.04);
                       border: 1px solid rgba(255, 255, 255, 0.08);
                       box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);">
                <i class="fa-solid fa-house me-1" style="font-size: 14px;"></i> หน้าแรก
            </a>

            <a href="{{ env('APP_URL') }}/api/logout"
                class="menu-icon-vertical d-block py-1 px-2 rounded-3 logout-btn"
                style="font-size: 0.75rem;
                       color: white;
                       background: rgba(255, 71, 87, 0.15);
                       border: 1px solid rgba(255, 71, 87, 0.2);
                       box-shadow: 0 2px 4px rgba(255, 71, 87, 0.08);">
                <i class="fa-solid fa-right-from-bracket me-1" style="font-size: 14px;"></i> ออก
            </a>
        </div>
    </div>
</aside>
<style>
    /* เอฟเฟกต์ hover แบบเรียบหรู */
    .nav-link:hover {
        background: rgba(52, 211, 153, 0.1) !important;
        border-left: 2px solid #34D399 !important;
        transform: translateX(3px);
        transition: all 0.2s ease-in-out;
    }

    .logo-hover:hover .logo-img {
        transform: scale(1.02);
        transition: transform 0.2s ease-in-out;
    }

    .logo-hover:hover .logo-overlay {
        opacity: 0.8;
        transition: opacity 0.2s ease-in-out;
    }

    .logo-hover:hover div {
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .menu-icon-vertical:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
        transition: all 0.2s ease-in-out;
    }

    .menu-icon-vertical:hover i {
        transform: scale(1.05) !important;
        transition: transform 0.2s ease-in-out;
    }

    .logout-btn:hover {
        background: rgba(255, 71, 87, 0.15) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 8px rgba(255, 71, 87, 0.15) !important;
        transition: all 0.2s ease-in-out;
    }

    .logout-btn:hover i {
        animation: shake 0.4s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-2px);
        }

        50% {
            transform: translateX(2px);
        }

        75% {
            transform: translateX(-1px);
        }
    }
</style>

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

<!-- <script>
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
</script> -->


</html>