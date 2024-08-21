<?php

use App\Models\Message;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CerfaController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SuiviController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DayOffController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\SortableController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\NeedToBuyController;
use App\Http\Controllers\ToolsDebugController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MeetingRoomController;
use App\Http\Controllers\HourRecoveryController;
use App\Http\Controllers\ItemMovementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PointingTempController;
use App\Http\Controllers\PublicHolidayController;
use App\Http\Controllers\SlackEndpointController;
use App\Http\Controllers\ComplementHourController;
use App\Http\Controllers\ErpInstructionController;
use App\Http\Controllers\Logs\AuditLogsController;
use App\Http\Controllers\Logs\SystemLogsController;
use App\Http\Controllers\Account\SettingsController;
use App\Http\Controllers\ErpDocumentationController;
use App\Http\Controllers\Auth\SocialiteLoginController;
use App\Http\Controllers\Documentation\ReferencesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return redirect('index');
// });
Route::get("/command/{arg?}", [MaintenanceController::class, 'command']);
$menu = theme()->getMenu();
array_walk($menu, function ($val) {
    if (isset($val['path'])) {
        $route = Route::get($val['path'], [PagesController::class, 'index']);

        // Exclude documentation from auth middleware
        if (!Str::contains($val['path'], 'documentation')) {
            $route->middleware('auth');
        }
    }
});

// Documentations pages
Route::prefix('documentation')->group(function () {
    Route::get('getting-started/references', [ReferencesController::class, 'index']);
    Route::get('getting-started/changelog', [PagesController::class, 'index']);
});

Route::middleware('auth')->group(function () {

    // Account pages
    Route::prefix('account')->group(function () {

        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::put('settings/email', [SettingsController::class, 'changeEmail'])->name('settings.changeEmail');
        Route::put('settings/password', [SettingsController::class, 'changePassword'])->name('settings.changePassword');

    });

    // Logs pages
    Route::prefix('log')->name('log.')->group(function () {
        Route::resource('system', SystemLogsController::class)->only(['index', 'destroy']);
        Route::resource('audit', AuditLogsController::class)->only(['index', 'destroy']);
    });
});

Route::resource('users', UsersController::class);

/**
 * Socialite login using Google service
 * https://laravel.com/docs/8.x/socialite
 */
Route::get('/auth/redirect/{provider}', [SocialiteLoginController::class, 'redirect']);

require __DIR__.'/auth.php';


// //Reset Password
// Route::get('/resetPassword', [ResetPasswordController::class, "index"]);
// Route::get('/sendCodeToEmail', [ResetPasswordController::class, 'sendCodeToEmail']);
// Route::get('/confirm-code', [ResetPasswordController::class, 'goToConfirmationCode'])->name('confirmationCode');
// Route::post('/checkCode', [ResetPasswordController::class, 'checkIfCodeIsCorrect']);
// Route::get('/password-form', [ResetPasswordController::class, 'goToPasswordForm'])->name('password-form');
// Route::post('/checkNewPassword', [ResetPasswordController::class, 'checkNewPassword']);

