<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait SessionHelper
{
    /**
     * ตั้งค่า session เริ่มต้นสำหรับ form
     */
    protected function initializeSession($projectId = null, $userId = null)
    {
        // ตั้งค่า project_id
        if ($projectId && !session()->has('project_id')) {
            session(['project_id' => $projectId]);
        } elseif (!session()->has('project_id')) {
            session(['project_id' => 1]); // default
        }

        // ตั้งค่า user_id/admin_id
        if ($userId) {
            session(['admin_id' => $userId]);
        } elseif (!session()->has('admin_id')) {
            $currentUserId = optional(Auth::user())->id ?? 1;
            session(['admin_id' => $currentUserId]);
        }

        // สำหรับความเข้ากันได้เก่า
        if (!session()->has('user_id')) {
            session(['user_id' => session('admin_id')]);
        }

        Log::info('Session initialized', [
            'project_id' => session('project_id'),
            'admin_id' => session('admin_id'),
            'user_id' => session('user_id')
        ]);
    }

    /**
     * ตรวจสอบว่า session ถูกต้องหรือไม่
     */
    protected function validateSession()
    {
        $projectId = session('project_id');
        $userId = session('admin_id') ?? session('user_id');

        if (empty($projectId)) {
            Log::error('❌ Project ID is missing from session');
            return redirect()->route('form1.show')
                ->with('error', 'กรุณากรอกข้อมูลหน้าแรกก่อน');
        }

        if (empty($userId)) {
            Log::error('❌ User ID is missing from session');
            return redirect()->route('form1.show')
                ->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        return null; // session valid
    }

    /**
     * ดึงค่า session สำหรับใช้งาน
     */
    protected function getSessionData()
    {
        return [
            'project_id' => session('project_id'),
            'edit_id' => session('admin_id') ?? session('user_id') ?? 1,
            'user_id' => session('user_id', session('admin_id', 1))
        ];
    }

    /**
     * แสดง session debug info
     */
    protected function debugSession($method = '')
    {
        Log::info("🔍 Session Debug - {$method}", [
            'project_id' => session('project_id'),
            'admin_id' => session('admin_id'),
            'user_id' => session('user_id'),
            'all_session' => session()->all()
        ]);
    }
}
