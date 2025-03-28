<?php


use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LibrarianCommentController;
use App\Http\Controllers\ReplyComment;




// Public Route
Route::get('/', function () {
    return view('welcome');
});

// Middleware for Authentication and Verification
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // Profile Routes
    Route::prefix('profile')->middleware('auth')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
    
   
    Route::get('documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::put('documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/sections/dashboard', [HomeController::class, 'sectionsDashboard'])->name('sectionsDashboard');

   
    Route::get('/session/check', function () {
        return response()->json(['authenticated' => Auth::check()]);
    })->name('session.check');

    
    // Document Management Routes
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('/incoming', [DocumentController::class, 'incoming'])->name('documents.incoming');
        Route::get('/outcoming', [DocumentController::class, 'outcoming'])->name('documents.outcoming');
        Route::get('/submitted', [DocumentController::class, 'submitted'])->name('documents.submitted');
        Route::get('/upload', [DocumentController::class, 'uploadForm'])->name('documents.upload');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('/view/{filename}', [DocumentController::class, 'view'])->name('documents.view');
        Route::patch('/documents/notifications/{id}/', [DocumentController::class, 'updateStatus'])->name('documents.updateStatus');
        Route::get('/search', [DocumentController::class, 'search'])->name('documents.search');
        Route::get('/sorting', [DocumentController::class, 'sorting'])->name('documents.sorting');
        Route::get('/files/{fileName}', [DocumentController::class, 'showFile'])->name('documents.file.view');
        Route::get('/ongoing', [HomeController::class, 'ongoing'])->name('documents.ongoing');
   

        Route::get('/filter-documents', [DocumentController::class, 'filterDocuments'])->name('filter.documents');

    });

});


Route::get('/get-selected-sections/{documentId}', [DocumentController::class, 'getSelectedSections']);
Route::post('/documents/{id}/save-sections', [DocumentController::class, 'saveSections']);


Route::middleware('auth')->group(function () {
Route::get('/notifications', [NotificationController::class, 'fetchNotifications']);
Route::post('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead']);

});


Route::middleware(['auth'])->group(function () {
    // Store comment
    Route::post('/documents/{documentId}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/documents/{documentId}/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::get('/documents/{documentId}/comments', [DocumentController::class, 'getComments']);

});

// Route::post('/documents/{id}/reply', [DocumentController::class, 'reply'])->name('documents.reply');

// Route::middleware(['auth'])->group(function () {
//   // LIBRARIAN COMMENTS
// Route::post('/comments', [LibrarianCommentController::class, 'store'])->name('comments.store');
// Route::put('/comments/{commentId}', [LibrarianCommentController::class, 'update'])->name('comments.update');
// });




Route::middleware(['auth'])->group(function () {
Route::get('/documents/reject', [ApprovalController::class, 'reject'])->name('documents.reject');
Route::get('/documents/approved', [ApprovalController::class, 'approved'])->name('documents.approved');
Route::get('/documents/accomplished', [ApprovalController::class, 'completed'])->name('documents.completed');

});


Route::middleware(['auth'])->group(function () {
    Route::patch('/documents/{id}/update-status', [DocumentController::class, 'updateStatus'])->name('documents.updateStatus');
});

// In web.php or routes file
Route::middleware(['auth'])->group(function () {
    Route::get('/documents/details/{id}', [DocumentController::class, 'show'])->name('document.show');
});

// Admin Dashboard Route (Admin Only)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/adminDashboard', [AdminController::class, 'dashboard'])->name('admin.adminDashboard');
    Route::get('/users', [AdminController::class, 'listUsers'])->name('admin.users');
    Route::get('/create-user', [AdminController::class, 'createUserForm'])->name('admin.createUser');
    
   
 
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/users/{selectedUser}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::patch('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
});



Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('users/create', [AdminController::class, 'create'])->name('users.create');
    Route::post('users', [AdminController::class, 'store'])->name('users.store');

    Route::get('/create-section', [AdminController::class, 'createSection'])->name('sections.create');
    Route::post('/sections', [AdminController::class, 'storeSection'])->name('sections.store');
    Route::get('/sections/{userLevel}', [AdminController::class, 'fetchSections'])->name('sections.get');
    Route::post('/admin/sections/delete', [AdminController::class, 'deleteSection'])->name('admin.sections.delete');
    Route::post('/admin/sections/update/{id}', [AdminController::class, 'updateSection'])->name('sections.update');



});



// Section Dashboard Route
Route::get('/section/dashboard', [HomeController::class, 'sectionsDashboard'])
    ->name('section.dashboard')
    ->middleware('auth')
    ->middleware(\App\Http\Middleware\Sections::class);



// Boss Dashboard Route (Boss Only)
Route::middleware(['auth', 'boss'])->group(function () {
    Route::get('/boss/dashboard', [HomeController::class, 'bossDashboard'])->name('boss.dashboard');
});

Route::post('/send-selected-sections', [DocumentController::class, 'storeSections']);





// Access Denied Route
Route::get('/access', function () {
    return view('access');
})->name('access-denied');

// Auth Routes (Generated by Laravel)
require __DIR__.'/auth.php';