Route::middleware(['auth', 'checkinweb'])->group(function () {
    Route::get("/user_chrono", [CheckInController::class, 'userChrono']);
    
    Route::get('/', function () {
        return redirect()->route('settings.index');
    });
    Route::get('', function () {
        return redirect()->route('settings.index');
    });

    Route::get('/days-off/download-attachment/{idAttachment}', [DayOffController::class, 'downloadAttachment']);
    Route::get("/my-days-off", [DayOffController::class, "seeMyRequestDaysOff"])->name("my_days_off");
    Route::get("/my_days_off/dataList", [DayOffController::class, "data_list_my_request_days_off"]);
    Route::post("/request_days_off/modal/{dayOff?}", [DayOffController::class, "loadModalToRequestDayOff"]);
    Route::post("/days-off/update", [DayOffController::class, "update"]);
    Route::get("/days-off/update-select-form", [DayOffController::class, "loadSelect"]);
    Route::post("/days-off/store-request", [DayOffController::class, 'store']);
    Route::post("/dayOff/delete/{dayOff?}", [DayOffController::class, "destroy"]);

    Route::post('/user/check-timer', [UserController::class, 'check']);

    Route::post('/user/info', [UserController::class, 'check_history_modal']);
    Route::get('/user/check/history', [UserController::class, 'check_history_list']);

    Route::get("/tickets", [TicketController::class, "index"]);
    Route::get("/ticket/list", [TicketController::class, "data_list"]);
    Route::post("/ticket/modal-form", [TicketController::class, "modal_form"]);
    Route::post("/ticket/add", [TicketController::class, "store"]);
    Route::post("/ticket/delete", [TicketController::class, "delete"]);
    Route::post("/add/ticket/assign/{ticket}", [TicketController::class, "add_assign_modal_form"]);
    Route::get("/load/user/it_not_assgned/{ticket}", [TicketController::class, "it_not_assgned_list"]);
    Route::post("/add/ticket/assign", [TicketController::class, "add_assign_to"]);
    Route::post("/ticket/set/resolve", [TicketController::class, "set_resolve"]);
    Route::post("/ticket/edit/{ticket}", [TicketController::class, "edit_ticket"]);
    Route::post("/ticket/save_edit/{ticket}", [TicketController::class, "save_edit"]);

    /* CERFA */
    Route::get('/cerfa/customer', [CerfaController::class, 'index']);
    Route::get('/cerfa/customer/dataList', [CerfaController::class, 'data_list']);
    Route::get('/cerfa/form/{customer?}', [CerfaController::class, 'customerProjectForm']);
    Route::post('/cerfa/project/store/{customer?}/{project?}', [CerfaController::class, 'store']);
    Route::post('/cerfa/customer/delete-modal', [CerfaController::class, 'delete_customer_modal']);
    Route::post('/cerfa/customer/delete', [CerfaController::class, 'delete_customer']);

    Route::get('/user/tab/info', [UserController::class, 'info_tab']);
    Route::post('/user/info/update', [UserController::class, 'update_info']);
    Route::get('/user/tab/connexion', [UserController::class, 'setting_account_tab']);
    Route::get('/user/tab/work-info', [UserController::class, 'user_work_info']);
    Route::post("/user/update/avatar", [UserController::class, "update_avatar"]);
    Route::get('/user/sanction', [UserController::class, 'get_sanction']);
    Route::get("/users/sanctions-data/{user}", [SanctionController::class, 'getData']);
    Route::post('/user/delete-modal', [UserController::class, 'delete_user_modal']);
    Route::post('/user/delete', [UserController::class, 'delete_user']);
    Route::post('/notification/set/as-read', [UserController::class, 'set_notification_as_seen']);
    Route::post('/notification/set/seen', [UserController::class, 'mark_as_read']);
    Route::post('/load/more/notification', [UserController::class, 'load_more_notification']);

    Route::get("/hour-recoveries", [HourRecoveryController::class, "index"]);
    Route::post("/hour-recoveries/form/{hourRecovery?}", [HourRecoveryController::class, "show_modal_form"]);
    Route::post("/hour-recoveries", [HourRecoveryController::class, "store"]);
    Route::post("/hour-recoveries/delete", [HourRecoveryController::class, "delete"]);
    Route::get("/hour-recoveries-dataList", [HourRecoveryController::class, "data_list"]);

    Route::get("/informations", [ErpDocumentationController::class, "index"]);
    Route::get("/Guides", [ErpInstructionController::class, "index"]);

    Route::post("/desktop/notification", [TicketController::class, "desktop_notification"]);

    /**Suivi */
    Route::get("/suivi/v2/projet", [SuiviController::class, "index"]);
    Route::get("/suivi/tab/table", [SuiviController::class, "table_tab"]);
    Route::post("/suivi/add-row", [SuiviController::class, "add_row"]);
    Route::get("/suivi/data_list", [SuiviController::class, "data_list"]);
    Route::post("/suivi/save/row", [SuiviController::class, "save_row"]);
    Route::post("/suivi/delete/row/confiramtion-modal", [SuiviController::class, "delete_row_confiramtion_modal"]);
    Route::post("/suivi/delete-row", [SuiviController::class, "delete_row"]);
    Route::get("/search/folder", [SuiviController::class, "search_folder"]);
    Route::post("/suivi/folder/list", [SuiviController::class, "folder_list"]);
    Route::get("/search/user", [SuiviController::class, "search_user"]);
    Route::post("/suivi/custom-filter-modal", [SuiviController::class, "custom_filter_modal"]);

    Route::post("/suivi/version-modal", [SuiviController::class, "version_modal"]);
    Route::post("/save/version", [SuiviController::class, "save_version"]);
    Route::post("/suivi/level-modal", [SuiviController::class, "level_modal"]);
    Route::post("/suivi/point-modal", [SuiviController::class, "point_modal"]);
    Route::post("/suivi/delete/point", [SuiviController::class, "point_delete"]);
    // save point and niveau by type client and type project
    Route::post("/suivi/save-point-level", [SuiviController::class, "save_point_level"]);
    Route::get("/suivi/data/points", [SuiviController::class, "point_data"]);
    Route::post("/suivi/save-point-other-version", [SuiviController::class, "save_other_version_point"]);
    Route::get("/suivi/data/other-version-point", [SuiviController::class, "data_other_version_point"]);

    // Route::post("/suivi/load-level/point", [SuiviController::class, "load_level_point"]);
    // Route::post("/suivi/save-level/point", [SuiviController::class, "save_level_point"]);
    Route::post("/suivi/version-level-point/dropdown", [SuiviController::class, "level_point_dropdown"]);

    Route::get("/suivi/data/version", [SuiviController::class, "data_list_version"]);
    Route::post("/suivi/delete/version/{version}", [SuiviController::class, "delete_version"]);
    Route::post("/suivi/type-modal", [SuiviController::class, "type_modal"]);
    Route::post("/suivi/save/type", [SuiviController::class, "save_type_suivi"]);
    Route::get("/suivi/data/type", [SuiviController::class, "data_list_type"]);
    Route::post("/suivi/delete/type/{type}", [SuiviController::class, "delete_type"]);
    Route::post("/suivi/save/custom-visible-column", [SuiviController::class, "save_hidden_column"]);
    Route::post("/suivi/more-detail", [SuiviController::class, "more_detail_item"]);
    Route::post("/suivi/more-detail/save-note", [SuiviController::class, "save_note"]);
    Route::get("/suivi/item/note/data-list", [SuiviController::class, "suivi_item_note_list"]);
    Route::post("/suivi/delete/note/{note}", [SuiviController::class, "suivi_item_note_delete"]);
    
    Route::post("/suivi/save/hours_days_work", [SuiviController::class, "save_hours_days_work"]);
    Route::post("/suivi/user-list", [SuiviController::class, "user_on_suivi_list"]);

    Route::post("/suivi/more-detail/save", [SuiviController::class, "save_more_detail_item"]);
    Route::post("/suivi/save/type-client", [SuiviController::class, "save_type_client"]);
    Route::post("/suivi/type-client", [SuiviController::class, "type_client_modal"]);
    Route::get("/suivi/get-one/type-client", [SuiviController::class, "get_type_client"]);
    Route::get("/suivi/data/type-client", [SuiviController::class, "type_client_data"]);
    Route::post("/suivi/delete/type-client/{type}", [SuiviController::class, "type_client_delete"]);

    Route::post("/save/custom-filter", [SuiviController::class, "save_custom_filter"]);
    Route::get("/suivi/custom-filter-data-list", [SuiviController::class, "custom_filter_data_list"]);
    Route::post("/suivi/custom-filter-data-delete/{customerFilter?}", [SuiviController::class, "delete_custom_filter"]);
    Route::get("/suivi/tab/statistiques", [SuiviController::class, "statistique"]);
    Route::post("/suivi/pause/prod", [SuiviController::class, "pause_prod"]);

    
    Route::post("/load/stat", [SuiviController::class, "load_stat"]);
    Route::post("/load/prod", [SuiviController::class, "load_prod"]);

    Route::get('/suivi/tableau/recapitulatif', [SuiviController::class, 'recapitulatif']);
    Route::post('/suivi/data/recap', [SuiviController::class, 'get_total_point_prod']);
    Route::post('/suivi/data/recap/point-list', [SuiviController::class, 'get_list_point_prod']);
    /**To Do list */

    Route::get("/tâche/list", [TaskController::class, "index"]);
    Route::get("/tache/list", [TaskController::class, "index"]);
    Route::post("/task/update", [TaskController::class, "update"]);
    Route::post("/task/update/order/item", [TaskController::class, "update_order_item"]);
    Route::post("/task/update/board/order", [TaskController::class, "update_order_board"]);
    Route::post("/task/add/board/status-modal", [TaskController::class, "add_board_modal"]);
    Route::post("/task/save/board/status", [TaskController::class, "add_status_board"]);
    Route::post("/task/save/comment", [TaskController::class, "save_comment"]);
    Route::post("/task/comment/delete", [TaskController::class, "delete_comment"]);
    Route::post("/task/modal-form", [TaskController::class, "modal_form"]);
    Route::post("/task/detail", [TaskController::class, "detail"]);
    Route::post("/task/save", [TaskController::class, "save"]);
    Route::get("/task/search", [TaskController::class, "search_task"]);
    Route::post("/task/create/section/modal", [TaskController::class, "task_section_modal"]);
    Route::post("/task/section/save", [TaskController::class, "task_section_save"]);
    Route::post("/task/section/load/kanban", [TaskController::class, "load_kanban_section"]);
    Route::post("/task/section/modal/members", [TaskController::class, "members_modal_form"]);
    Route::post("/task/section/save/new_members", [TaskController::class, "save_new_members"]);
    Route::get("/task/section/members/list", [TaskController::class, "members_section_task_data"]);
    Route::post("/task/section/members/delete/{section}", [TaskController::class, "delete_member_section"]);
    Route::post("/task/section/delete", [TaskController::class, "delete_section"]);
    Route::get("/task/files-list", [TaskController::class, "task_files_data"]);
    Route::post("/task/file/delete", [TaskController::class, "delete_task_files"]);
    Route::post("/task/file/add/other-file", [TaskController::class, "add_files_task"]);
    Route::post("/task/checklist/add/new", [TaskController::class, "add_new_task_checklist"]);
    Route::post("/task/checklist/mark/done", [TaskController::class, "mark_done_checklist"]);
    Route::post("/task/checklist/delete", [TaskController::class, "delete_checklist"]);
    
    Route::post("/kanban/data/source", [TaskController::class, "kanban_data"]);
    Route::post("/notification/check/permanent", [NotificationController::class, "check_permanent_notification"]);
    Route::post("/notification/list/permanent", [NotificationController::class, "list_permanent_notification"]);

    /**SDF */
    Route::get("/salle-de-reunion", [MeetingRoomController::class, "index"]);
    Route::post("/meeting-room/create-meeting/modal-form", [MeetingRoomController::class, "meeting_modal_form"]);
    Route::post("/meeting-room/store-meeting", [MeetingRoomController::class, "store"]);
    Route::post("/meeting-room/update-meeting", [MeetingRoomController::class, "update"]);
    Route::post("/meeting-room/load/horaires", [MeetingRoomController::class, "horaires"]);
    Route::post("/meeting-room/load/calendar", [MeetingRoomController::class, "calendar"]);
    
    /**Sortable */
    Route::get("/status/index", [SortableController::class, "index"]);
    Route::post("/update/status/order", [SortableController::class, "update_status_ordering"]);
});

