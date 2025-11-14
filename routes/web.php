<?php

use App\Http\Controllers\BooksApiController;
use App\Http\Controllers\CkeditorUploadController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseChapterController;
use App\Http\Controllers\CourseLessonController;
use App\Http\Controllers\CourseLearnController;
use App\Http\Controllers\CustomPageController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\VerifyEmailNotificationController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookPublicController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\ContactMessageController as AdminContact;
use App\Http\Controllers\PromotionController;
use Illuminate\Support\Facades\Artisan;
use App\Services\SessionCart;

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return '<h3 style="font-family:sans-serif;color:green;">✅ All cache cleared successfully!</h3>';
})->name('clear.cache');

/*
|--------------------------------------------------------------------------
| Public Frontend (no auth)
|--------------------------------------------------------------------------
*/
Route::get('/', [FrontendController::class, 'index'])->name('frontend.index');
//Courses list
Route::get('/courses', [FrontendController::class, 'courses'])->name('courses.list');
Route::get('/courses/{course:slug}', [FrontendController::class, 'show'])->name('courses.show');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');

/*
|--------------------------------------------------------------------------
| 🛒 Cart Routes (Session Based)
|--------------------------------------------------------------------------
*/

// Add item to cart (via form POST)
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

// Update quantity
Route::post('/cart/update-qty', [CartController::class, 'updateQty'])->name('cart.updateQty');

// Remove a single item
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// Clear all cart items
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');


/*
|--------------------------------------------------------------------------
| 💳 Checkout Routes (Cart-based)
|--------------------------------------------------------------------------
*/

// Show checkout page (shows all session cart items)
Route::get('/checkout', [CheckoutController::class, 'page'])->name('checkout.page');

// Submit checkout form (proceed to payment or success)
Route::post('/checkout', [CheckoutController::class, 'submit'])->name('checkout.submit');


/*
|--------------------------------------------------------------------------
| 🔁 Legacy Compatibility (optional)
|--------------------------------------------------------------------------
*/
Route::get('/checkout/{type}/{slug}', function (string $type, string $slug, SessionCart $cart) {
    abort_unless(in_array($type, ['course', 'book'], true), 404);
    $cart->add($type, $slug, 1);
    return redirect()->route('checkout.page')->with('success', 'Item added to cart.');
})->where('type', 'course|book')->name('checkout.add.direct');

// Optional: allow /checkout/{slug} (defaults to course)
Route::get('/checkout/{slug}', function (string $slug, SessionCart $cart) {
    $cart->add('course', $slug, 1);
    return redirect()->route('checkout.page')->with('success', 'Course added to cart.');
})->name('checkout.add.course');

Route::post('books/{book:slug}/buy', [BookPublicController::class, 'buy'])->name('books.buy');

Route::get('/books/paginate', [BooksApiController::class, 'index'])->name('books.paginate');

Route::get('/lang/{locale}', function ($locale) {
    abort_unless(in_array($locale, ['ar','en'], true), 404);
    session([
        'locale' => $locale,
        'dir'    => $locale === 'ar' ? 'rtl' : 'ltr',
    ]);
    return back();
})->name('lang.switch');

// ---------- Password Reset ----------
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])
        ->name('password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'edit'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'update'])
        ->name('password.update');

    // Legacy URL support (optional)
    Route::get('/password/reset', fn () => redirect()->route('password.request'))
        ->name('password.request.legacy');
});

Auth::routes(['reset' => false, 'verify' => false]);
    // ---------- Email Verification ----------
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->middleware('auth')
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['auth', 'signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [VerifyEmailNotificationController::class, 'send'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.send');

Route::middleware('auth')->group(function () {

    //Dashboard
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Route::post('/courses/{course}/enroll', [FrontendController::class, 'enroll'])
    //     ->name('courses.enroll');
        
    // Route::post('/cart/{courseId}', [CartController::class, 'addToCart'])->name('cart.add');
    // Route::get('/cart', [CartController::class, 'cart'])->name('cart.view');
    // Route::delete('/cart/remove/{cartItem}', [CartController::class, 'removeFromCart'])->name('cart.remove');

    Route::get('/learn/{course}', [LearningController::class, 'show'])
        ->name('learn.course');

    // Specific lesson
    Route::get('/learn/{course}/lesson/{lesson}', [LearningController::class, 'lesson'])
        ->name('learn.lesson');

    // Mark complete
    Route::post('/learn/lesson/{lesson}/complete', [LearningController::class, 'complete'])
        ->name('learn.lesson.complete');

    // Save progress (AJAX)
    Route::post('/learn/progress/save', [LearningController::class, 'saveProgress'])
        ->name('learn.progress.save');

    Route::get('/p/{slug}', [PageController::class,'show'])->name('pages.show');

    // Profile
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('books/{book:slug}/preview', [BookPublicController::class, 'preview'])->name('books.preview');
    Route::get('books/{book:slug}/download', [BookPublicController::class, 'download'])->name('books.download');

    Route::get('library', [BookController::class, 'purchase_book'])->name('library');

});

/*
|--------------------------------------------------------------------------
| Auth (login, register, password reset, verify email*)
|--------------------------------------------------------------------------
| Provided by laravel/ui (ui:auth).
| with: Auth::routes(['register' => false]);
*/
Auth::routes(); // login, register, password reset, email verify (if enabled)

