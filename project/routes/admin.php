<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\RoutesHelper;

Route::middleware(['admin.guest'])->controller('Auth\LoginController')->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');

    Route::get('/forgot-password', 'showForgotPasswordForm')->name('password.forgot');
    Route::post('/forgot-password', 'sendResetLink')->name('password.email');

    Route::get('/set-password', 'showSetPasswordForm')->name('password.set');
    Route::post('/set-password', 'setPassword')->name('password.update');
});

Route::middleware(['admin'])->controller('Auth\LoginController')->group(function () {
    Route::get('/logout', 'logout')->name('logout');
});

RoutesHelper::registerAdminRoutes(function () {
    Route::controller('AdminController')->group(function () {
        Route::get('/', 'dashboard')->name('dashboard');
        Route::get('/dashboard', 'dashboard')->name('dashboard');

        Route::prefix('/setting')->name('setting.')->group(function () {
            Route::get('/profile', 'profile')->name('profile');
            Route::post('/profile', 'updateProfile')->name('profile.update');
            Route::get('/password', 'password')->name('password');
            Route::post('/password', 'updatePassword')->name('password.update');

            Route::controller('CronJobSettingController')->name('cronjob.')->prefix('cronjob')->group(function() {
                Route::get('/','list')->name('list');
                Route::post('/save/{id}','save')->name('save');
                Route::post('/status/{id}/running','running')->name('running');
                Route::post('/status/{id}/pause','pause')->name('pause');
            });

            Route::controller('GlobalSeoSettingController')->name('global_seo.')->prefix('global-seo')->group(function() {
                Route::get('/','index')->name('index');
                Route::post('/','save')->name('save');
            });

            Route::controller('NotificationSettingController')->group(function () {
                Route::get('/notification', 'notificationSetting')->name('notification');
                Route::post('/notification/send-test-mail', 'sendTestMail')->name('notification.test_mail');
                Route::post('/notification/send-test-sms', 'sendTestSMS')->name('notification.test_sms');
                Route::post('/notification', 'updateNotificationSetting')->name('notification.update');
            });

            Route::controller('MailTemplateController')->prefix('mail-templates')->name('mail_template.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/{mailTemplate}/edit', 'edit')->name('edit');
                Route::post('/{mailTemplate}/update', 'update')->name('update');
                Route::post('/{mailTemplate}/delete', 'destroy')->name('delete');
            });

            Route::controller('GeneralSettingController')->group(function () {
                Route::post('/general', 'updateGeneralSetting')->name('general.update');
                Route::get('/general', 'generalSetting')->name('general');
            });

            Route::controller('ConfigurationSettingController')->group(function () {
                Route::get('/configuration', 'configurationSetting')->name('configuration');
                Route::post('/configuration', 'updateConfigurationSetting')->name('configuration.update');
            });

            Route::get('/server-information', 'serverInformation')->name('server.information');
        });
    });

    Route::prefix('/setting')->name('setting.')->group(function () {
        Route::prefix('forms')->controller('FormController')->name('form.')->group(function() {
            Route::get('/', 'index')->name('index');
            Route::get('/builder/{slug}', 'builder')->name('builder');
            Route::post('/builder/{id}/save', 'saveBuilder')->name('builder.save');
            Route::post('/save/{id?}', 'save')->name('save');
        });
        
        Route::prefix('language')->controller('LanguageController')->name('language.')->group(function () {
            Route::post('/bulk-keyword/{code}', 'bulkUpdate')->name('bulkUpdate');
            Route::post('/add-keyword/{code}', 'addKeyword')->name('keyword.add');
            Route::post('/import/{code}', 'import')->name('import');
            Route::get('/', 'list')->name('list');
            Route::get('/new', 'new')->name('new');
            Route::post('/save/{id?}', 'save')->name('save');
            Route::get('/translate/{code}', 'translate')->name('translate');
            Route::get('/keywords', 'keywords')->name('keywords');
            Route::get('/change-lang/{lang}', 'changeLang')->name('change');
        });

        Route::prefix('integrations')
            ->controller('IntegrationController')
            ->name('integration.')
            ->group(function () {
                Route::get('/', 'list')->name('list');

                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');

                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::post('/{id}/update', 'update')->name('update');

                Route::post('/{id}/toggle', 'toggle')->name('toggle');
                Route::delete('/{id}', 'delete')->name('delete');
            });

        Route::prefix('social-login')->controller('SocialLoginConfigController')->name('social_login.')->group(function () {
            Route::get('/', 'list')->name('list');
            Route::get('/fields/{key}', 'getFields')->name('fields');
            Route::post('/config/{key}', 'saveConfig')->name('config.save');
            Route::post('/status/{key}', 'changeStatus')->name('config.status');
        });
    });

    Route::controller('UserController')->prefix('/users')->name('user.')->group(function () {
        Route::get('/', 'list')->name('list');
        Route::get('/kyc-pending', 'kycPending')->name('kyc.pending');
        Route::get('/email-unverified', 'emailUnverified')->name('email.unverified');
        Route::get('/incomplete-profile', 'incompleteProfile')->name('incomplete');
        Route::get('/mobile-unverified', 'mobileUnverified')->name('mobile.unverified');
        Route::get('/new-users', 'newUsers')->name('new_list');
        Route::get('/new', 'new')->name('new');
        Route::get('/details/{id}', 'details')->name('details');
        Route::get('/login/{id}', 'loginUser')->name('login');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/edit/{id?}', 'save')->name('save');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::post('/{id}/notify', 'notify')->name('notify');

        Route::get('/kyc-data/{id}', 'kycData')->name('kyc.data');
        Route::post('/kyc-data/{id}/reject', 'kycReject')->name('kyc.reject');
        Route::post('/kyc-data/{id}/approve', 'kycApprove')->name('kyc.approve');
    });

    Route::controller('FeedController')->prefix('/feeds')->name('feed.')->group(function () {
        Route::get('/', 'list')->name('list');
        Route::get('/details/{id}', 'details')->name('details');
    });

    Route::controller('AdminsController')->prefix('/admins')->name('admin.')->group(function () {
        Route::get('/', 'list')->name('list');
        Route::get('/create', 'create')->name('create');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/store/{id?}', 'save')->name('store');
        Route::post('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller('MediaController')->group(function () {
        Route::get('/media/index', 'index')->name('media.index');
        Route::post('/media/upload', 'upload')->name('media.upload');
        Route::get('/show-media/{file}', 'show')->name('media.show');
    });

    Route::controller('PaymentGatewayController')->name('payment_gateway.')->prefix('payment-gateways')->group(function () {
        Route::get('/manual', 'manualList')->name('manual.list');
        Route::get('/', 'list')->name('list');
        Route::post('/change-status/{key}', 'changeStatus')->name('status.change');
        Route::get('/edit/{key}', 'edit')->name('edit');
        Route::post('/save/{key?}', 'save')->name('save');
    });

    Route::controller('PaymentController')->name('payment.')->group(function () {
        Route::get('/payments', 'list')->name('list');
        Route::get('/payments/pending', 'pending')->name('pending');
        Route::get('/payments/successfull', 'successfull')->name('successfull');
        Route::get('/payments/failed', 'failed')->name('failed');
        Route::post('/payments/{id}/delete', 'deletePayment')->name('delete');
    });

    Route::controller('ReportController')->prefix('/report')->name('report.')->group(function () {
        Route::get('/transactions', 'transactions')->name('transactions');
        Route::get('/notifications/{id}/read', 'notificationRead')->name('notifications.read');
        Route::post('/notifications/{id}/delete', 'deleteNotification')->name('notifications.delete');
        Route::get('/notifications/mark-all-as-read', 'markAllAsRead')->name('notifications.read.all');
        Route::get('/notifications', 'notifications')->name('notifications');
        Route::get('/admin-logins', 'adminLogins')->name('admin_login');
        Route::get('/user-logins', 'userLogins')->name('user_login');
    });

    Route::controller('ACLController')->prefix('/acl')->name('acl.')->group(function () {
        Route::get('/roles', 'roles')->name('role.list');
        Route::get('/roles/create', 'createRole')->name('role.create');
        Route::post('/roles/store/{id?}', 'storeRole')->name('role.store');
        Route::get('/roles/edit/{id}', 'editRole')->name('role.edit');
        Route::post('/roles/delete/{id}', 'deleteRole')->name('role.delete');
        Route::post('/abilities/generate', 'genreateAbilities')->name('ability.generate');
    });

    Route::controller('BlogCategoryController')->name('blog_category.')->prefix('blogs-category')->group(function () {
        Route::get('/', 'list')->name('list');
        Route::post('/save/{id?}', 'save')->name('save');
        Route::post('/status/{id?}', 'status')->name('status');
    });

    Route::controller('BlogPostController')->name('blog.')->prefix('blogs')->group(function () {
        Route::get('/', 'list')->name('list');
        Route::get('/create', 'create')->name('create');
        Route::get('/published', 'published')->name('published');
        Route::get('/catagorised/{id}', 'catagorised')->name('catagorised');
        Route::get('/unpublished', 'unpublished')->name('unpublished');
        Route::post('/save/{id?}', 'save')->name('save');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller('SupportDepartmentController')
        ->name('support_department.')
        ->prefix('support-departments')
        ->group(function () {
            Route::get('/', 'list')->name('list');
            Route::post('/save/{id?}', 'save')->name('save');
            Route::post('/status/{id}', 'status')->name('status');
        });

    Route::controller('SupportTicketController')
        ->name('support_ticket.')
        ->prefix('support-tickets')
        ->group(function () {
            Route::get('/', 'list')->name('list');
            Route::get('/open', 'open')->name('open');
            Route::get('/answered', 'answered')->name('answered');
            Route::get('/closed', 'closed')->name('closed');
            Route::get('/by-department/{id?}', 'byDepartment')->name('by_department');

            Route::post('/save/{id?}', 'save')->name('save');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::post('/delete/{id}', 'delete')->name('delete');

            // quick toggle open/close
            Route::post('/status/{id}', 'status')->name('status');

            Route::post('/reply/{id}', 'reply')->name('reply');
            Route::post('/close/{id}', 'close')->name('close');
            Route::post('/reopen/{id?}', 'reopen')->name('reopen');
            Route::post('/priority/{id?}', 'setPriority')->name('priority');
        });


    Route::controller('NotificationSenderController')->prefix('/notification-sender')->name('notification.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/send', 'send')->name('send');
    });

    Route::controller('WebsiteController')->prefix('website')->name('website.')->group(function () {

        Route::prefix('/pages')->name('page.')->group(function () {
            Route::get('/', 'pages')->name('list');
            Route::get('/{id}/edit', 'editPage')->name('edit');
            Route::get('/new', 'newPage')->name('new');
            Route::post('/new', 'saveNewPage')->name('save');
            Route::post('/{id}/update', 'updatePage')->name('update');
            Route::post('/{id}/delete', 'deletePage')->name('delete');
        });

        Route::prefix('/sections')->name('section.')->group(function () {
            Route::get('/', 'sections')->name('list');
            Route::get('/{key}/edit', 'editSection')->name('edit');
            Route::post('/{key}/update', 'updateSection')->name('update');
        });
    });

    Route::controller('MiscellaneousController')->name('miscell.')->prefix('miscell')->group(function () {
        Route::get('/update', 'update')->name('update');
        Route::post('/update-system', 'updateSystem')->name('update.system');
        Route::get('/cache', 'cache')->name('cache');
        Route::post('/cache-clear', 'cacheClear')->name('cache.clear');
        Route::get('/custom-css', 'customCss')->name('custom.css');
        Route::post('/custom-css', 'customCssSave')->name('custom.css.save');
    });
});