Route::middleware(['auth', 'checkinweb', 'role:2,4'])->group(function () {

    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/user/dataList', [UserController::class, 'data_list']);
    Route::get('/user/form/{user?}', [UserController::class, 'goToNewUserForm']);
    Route::post('/user/store', [UserController::class, 'store']);
    Route::post("/user/edit/{id}", [UserController::class, "edit"]);

    /**Project  & Members */

    Route::get('/user/projet-membre', [UserController::class, 'project_member']);
    Route::get('/user/projet-membre/data-list', [UserController::class, 'project_member_data_list']);
    Route::post('/project/add/modal-form', [UserController::class, 'add_project_from']);
    Route::post('/project/save', [UserController::class, 'create_project']);
    Route::post('/project/add/members', [UserController::class, 'add_project_new_member_modal']);
    Route::post('/project/save/new-members', [UserController::class, 'save_project_new_member_modal']);
    Route::post('/project/edit/modal', [UserController::class, 'edit_project_member_modal']);
    Route::post('/project/do-edit', [UserController::class, 'save_edit_project_group']);
    Route::post('/project/add/validator-dayoff', [UserController::class, 'add_users_validator_dayoff_form']);
    Route::post('/project/save-users-validator-dayoff', [UserController::class, 'save_users_validator_dayoff']);



    Route::post("/modal-hour-recoveries-response/{hourRecovery}", [HourRecoveryController::class, "modal_response"]);
    Route::post("/response-hour-recoveries", [HourRecoveryController::class, "response_request"]);

    Route::get("/jobs", [JobController::class, 'index']);
    Route::get("/jobs/dataList", [JobController::class, 'data_list']);
    Route::post("/jobs/modal/{job?}", [JobController::class, 'modal_form']);
    Route::post("/jobs", [JobController::class, "saveOrUpdateJob"]);
    Route::post("/public-holidays/modalDelete/{job}", [JobController::class]);
    Route::post("/job/delete/{job}", [JobController::class, "deleteJob"]);

    Route::get('/days-off', [DayOffController::class, 'index'])->name('days-off');
    Route::get('/days-off/type', [DayOffController::class, 'dayoff_type']);
    Route::get('/days-off/type-data-list', [DayOffController::class, 'daysoff_type_data_list']);
    Route::post('/days-off/save-dayoff-type', [DayOffController::class, 'save_dayoff_type']);
    Route::post('/days-off/daysOffType/modal_form/{daysOffType?}', [DayOffController::class, 'dayoff_modal_form']);

    Route::get("/days-off/dataList", [DayOffController::class, "data_list"]);
    Route::get("/dayOff/export/pdf/{dayOff}/{ovpreview?}", [DayOffController::class, "export_pdf"]);
    Route::post('/days-off/giveResult', [DayOffController::class, 'giveResult'])->name('answer-request-daysOff');
    Route::post('/request-days-off/employee', [DayOffController::class, 'requestDaysOffForAnEmployee'])->name('requestDaysOffForAnEmployee');
    Route::get("/days-off/upgrade", [DayOffController::class, "upgradeDayOffEmployees"]);
    Route::get("/days-off/upgrade/dataList", [DayOffController::class, "data_list_upgrade_days_off"]);
    Route::post('/days-off/upgrade', [DayOffController::class, 'saveUpgradeDayOff']);
    Route::post("/days-off/information/modal/{dayOff?}", [DayOffController::class, "loadModalInfo"]);
    Route::post("/days-off/save-dayoff-nature", [DayOffController::class, "save_dayoff_nature"]);
    Route::get("/days-off/nature-data-list", [DayOffController::class, "nature_data_list"]);
    Route::post("/days-off/daysOffNature/modal_form/{dayoffNatureColor?}", [DayOffController::class, "addDayoffNature"]);

    Route::get('/public-holidays', [PublicHolidayController::class, "index"]);
    Route::post("/public-holidays/modal/{publicHoliday?}", [PublicHolidayController::class, "modal_form"]);
    Route::get('/public-holidays/data-list', [PublicHolidayController::class, "data_list"]);
    Route::post("/public-holidays", [PublicHolidayController::class, "updateOrCreate"]);
    Route::post("/public-holidays/modalDelete/{publicHoliday}", [PublicHolidayController::class]);
    Route::post("/public-holidays/delete/{publicHoliday}", [PublicHolidayController::class, "destroy"]);

    Route::get('/users-pointing', [CheckInController::class, 'index']);
    Route::get('/users-pointing/dataList', [CheckInController::class, 'data_list']);
    Route::get('/users-pointing-excel', [CheckInController::class, 'pagePointingExcel']);
    Route::get('/users-pointing-excel/dataList', [CheckInController::class, 'data_list_excel']);
    Route::post('/users-pointing-excel/form-modal', [CheckInController::class, 'formModalImportExcel']);
    Route::post("/users-pointing-excel/save", [CheckInController::class, "importFingerpointPointingInExcel"]);

    Route::get("/user-pointing/resume", [CheckInController::class, "resumePage"]);
    Route::get("/user-pointing/resume/data-list", [CheckInController::class, "data_list_pointing_resume"]);
    Route::post("/user-pointing/resume/details", [CheckInController::class, "modal_detail_resume"]);

    Route::get("/complement-hours", [ComplementHourController::class, 'index']);
    Route::get("/complement-hours/dataList", [ComplementHourController::class, "data_list"]);
    Route::post("/complement-hours/modal-form/{pointingResume?}", [ComplementHourController::class, 'modal_form']);
    Route::post("/complement-hours", [ComplementHourController::class, 'store']);
    Route::post("/complement-hours/delete/{pointingResume}", [ComplementHourController::class, "destroy"]);

    Route::get('/pointing-temp', [PointingTempController::class, 'index']);
    Route::get('/pointing-temp/data', [PointingTempController::class, 'getData']);
    Route::post('/pointing-temp', [PointingTempController::class, 'store']);
    Route::post('/pointing-temp/import-file', [PointingTempController::class, 'import']);

    Route::get("/negative-cumulative-hour", [CheckInController::class, 'negative_hour']);
    Route::get('/users-pointing/dataList-cumul-negative', [CheckInController::class, 'data_list_negative']);
    Route::post('/users-pointing/cumul-negative-notification', [CheckInController::class, 'cumul_negative_notification']);

    Route::post('load/users', [UserController::class, 'load_user']);
});

