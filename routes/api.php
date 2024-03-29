<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\ExamLaunchController;
use App\Http\Controllers\Admin\EditController;
use App\Http\Controllers\Admin\DeleteController;

use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Teacher\ExamController;
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\StudentRatedController;

use App\Http\Controllers\Supervisor\SupervisorController;
use App\Http\Controllers\Supervisor\ExamStartController;

use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\AnswerController;


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('admin')->group(function () {
    Route::post('login', [AdminController::class, 'login']);
});

Route::prefix('teacher')->group(function () {
    Route::post('login', [TeacherController::class, 'login']);
});

Route::prefix('student')->group(function () {
    Route::post('login', [StudentController::class, 'login']);
});

Route::prefix('supervisor')->group(function () {
    Route::post('login', [SupervisorController::class, 'login']);
});

Route::get('admin/refresh', [AdminController::class, 'refresh']);
Route::get('teacher/refresh', [TeacherController::class, 'refresh']);
Route::get('supervisor/refresh', [SupervisorController::class, 'refresh']);
Route::get('student/refresh', [StudentController::class, 'refresh']);

Route::middleware(['api'])->group(function() {
    // ADMIN
    Route::prefix('admin')->middleware(['assign.guard:admin'])->group(function () {
        Route::get('logout', [AdminController::class, 'logout']);
        Route::get('get-user', [AdminController::class, 'getAuthenticatedUser']);

        // GET DATA
        Route::get('rooms', [ImportController::class, 'getRoom']);
        Route::get('schedules', [ImportController::class, 'getSchedule']);
        Route::get('student-schedule', [ImportController::class, 'getStudentSchedule']);
        Route::get('students', [ImportController::class, 'getStudent']);
        Route::get('supervisors', [ImportController::class, 'getSupervisor']);
        Route::get('teachers', [ImportController::class, 'getTeacher']);
        
        // IMPORT
        Route::prefix('import')->group(function () {
            Route::post('/rooms', [ImportController::class, 'importRoom']);
            Route::post('/schedules', [ImportController::class, 'importSchedule']);
            Route::post('/student-schedule', [ImportController::class, 'importStudentSchedule']);
            Route::post('/students', [ImportController::class, 'importStudent']);
            Route::post('/supervisors', [ImportController::class, 'importSupervisor']);
            Route::post('/teachers', [ImportController::class, 'importTeacher']);
        });

        // EDIT
        Route::prefix('edit')->group(function () {
            Route::post('teacher/{id}', [EditController::class, 'editTeacher']);
            Route::post('student/{id}', [EditController::class, 'editStudent']);
            Route::post('supervisor/{id}', [EditController::class, 'editSupervisor']);
            Route::post('room/{id}', [EditController::class, 'editRoom']);
        });

        // DELETE
        Route::prefix('delete')->group(function () {
            Route::delete('teacher/{id}', [DeleteController::class, 'deleteTeacher']);
            Route::delete('student/{id}', [DeleteController::class, 'deleteStudent']);
            Route::delete('supervisor/{id}', [DeleteController::class, 'deleteSupervisor']);
            Route::delete('room/{id}', [DeleteController::class, 'deleteRoom']);
            Route::delete('schedule/{id}', [DeleteController::class, 'deleteSchedule']);
        });

        // EXAM
        Route::prefix('exam')->group(function () {
            Route::get('/', [ExamLaunchController::class, 'index']);
            Route::post('/trigger/{type}', [ExamLaunchController::class, 'trigger']);
            Route::post('/trigger-rated/{type}', [ExamLaunchController::class, 'triggerRated']);
        });
    });

    // TEACHER
    Route::prefix('teacher')->middleware(['assign.guard:teacher'])->group(function () {
        Route::get('logout', [TeacherController::class, 'logout']);
        Route::get('get-user', [TeacherController::class, 'getAuthenticatedUser']);

        Route::prefix('exam')->group(function () {
            Route::get('/', [ExamController::class, 'index']);
            Route::get('/class/{class}', [ExamController::class, 'showByClass']);
            Route::post('/add', [ExamController::class, 'add']);
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

        Route::prefix('rated')->group(function () {
            Route::get('/', [StudentRatedController::class, 'index']);
            Route::get('/export/{id}', [StudentRatedController::class, 'export']);
            Route::get('/student/{id}/{class}', [StudentRatedController::class, 'detailStudent']);
            Route::get('/student-detail/{studentId}/{examId}', [StudentRatedController::class, 'detailRated']);
            Route::post('/student-rated/{id}', [StudentRatedController::class, 'updateRated']);
        });
    });

    // SUPERVISOR
    Route::prefix('supervisor')->middleware(['assign.guard:supervisor'])->group(function () {
        Route::get('logout', [SupervisorController::class, 'logout']);
        Route::get('get-user', [SupervisorController::class, 'getAuthenticatedUser']);

        Route::prefix('exam')->group(function () {
            Route::get('/', [ExamStartController::class, 'index']);
            Route::post('/start/{id}', [ExamStartController::class, 'start']);
            Route::post('/stop/{id}', [ExamStartController::class, 'stop']);
        });

        Route::prefix('student')->group(function () {
            Route::get('/{id}', [ExamStartController::class, 'student']);
        });
    });

    // STUDENT
    Route::prefix('student')->middleware(['assign.guard:student'])->group(function () {
        Route::get('logout', [StudentController::class, 'logout']);
        Route::get('get-user', [StudentController::class, 'getAuthenticatedUser']);

        Route::get('/exam-launched', [AnswerController::class, 'examLaunched']);
        Route::get('/exam-finished', [AnswerController::class, 'examFinished']);
        Route::post('/token/{id}', [AnswerController::class, 'token']);
        Route::post('/answer', [AnswerController::class, 'answer']);
        Route::post('/end-exam', [AnswerController::class, 'endExam']);
        Route::post('/block', [AnswerController::class, 'block']);
        Route::post('/open-block', [AnswerController::class, 'openBlock']);

        Route::get('/rated/{id}', [AnswerController::class, 'rated']);
    });
});