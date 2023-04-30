<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminController;

use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Teacher\ExamController;
use App\Http\Controllers\Teacher\QuestionController;

use App\Http\Controllers\Supervisor\SupervisorController;
use App\Http\Controllers\Supervisior\ExamLaunchController;

use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\AnswerController;


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('admin')->group(function () {
    Route::post('login', [AdminController::class, 'login']);
    Route::get('logout', [AdminController::class, 'logout']);
});

Route::prefix('teacher')->group(function () {
    Route::post('login', [TeacherController::class, 'login']);
    Route::get('logout', [TeacherController::class, 'logout']);
});

Route::prefix('student')->group(function () {
    Route::post('login', [StudentController::class, 'login']);
    Route::get('logout', [StudentController::class, 'logout']);
});

Route::prefix('supervisor')->group(function () {
    Route::post('login', [SupervisorController::class, 'login']);
    Route::get('logout', [SupervisorController::class, 'logout']);
});

// Route::middleware(['api'])->group(function() {
    // ADMIN
    Route::prefix('admin')->middleware(['assign.guard:admin'])->group(function () {
        Route::get('refresh', [AdminController::class, 'refresh']);
        Route::get('get-user', [AdminController::class, 'getAuthenticatedUser']);
    });

    // TEACHER
    Route::prefix('teacher')->middleware(['assign.guard:teacher'])->group(function () {
        Route::get('refresh', [TeacherController::class, 'refresh']);
        Route::get('get-user', [TeacherController::class, 'getAuthenticatedUser']);

        Route::prefix('exam')->group(function () {
            Route::get('/', [ExamController::class, 'index']);
            Route::get('/class/{class}', [ExamController::class, 'showByClass']);
            Route::post('/add', [ExamController::class, 'add']);
            Route::get('/detail/{id}', [ExamController::class, 'detail']);
            Route::post('/edit/{id}', [ExamController::class, 'edit']);
            Route::delete('/delete/{id}', [ExamController::class, 'delete']);
            Route::post('/launch/{id}', [ExamController::class, 'launch']);
            Route::post('/stop', [ExamController::class, 'stop']);
            Route::get('/answer/{examId}/{studentId}', [ExamController::class, 'answer']);
        });

        Route::prefix('exam/question')->group(function () {
            Route::get('/{id}', [QuestionController::class, 'index']);
            Route::post('/add', [QuestionController::class, 'add']);
            Route::post('/edit/{id}', [QuestionController::class, 'edit']);
            Route::delete('/delete/{id}', [QuestionController::class, 'delete']);
        });
    });

    // SUPERVISOR
    Route::prefix('supervisor')->middleware(['assign.guard:supervisor'])->group(function () {
        Route::get('refresh', [SupervisorController::class, 'refresh']);
        Route::get('get-user', [SupervisorController::class, 'getAuthenticatedUser']);

        Route::prefix('exam')->group(function () {
            Route::post('/launch/{id}', [ExamLaunchController::class, 'launch']);
            Route::post('/stop', [ExamLaunchController::class, 'stop']);
        });
    });

    // STUDENT
    Route::prefix('student')->middleware(['assign.guard:student'])->group(function () {
        Route::get('refresh', [StudentController::class, 'refresh']);
        Route::get('get-user', [StudentController::class, 'getAuthenticatedUser']);

        Route::get('/generate/{id}', [AnswerController::class, 'exam']);
        Route::post('/answer', [AnswerController::class, 'answer']);
        Route::post('/end-exam', [AnswerController::class, 'Exam']);
    });
// });