Route::middleware(['auth', 'checkinweb'])->group(function () {
    Route::get("/days-off/dataListGantt", [DayOffController::class, "days_off_gantt"]);

    /** Stock */
    Route::get("/stock/gerer", [StockController::class, "index"]);
    Route::get("/stock", [StockController::class, "index"]);
    
    Route::get("/stock/inventory/tab", [StockController::class, 'inventory']);
    Route::get("/stock/inventory/data_list", [StockController::class, 'inventory_data_list']);
    Route::post("/stock/inventory/modal-form", [StockController::class, 'inventor_modal_form']);
    Route::post("/stock/inventory/save/inventor/from-edit", [StockController::class, 'save_inventor_from_update']);
    Route::post("/purchase/migrate-one-article-to-stock", [StockController::class, 'create_article_migration_to_stock']);
    Route::post("/stock/inventory/create-article", [StockController::class, 'create_article_to_stock_modal_form']);
    Route::post("/stock/inventory/save-new-article", [StockController::class, 'save_new_article_to_stock']);
    Route::post("/stock/delete/item", [StockController::class, 'delete_item_in_inventory_list']);

    Route::get("/stock/category/tab", [StockController::class, 'category']);
    Route::get("/stock/category/data-list", [StockController::class, 'category_data_list']);
    Route::post("/stock/category/modal-form", [StockController::class, 'category_modal_form']);
    Route::post("/stock/category/save", [StockController::class, 'category_save']);
    Route::post("/stock/category/delete", [StockController::class, 'category_delete']);

    Route::get("/stock/article/tab", [StockController::class, 'article']);
    Route::get("/stock/article/data-list", [StockController::class, 'article_data_list']);
    Route::post("/stock/article/modal-form", [StockController::class, 'article_modal_form']);
    Route::post("/stock/article/save", [StockController::class, 'article_save']);
    Route::post("/stock/article/delete", [StockController::class, 'article_delete']);

    /**Location */
    Route::get("/stock/location/tab", [StockController::class, 'location']);
    Route::get("/stock/location/data-list", [StockController::class, 'location_data_list']);
    Route::post("/stock/location/modal-form", [StockController::class, 'location_modal_form']);
    Route::post("/stock/location/save", [StockController::class, 'location_save']);
    Route::post("/stock/location/delete", [StockController::class, 'location_delete']);
    Route::post("/stock/get-location-code", [StockController::class, 'get_location_code']);
    Route::get("/stock/location/history", [StockController::class, 'item_location_history']);

    
    /** Purchase */
    Route::post('/purchases/save', [PurchaseController::class, "save"]);
    Route::post('/purchases/delete', [PurchaseController::class, "delete"]);
    Route::get("/purchases/details/{purchase}", [PurchaseController::class, "pageDetail"]);
    Route::get("/purchases/details-data/{purchase}", [PurchaseController::class, "getDetail"]);
    Route::get("/purchases/download-file/{purchaseFile}", [PurchaseController::class, 'downloadFile']);
    Route::post("/purchase/save-num-invoice", [PurchaseController::class, 'saveNumInvoiceLine']);
    Route::post("/purchase/delete-num-invoice", [PurchaseController::class, 'deleteNumInvoiceLine']);
    Route::post("/purchases/to-stcok-modal-form", [PurchaseController::class, 'migrationToStockModal']);


});