/*
|--------------------------------------------------------------------------
| Protected Area (must be logged in)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    
    // Courses CRUD
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');


   // Chapter routes
    Route::get('courses/{course}/chapters', [CourseChapterController::class, 'index'])->name('courses.chapters.index');
    Route::post('courses/{course}/chapters', [CourseChapterController::class, 'store'])->name('courses.chapters.store');
    Route::put('courses/{course}/chapters/{chapter}', [CourseChapterController::class, 'update'])->name('courses.chapters.update');
    Route::delete('courses/{course}/chapters/{chapter}', [CourseChapterController::class, 'destroy'])->name('courses.chapters.destroy');

    // Lesson routes
    Route::get('courses/{course}/chapters/{chapter}/lessons', [CourseLessonController::class, 'index'])->name('courses.lessons.index');
    Route::post('courses/{course}/chapters/{chapter}/lessons', [CourseLessonController::class, 'store'])->name('courses.lessons.store');
    Route::put('courses/{course}/chapters/{chapter}/lessons/{lesson}', [CourseLessonController::class, 'update'])->name('courses.lessons.update');
    Route::delete('courses/{course}/chapters/{chapter}/lessons/{lesson}', [CourseLessonController::class, 'destroy'])->name('courses.lessons.destroy');

    Route::post('/courses/{course}/enroll', [CourseLearnController::class, 'enroll'])->name('courses.enroll');

    // Player
    Route::get('/learn/courses/{course}', [CourseLearnController::class, 'index'])->name('learn.course');
    Route::get('/learn/courses/{course}/lesson/{lesson}', [CourseLearnController::class, 'play'])->name('learn.lesson');

    // Progress APIs
    Route::post('/learn/courses/{course}/lesson/{lesson}/progress', [CourseLearnController::class, 'saveProgress'])->name('learn.lesson.progress');
    Route::post('/learn/courses/{course}/lesson/{lesson}/complete', [CourseLearnController::class, 'markComplete'])->name('learn.lesson.complete');

    Route::get('students', [StudentController::class, 'page'])->name('students.page');
    Route::get('students/activity/{user}', [StudentController::class, 'studentActivity'])
    ->name('students.activity.user');
    
    Route::get('/enrollments/assign', [StudentController::class, 'create'])->name('enrollments.assign');

    Route::post('/enrollments', [StudentController::class, 'store'])->name('enrollments.store');
    Route::post('/enrollments/bulk', [StudentController::class, 'bulkStore'])->name('enrollments.bulkStore');
    Route::delete('/enrollments/{enrollment}', [StudentController::class, 'destroy'])->name('enrollments.destroy');

    // AJAX helpers
    Route::get('/students/search', [StudentController::class, 'ajaxStudentSearch'])->name('students.ajaxSearch'); // q=
    Route::get('/courses/search',  [StudentController::class, 'ajaxCourseSearch'])->name('courses.ajaxSearch');   // q=
    Route::get('/enrollments/export', [StudentController::class, 'export'])->name('enrollments.export'); // CSV

    Route::get('settings', [SettingController::class,'index'])->name('settings.index');
    Route::post('settings/save', [SettingController::class,'save'])->name('settings.save');

        // Dropzone endpoints
    Route::post('settings/upload', [SettingController::class,'upload'])->name('settings.upload');
    Route::delete('settings/file', [SettingController::class,'destroyFile'])->name('settings.file.destroy');


    Route::get('custom-pages',              [CustomPageController::class, 'index'])->name('custom-pages.index');
    Route::get('custom-pages/create',       [CustomPageController::class, 'create'])->name('custom-pages.create');
    Route::post('custom-pages',             [CustomPageController::class, 'store'])->name('custom-pages.store');
    Route::get('custom-pages/{custom_page}/edit', [CustomPageController::class, 'edit'])->name('custom-pages.edit');
    Route::put('custom-pages/{custom_page}',      [CustomPageController::class, 'update'])->name('custom-pages.update');
    Route::delete('custom-pages/{custom_page}',   [CustomPageController::class, 'destroy'])->name('custom-pages.destroy');

    Route::post('ckeditor/upload', [CkeditorUploadController::class, 'store'])
    ->name('ckeditor.upload');

    Route::resource('books', BookController::class);
    Route::get('books/{book}/preview', [BookController::class, 'preview'])->name('books.preview');

    //Promotion
    Route::get('promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('promotions/create', [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('promotions/store', [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('promotions/{id}/edit', [PromotionController::class, 'edit'])->name('promotions.edit');
    Route::post('promotions/{id}/update', [PromotionController::class, 'update'])->name('promotions.update');
    Route::post('promotions/destroy/{id}', [PromotionController::class, 'destroy'])->name('promotions.destroy');
    Route::post('promotions/{id}/update-status', [PromotionController::class, 'updateStatus']);
    Route::get('check-promotion/{course_id}', [PromotionController::class, 'checkPromotion']);
    Route::post('promotions/deactivate-old', [PromotionController::class, 'deactivateOldPromotions']);

});



Route::middleware(['auth' , 'admin']) // add 'can:manage-contacts' if you use policies
    ->prefix('admin/contacts')
    ->name('admin.contacts.')
    ->group(function () {
        Route::get('/', [AdminContact::class, 'index'])->name('index');
        Route::get('/{contact}', [AdminContact::class, 'show'])->name('show');

        Route::post('/{contact}/toggle-star', [AdminContact::class, 'toggleStar'])->name('toggle-star');
        Route::post('/{contact}/status', [AdminContact::class, 'setStatus'])->name('set-status');
        Route::post('/{contact}/reply', [AdminContact::class, 'reply'])->name('reply');

        Route::delete('/{contact}', [AdminContact::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [AdminContact::class, 'restore'])->name('restore');
});

/*
|--------------------------------------------------------------------------
| Optional: redirect /home -> /dashboard for compatibility
|--------------------------------------------------------------------------
*/
// Route::get('/home', function () {
//     return redirect()->route('dashboard');
// })->name('home');
