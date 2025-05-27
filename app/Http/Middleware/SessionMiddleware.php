<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // ตั้งค่า session เริ่มต้นถ้ายังไม่มี
        $this->initializeDefaultSession($request);

        // ตรวจสอบและจัดการ session
        $this->manageSessions($request);

        // Log session state สำหรับ debug
        $this->logSessionState($request);

        return $next($request);
    }

    /**
     * ตั้งค่า session เริ่มต้น
     */
    private function initializeDefaultSession(Request $request)
    {
        // ตั้งค่า admin_id หากยังไม่มี
        if (!session()->has('admin_id')) {
            $userId = optional(Auth::user())->id ?? 1;
            session(['admin_id' => $userId]);
        }

        // ตั้งค่า user_id ให้เท่ากับ admin_id เพื่อความเข้ากันได้
        if (!session()->has('user_id')) {
            session(['user_id' => session('admin_id')]);
        }

        // ตั้งค่า project_id จาก route parameter หากมี
        $projectId = $request->route('id') ?? $request->input('project_id');
        if ($projectId && !session()->has('project_id')) {
            session(['project_id' => $projectId]);
        }
    }

    /**
     * จัดการ sessions ให้สม่ำเสมอ
     */
    private function manageSessions(Request $request)
    {
        // sync user_id กับ admin_id
        if (session('admin_id') && session('admin_id') !== session('user_id')) {
            session(['user_id' => session('admin_id')]);
        }

        // อัปเดต project_id จาก route parameter หากมี
        $routeProjectId = $request->route('id');
        if ($routeProjectId && $routeProjectId !== session('project_id')) {
            session(['project_id' => $routeProjectId]);
        }
    }

    /**
     * Log session state สำหรับการ debug
     */
    private function logSessionState(Request $request)
    {
        Log::info('🔧 Session Middleware', [
            'route' => $request->route()->getName() ?? 'unknown',
            'method' => $request->method(),
            'project_id' => session('project_id'),
            'admin_id' => session('admin_id'),
            'user_id' => session('user_id'),
            'route_params' => $request->route()->parameters() ?? [],
        ]);
    }
}