Route::get("/purchases", [PurchaseController::class, 'index']);
Route::get("/purchases/data_list", [PurchaseController::class, "getPurchaseList"]);
Route::post('/purchases/demande-form', [PurchaseController::class, "modal_form"]);
Route::get('/item/{item_id}', [StockController::class, "detail_after_scanned_qrcode"]);

Route::middleware(['auth', 'checkinweb', 'not_contributor'])->group(function () {
    Route::get("/needToBuy", [NeedToBuyController::class, "index"]);
    Route::get("/needToBuy/pageList", [NeedToBuyController::class, "getPageList"]);
    Route::get("/needToBuy/data", [NeedToBuyController::class, "getDataList"]);
    Route::post("/needToBuy/form-modal/{needToBuy?}", [NeedToBuyController::class, "modalForm"]);
    Route::post("/needToBuy/save", [NeedToBuyController::class, "store"]);
    Route::post("/needToBuy/delete/{needToBuy}", [NeedToBuyController::class, "destroy"]);

    Route::post("/needToBuy/infos/{needToBuy}", [NeedToBuyController::class, "showInfosModal"]);
    Route::post("/need-to-buy/save-validate", [NeedToBuyController::class, "saveValidation"]);
    Route::get("/need-to-buy/detail/{needToBuy}", [NeedToBuyController::class, "getDetails"]);
    Route::post("/save-detail-need", [NeedToBuyController::class, "storeDetail"]);

    Route::get("/need-to-buy/statistic", [NeedToBuyController::class, "getPageStatistics"]);
    Route::get("/need-to-buy/statistic-data", [NeedToBuyController::class, "getDataListStatistic"]);

    Route::get("/need-to-buy/pdf", [NeedToBuyController::class, "exportToPDF"]);

    Route::get('/need-to-buy/file-page', [NeedToBuyController::class, "getPageFileList"]);
    Route::get('/need-to-buy/file-page/data', [NeedToBuyController::class, "getFileList"]);
    Route::get('/need-to-buy/download-invoice/{needFile}', [NeedToBuyController::class, "downloadInvoice"]);
});

