<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Form1Controller;
use App\Http\Controllers\Form2Controller;
use App\Http\Controllers\Form3Controller;
use App\Http\Controllers\Form4Controller;
use App\Http\Controllers\Form5Controller;
use App\Http\Controllers\Form6Controller;
use App\Http\Controllers\Form7Controller;
use App\Http\Controllers\Form8Controller;
use App\Http\Controllers\Form9Controller;
use App\Http\Controllers\DashboardController;

//homepage
Route::get('/', [Form1Controller::class, 'showForm'])->name('form1.show');

//form 1
Route::get('/form1', [Form1Controller::class, 'showForm'])->name('form1.show');
Route::post('/form1/store', [Form1Controller::class, 'store'])->name('form1.store');
Route::get('/form1/edit/{id}', [App\Http\Controllers\Form1Controller::class, 'edit'])->name('form1.edit');
Route::put('/form1/edit/{id}', [Form1Controller::class, 'update'])->name('form1.edit');
Route::get('/api/get-employee/{tjobId}', [Form1Controller::class, 'getEmployeeByTjobId']);
Route::get('/form1/searchEmployee', [Form1Controller::class, 'searchEmployee'])->name('form1.searchEmployee');

//form 2
Route::get('/form2', [Form2Controller::class, 'showForm'])->name('form2.show');
Route::post('/form2', [Form2Controller::class, 'store'])->name('form2.store');
Route::get('/form2/edit/{id}', [Form2Controller::class, 'edit'])->name('form2.edit');
Route::put('/form2/update/{id}', [Form2Controller::class, 'update'])->name('form2.update');
//form 3
Route::get('/form3', [Form3Controller::class, 'showForm'])->name('form3.show');
Route::post('/form3', [Form3Controller::class, 'store'])->name('form3.store');
Route::get('/form3/edit/{id}', [Form3Controller::class, 'edit'])->name('form3.edit');
Route::put('/form3/update/{id}', [Form3Controller::class, 'update'])->name('form3.update');

//form 4
Route::get('/form4', [Form4Controller::class, 'showForm'])->name('form4.show');
Route::post('/form4', [Form4Controller::class, 'store'])->name('form4.store');
Route::get('/form4/edit/{id}', [Form4Controller::class, 'edit'])->name('form4.edit');
Route::put('/form4/update/{id}', [Form4Controller::class, 'update'])->name('form4.update');

//form 5
Route::get('/form5', [Form5Controller::class, 'showForm'])->name('form5.show');
Route::post('/form5', [Form5Controller::class, 'store'])->name('form5.store');
Route::get('/form5/edit/{id}', [Form5Controller::class, 'edit'])->name('form5.edit');
Route::put('/form5/update/{id}', [Form5Controller::class, 'update'])->name('form5.update');

//form 6
Route::get('/form6', [Form6Controller::class, 'showForm'])->name('form6.show');
Route::post('/form6/store', [Form6Controller::class, 'store'])->name('form6.store');
Route::get('/form6/edit/{id}', [Form6Controller::class, 'edit'])->name('form6.edit');
Route::put('/form6/update/{id}', [Form6Controller::class, 'update'])->name('form6.update');
//form 7
Route::get('/form7', [Form7Controller::class, 'showForm'])->name('form7.show');
Route::get('/form7/edit/{id}', [Form7Controller::class, 'edit'])->name('form7.edit');
Route::post('/form7', [Form7Controller::class, 'store'])->name('form7.store');
Route::put('/form7/{id}', [Form7Controller::class, 'update'])->name('form7.update');

//form 8
Route::get('/form8', [Form8Controller::class, 'showForm'])->name('form8.show');
Route::post('/form8/store', [Form8Controller::class, 'store'])->name('form8.store');
Route::get('/form8/edit/{id}', [Form8Controller::class, 'edit'])->name('form8.edit');
Route::put('/form8/edit/{id}', [Form8Controller::class, 'update'])->name('form8.update');

//form 9
Route::get('/form9', [Form9Controller::class, 'showForm'])->name('form9.show');
Route::post('/form9/store', [Form9Controller::class, 'store'])->name('form9.store');
Route::get('/form9/edit/{id}', [Form9Controller::class, 'edit'])->name('form9.edit');
Route::put('/form9/update/{id}', [Form9Controller::class, 'update'])->name('form9.update');

//Dashboard
Route::get('/dashboard', [DashboardController::class, 'showForm'])->name('dashboard.show');
Route::patch('/project/cancel/{id}', [DashboardController::class, 'destroy'])->name('project.cancel');
Route::get('/project/export/pdf/{id}', [DashboardController::class, 'exportFullPdf'])->name('project.export.pdf');
Route::get('/project/stream/pdf/{id}', [DashboardController::class, 'StreamFullPdf'])->name('project.pdf');