Route::middleware(['auth', 'checkinweb'])->group(function() {
    Route::get('/messaging', [MessagingController::class, 'index']);
    Route::get('/messaging/contacts', [MessagingController::class, 'getContactList']);
    Route::get('/messaging/discussion/{user?}', [MessagingController::class, 'getDiscussionPage']);
    Route::get('/messaging/discussion-group/{messageGroup?}', [MessagingController::class, 'getDiscussionPageGroup']);
    Route::post('/messaging/show-modal-message', [MessagingController::class, 'modalMessage']);
    Route::post('/messaging/send-message', [MessagingController::class, 'store']);
    Route::get('/messaging/search-user', [MessagingController::class, 'searchUser']);
    Route::get("/messaging/modals/view-contacts", [MessagingController::class, 'viewContactModal']);
    Route::get("/messaging/modals/view-groups", [MessagingController::class, 'viewGroupModal']);
    Route::get("/message/download/file/{message}", [MessagingController::class, 'downloadAttachedFile']);

    Route::post("/messaging/search-discussion", [MessagingController::class, 'modalSearchDiscussion']);
    Route::post("/messaging/users-list-seen-message-modal/{message}", [MessagingController::class, 'getModalUserListSeenMessage']);
    
    Route::post("/messaging/group-form-modal/{messageGroup?}", [MessagingController::class, 'formGroupModal']);
    Route::post("/messaging/group-store", [MessagingController::class, 'storeGroup']);
    Route::post("/messaging/group-participants-modal/{messageGroup}", [MessagingController::class, 'formGroupParticipantsModal']);
    Route::get('/messaging/group-participants-data/{messageGroup}', [MessagingController::class, 'getDataOfParticipants']);
    Route::post("/messaging/group-participants-delete/{messageGroupParticipant}", [MessagingController::class, 'deleteMember']);
    Route::post("/messaging/group-participants-add-user", [MessagingController::class, 'addUserInGroup']);

    Route::post("/messaging/reactions/save-reaction", [MessagingController::class, 'saveReaction']);

    /**outils-debug */
    Route::get("/outils-debug", [ToolsDebugController::class, 'index']);
    Route::post("/outils-debug/reset-pwd", [ToolsDebugController::class, 'do_reset_pwd']);
    Route::post("/outils-debug/reset-pwd-all", [ToolsDebugController::class, 'do_reset_pwd_all']);

});

Route::middleware(['auth', 'checkinweb', 'role:2'])->group(function () {
    Route::get("/users/sanctions/index", [SanctionController::class, 'index']);
    Route::get("/users/sanctions/data_list", [SanctionController::class, 'data_list']);
    Route::post("/users/sanctions/form/{sanction?}", [SanctionController::class, 'formModal']);
    Route::post("/users/sanctions/form-save", [SanctionController::class, "store"]);
    Route::post("/users/sanctions/delete/{sanction}", [SanctionController::class, 'destroy']);
});

Route::get("/cmd/{command}", function($command) {
    Artisan::call($command);
    return ["success" => true];
});

Route::get("/signup/force", function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('login');
});

Route::get("/test", function() {
    
});

Route::get("/password-reset", function() {
    return Hash::make('123456');
});

Route::get("/user-password-reset/{registration_number}", [UserController::class, 'renewPasswordByRegistrationNumber']);

/** Slack Notification */
Route::get('/slack/endpoint', [SlackEndpointController::class, 'capture_get']);
Route::get('/slack/decode/{id}', [SlackEndpointController::class, 'decode_slack_event']);
Route::post('/slack/endpoint', [SlackEndpointController::class, 'capture']);

/** Test route */
Route::get('/redis/test', [NotificationController::class, 'redis']);

