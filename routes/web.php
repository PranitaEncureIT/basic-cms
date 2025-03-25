<?php

use Illuminate\Support\Facades\Route;
use App\Models\Permalink;
use UniSharp\LaravelFilemanager\Lfm;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;



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

Route::get('clear-cache', function () {
  Artisan::call('cache:clear');
  Artisan::call('route:clear');
  Artisan::call('config:clear');
  Artisan::call('view:clear');
  return 'Application optimized';
});

Route::get('admin_permission', function () {
  $permi = Permission::pluck('name')->all();
  $role = Role::find(1);
  $role->syncPermissions($permi);
  die('Done');
});

Route::fallback(function () {
  return view('errors.404');
});

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth:admin', 'setLfmPath']], function () {
  Route::controller(App\Http\Controllers\Admin\SummernoteController::class)->group(function () {
    Lfm::routes();
    Route::post('summernote/upload', 'uploadFileManager')->name('lfm.summernote.upload');
  });
});

Route::controller(App\Http\Controllers\Front\FrontendController::class)->group(function () {
  Route::get('/backup', 'backup');
});

/*=======================================================
******************** Front Routes **********************
=======================================================*/

Route::post('/push', 'Front\PushController@store');

Route::group(['middleware' => 'setlang'], function () {

  Route::controller(App\Http\Controllers\Front\FrontendController::class)->group(function () {
    Route::get('/', 'index')->name('front.index');
    Route::post('/payment/instructions', 'paymentInstruction')->name('front.payment.instructions');
    Route::post('/sendmail', 'sendmail')->name('front.sendmail');
    Route::post('/subscribe', 'subscribe')->name('front.subscribe');
    Route::get('/quote', 'quote')->name('front.quote');
    Route::post('/sendquote', 'sendquote')->name('front.sendquote');
    Route::get('/checkout/payment/{slug1}/{slug2}', 'loadpayment')->name('front.load.payment');
    Route::post('/package-order', 'submitorder')->name('front.packageorder.submit');
    Route::get('/order-confirmation/{packageid}/{packageOrderId}', 'orderConfirmation')->name('front.packageorder.confirmation');
    Route::get('/team', 'team')->name('front.team');
    Route::get('/gallery', 'gallery')->name('front.gallery');
    Route::get('/faq', 'faq')->name('front.faq');
    // change language routes
    Route::get('/changelanguage/{lang}', 'changeLanguage')->name('changeLanguage');
  });

  Route::controller(App\Http\Controllers\Front\CausesController::class)->group(function () {
    Route::post('/cause/payment', 'makePayment')->name('front.causes.payment');
  });

  Route::controller(App\Http\Controllers\Payment\Causes\PaystackController::class)->group(function () {
    Route::get('/donation/paystack/success', 'successPayment')->name('donation.paystack.success');
  });

  //event tickets payment
  Route::controller(App\Http\Controllers\Front\EventController::class)->group(function () {
    Route::post('/event/payment', 'makePayment')->name('front.event.payment');
  });

  //causes donation payment via Paypal
  Route::controller(App\Http\Controllers\Payment\Causes\PaypalController::class)->group(function () {
    Route::get('/cause/paypal/payment/success', 'successPayment')->name('donation.paypal.success');
    Route::get('/cause/paypal/payment/cancel', 'cancelPayment')->name('donation.paypal.cancel');
  });

  //causes donation payment via Paytm
  Route::controller(App\Http\Controllers\Payment\causes\PaytmController::class)->group(function () {
    Route::post('/cause/paytm/payment/success', 'paymentStatus')->name('donation.paytm.paymentStatus');
  });

  //causes donation payment via Razorpay
  Route::controller(App\Http\Controllers\Payment\causes\RazorpayController::class)->group(function () {
    Route::post('/cause/razorpay/payment/success', 'successPayment')->name('donation.razorpay.success');
    Route::post('/cause/razorpay/payment/cancel', 'cancelPayment')->name('donation.razorpay.cancel');
  });

  Route::controller(App\Http\Controllers\Payment\RazorpayController::class)->group(function () {
    Route::post('razorpay/submit', 'store')->name('front.razorpay.submit');
    Route::post('razorpay/notify', 'notify')->name('front.razorpay.notify');
  });

  //causes donation payment via Payumoney
  Route::controller(App\Http\Controllers\Payment\Causes\PayumoneyController::class)->group(function () {
    Route::post('/cause/payumoney/payment', 'payment')->name('donation.payumoney.payment');
  });

  //causes donation payment via Flutterwave
  Route::controller(App\Http\Controllers\Payment\causes\FlutterWaveController::class)->group(function () {
    Route::post('/cause/flutterwave/success', 'successPayment')->name('donation.flutterwave.success');
    Route::post('/cause/flutterwave/cancel', 'cancelPayment')->name('donation.flutterwave.cancel');
    Route::get('/cause/flutterwave/success', 'successPage')->name('donation.flutterwave.successPage');
  });

  //causes donation payment via Instamojo
  Route::controller(App\Http\Controllers\Payment\causes\InstamojoController::class)->group(function () {
    Route::get('/cause/instamojo/success', 'successPayment')->name('donation.instamojo.success');
    Route::post('/cause/instamojo/cancel', 'cancelPayment')->name('donation.instamojo.cancel');
  });

  //causes donation payment via Mollie
  Route::controller(App\Http\Controllers\Payment\causes\MollieController::class)->group(function () {
    Route::get('/cause/mollie/success', 'successPayment')->name('donation.mollie.success');
    Route::post('/cause/mollie/cancel', 'cancelPayment')->name('donation.mollie.cancel');
  });

  // Mercado Pago
  Route::controller(App\Http\Controllers\Payment\causes\MercadopagoController::class)->group(function () {
    Route::post('/cause/mercadopago/cancel', 'cancelPayment')->name('donation.mercadopago.cancel');
    Route::post('/cause/mercadopago/success', 'successPayment')->name('donation.mercadopago.success');
  });

  Route::controller(App\Http\Controllers\Payment\PaymentController::class)->group(function () {
    Route::get('/payment/{packageid}/cancle', 'paycancle')->name('front.payment.cancle');
  });

  //Paypal Routes
  Route::controller(App\Http\Controllers\Payment\PaypalController::class)->group(function () {
    Route::post('/paypal/submit', 'store')->name('front.paypal.submit');
    Route::get('/paypal/{packageid}/notify', 'notify')->name('front.paypal.notify');
  });

  //Stripe Routes
  Route::controller(App\Http\Controllers\Payment\StripeController::class)->group(function () {
    Route::post('/stripe/submit', 'store')->name('front.stripe.submit');
  });

  //Paystack Routes
  Route::controller(App\Http\Controllers\Payment\PaystackController::class)->group(function () {
    Route::post('/paystack/submit', 'store')->name('front.paystack.submit');
  });

  //PayTM Routes
  Route::controller(App\Http\Controllers\Payment\PaytmController::class)->group(function () {
    Route::post('/paytm/submit', 'store')->name('front.paytm.submit');
    Route::post('/paytm/notify', 'notify')->name('front.paytm.notify');
  });

  //Flutterwave Routes
  Route::controller(App\Http\Controllers\Payment\FlutterWaveController::class)->group(function () {
    Route::post('/flutterwave/submit', 'store')->name('front.flutterwave.submit');
    Route::post('/flutterwave/notify', 'notify')->name('front.flutterwave.notify');
  });

  //Instamojo Routes
  Route::controller(App\Http\Controllers\Payment\InstamojoController::class)->group(function () {
    Route::post('/instamojo/submit', 'store')->name('front.instamojo.submit');
    Route::get('/instamojo/notify', 'notify')->name('front.instamojo.notify');
  });

  //Mollie Routes
  Route::controller(App\Http\Controllers\Payment\MollieController::class)->group(function () {
    Route::post('/mollie/submit', 'store')->name('front.mollie.submit');
    Route::get('/mollie/notify', 'notify')->name('front.mollie.notify');
  });

  // Mercado Pago
  Route::controller(App\Http\Controllers\Payment\MercadopagoController::class)->group(function () {
    Route::post('mercadopago/submit', 'store')->name('front.mercadopago.submit');
    Route::post('mercadopago/notify', 'notify')->name('front.mercadopago.notify');
  });

  // Payu
  Route::controller(App\Http\Controllers\Payment\PayumoneyController::class)->group(function () {
    Route::post('/payumoney/submit', 'store')->name('front.payumoney.submit');
    Route::post('/payumoney/notify', 'notify')->name('front.payumoney.notify');
  });


  //Offline Routes
  Route::controller(App\Http\Controllers\Payment\OfflineController::class)->group(function () {
    Route::post('/offline/{oid}/submit', 'store')->name('front.offline.submit');
  });

  // Product
  Route::controller(App\Http\Controllers\Front\ProductController::class)->group(function () {
    Route::get('/cart', 'cart')->name('front.cart');
    Route::get('/add-to-cart/{id}', 'addToCart')->name('add.cart');
    Route::post('/cart/update', 'updatecart')->name('cart.update');
    Route::get('/cart/item/remove/{id}', 'cartitemremove')->name('cart.item.remove');
    Route::get('/checkout', 'checkout')->name('front.checkout');
    Route::get('/checkout/{slug}', 'Prdouctcheckout')->name('front.product.checkout');
    Route::post('/coupon', 'coupon')->name('front.coupon');
  });

  // review
  Route::controller(App\Http\Controllers\Front\ReviewController::class)->group(function () {
    Route::post('product/review/submit', 'reviewsubmit')->name('product.review.submit');
  });

  // CHECKOUT SECTION
  Route::controller(App\Http\Controllers\Payment\product\PaymentController::class)->group(function () {
    Route::get('/product/payment/return', 'payreturn')->name('product.payment.return');
    Route::get('/product/payment/cancle', 'paycancle')->name('product.payment.cancle');
  });

  // paypal routes
  Route::controller(App\Http\Controllers\Payment\product\PaypalController::class)->group(function () {
    Route::get('/product/paypal/notify', 'notify')->name('product.paypal.notify');
    Route::post('/product/paypal/submit', 'store')->name('product.paypal.submit');
  });

  // stripe routes
  Route::controller(App\Http\Controllers\Payment\product\StripeController::class)->group(function () {
    Route::post('/product/stripe/submit', 'store')->name('product.stripe.submit');
  });

  Route::controller(App\Http\Controllers\Payment\product\OfflineController::class)->group(function () {
    Route::post('/product/offline/{gatewayid}/submit', 'store')->name('product.offline.submit');
  });

  //Flutterwave Routes
  Route::controller(App\Http\Controllers\Payment\product\FlutterWaveController::class)->group(function () {
    Route::post('/product/flutterwave/submit', 'store')->name('product.flutterwave.submit');
    Route::post('/product/flutterwave/notify', 'notify')->name('product.flutterwave.notify');
    Route::get('/product/flutterwave/notify', 'success')->name('product.flutterwave.success');
  });

  //Paystack Routes
  Route::controller(App\Http\Controllers\Payment\product\PaystackController::class)->group(function () {
    Route::post('/product/paystack/submit', 'store')->name('product.paystack.submit');
  });

  // RazorPay
  Route::controller(App\Http\Controllers\Payment\product\RazorpayController::class)->group(function () {
    Route::post('/product/razorpay/submit', 'store')->name('product.razorpay.submit');
    Route::post('/product/razorpay/notify', 'notify')->name('product.razorpay.notify');
  });

  //Instamojo Routes
  Route::controller(App\Http\Controllers\Payment\product\InstamojoController::class)->group(function () {
    Route::post('/product/instamojo/submit', 'store')->name('product.instamojo.submit');
    Route::get('/product/instamojo/notify', 'notify')->name('product.instamojo.notify');
  });

  //PayTM Routes
  Route::controller(App\Http\Controllers\Payment\product\PaytmController::class)->group(function () {
    Route::post('/product/paytm/submit', 'store')->name('product.paytm.submit');
    Route::post('/product/paytm/notify', 'notify')->name('product.paytm.notify');
  });

  //Mollie Routes
  Route::controller(App\Http\Controllers\Payment\product\MollieController::class)->group(function () {
    Route::post('/product/mollie/submit', 'store')->name('product.mollie.submit');
    Route::get('/product/mollie/notify', 'notify')->name('product.mollie.notify');
  });

  // Mercado Pago
  Route::controller(App\Http\Controllers\Payment\product\MercadopagoController::class)->group(function () {
    Route::post('/product/mercadopago/submit', 'store')->name('product.mercadopago.submit');
    Route::post('/product/mercadopago/notify', 'notify')->name('product.mercadopago.notify');
  });

  // PayUmoney
  Route::controller(App\Http\Controllers\Payment\product\PayumoneyController::class)->group(function () {
    Route::post('/product/payumoney/submit', 'store')->name('product.payumoney.submit');
    Route::post('/product/payumoney/notify', 'notify')->name('product.payumoney.notify');
  });

  // CHECKOUT SECTION ENDS

  // client feedback route
  Route::controller(App\Http\Controllers\Front\FeedbackController::class)->group(function () {
    Route::get('/feedback', 'feedback')->name('feedback');
    Route::post('/store_feedback', 'storeFeedback')->name('store_feedback');
  });
});

Route::group(['middleware' => ['web', 'setlang']], function () {
  Route::controller(App\Http\Controllers\User\LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('user.login');
    Route::post('/login', 'login')->name('user.login.submit');
    Route::get('/login/facebook', 'redirectToFacebook')->name('front.facebook.login');
    Route::get('/login/facebook/callback', 'handleFacebookCallback')->name('front.facebook.callback');
    Route::get('/login/google', 'redirectToGoogle')->name('front.google.login');
    Route::get('/login/google/callback', 'handleGoogleCallback')->name('front.google.callback');
    Route::get('/logout', 'logout')->name('user-logout');
  });

  Route::controller(App\Http\Controllers\User\RegisterController::class)->group(function () {
    Route::get('/register', 'registerPage')->name('user-register');
    Route::post('/register/submit', 'register')->name('user-register-submit');
    Route::get('/register/verify/{token}', 'token')->name('user-register-token');
  });

  Route::controller(App\Http\Controllers\User\ForgotController::class)->group(function () {
    Route::get('/forgot', 'showforgotform')->name('user-forgot');
    Route::post('/forgot', 'forgot')->name('user-forgot-submit');
  });

  // Course Route For Front-End
  Route::controller(App\Http\Controllers\Front\CourseController::class)->group(function () {
    Route::post('/course/review', 'giveReview')->name('course.review');
  });
});

/** Route For Enroll In Free Courses **/
Route::controller(App\Http\Controllers\Front\FreeCourseEnrollController::class)->group(function () {
  Route::post('/free_course/enroll', 'enroll')->name('free_course.enroll');
  Route::get('/free_course/enroll/complete', 'complete')->name('course.enroll.complete');
});
/** End Of Route For Enroll In Free Courses **/

/** Route For PayPal Payment To Sell The Courses **/
Route::controller(App\Http\Controllers\Payment\Course\PayPalGatewayController::class)->group(function () {
  Route::post('/course/payment/paypal', 'redirectToPayPal')->name('course.payment.paypal');
  Route::get('/course/payment/paypal/notify', 'notify')->name('course.paypal.notify');
  Route::get('/course/payment/paypal/complete', 'complete')->name('course.paypal.complete');
  Route::get('/course/payment/paypal/cancel', 'cancel')->name('course.paypal.cancel');
});
/** End Of Route For PayPal Payment To Sell The Courses **/

/** Route For Stripe Payment To Sell The Courses **/
Route::controller(App\Http\Controllers\Payment\Course\StripeGatewayController::class)->group(function () {
  Route::post('/course/payment/stripe', 'redirectToStripe')->name('course.payment.stripe');
  Route::get('/course/payment/stripe/complete', 'complete')->name('course.stripe.complete');
});
/** End Of Route For Stripe Payment To Sell The Courses **/

/** Route For Paytm Payment To Sell The Courses **/
Route::controller(App\Http\Controllers\Payment\Course\PaytmGatewayController::class)->group(function () {
  Route::post('/course/payment/paytm', 'redirectToPaytm')->name('course.payment.paytm');
  Route::post('/course/payment/paytm/notify', 'notify')->name('course.paytm.notify');
  Route::get('/course/payment/paytm/complete', 'complete')->name('course.paytm.complete');
  Route::get('/course/payment/paytm/cancel', 'cancel')->name('course.paytm.cancel');
});
/** End Of Route For Paytm Payment To Sell The Courses **/

/** Route For Razorpay Payment To Sell The Courses **/
Route::controller(App\Http\Controllers\Payment\Course\RazorpayGatewayController::class)->group(function () {
  Route::post('/course/payment/razorpay', 'redirectToRazorpay')->name('course.payment.razorpay');
  Route::post('/course/payment/razorpay/notify', 'notify')->name('course.razorpay.notify');
  Route::get('/course/payment/razorpay/complete', 'complete')->name('course.razorpay.complete');
  Route::get('/course/payment/razorpay/cancel', 'cancel')->name('course.razorpay.cancel');
});
/** End Of Route For Razorpay Payment To Sell The Courses **/

/** Route For Instamojo Payment To Sell The Courses **/
Route::controller(App\Http\Controllers\Payment\Course\InstamojoGatewayController::class)->group(function () {
  Route::post('/course/payment/instamojo', 'redirectToInstamojo')->name('course.payment.instamojo');
  Route::get('/course/payment/instamojo/notify', 'notify')->name('course.instamojo.notify');
  Route::get('/course/payment/instamojo/complete', 'complete')->name('course.instamojo.complete');
  Route::get('/course/payment/instamojo/cancel', 'cancel')->name('course.instamojo.cancel');
});
/** End Of Route For Instamojo Payment To Sell The Courses **/

/** Route For Mollie Payment To Sell The Courses **/
Route::controller(App\Http\Controllers\Payment\Course\MollieGatewayController::class)->group(function () {
  Route::post('/course/payment/mollie', 'redirectToMollie')->name('course.payment.mollie');
  Route::get('/course/payment/mollie/notify', 'notify')->name('course.mollie.notify');
  Route::get('/course/payment/mollie/complete', 'complete')->name('course.mollie.complete');
  Route::get('/course/payment/mollie/cancel', 'cancel')->name('course.mollie.cancel');
});
/** End Of Route For Mollie Payment To Sell The Courses **/

/** Route For Mollie Payment To Sell The Courses **/
Route::controller(App\Http\Controllers\Payment\Course\PayuMoneyController::class)->group(function () {
  Route::post('/course/payment/payumoney', 'redirectToPayumoney')->name('course.payment.payumoney');
  Route::get('/course/payment/payumoney/complete', 'complete')->name('course.payumoney.complete');
  Route::get('/course/payment/payumoney/cancel', 'cancel')->name('course.payumoney.cancel');
  Route::post('/course/payment/payumoney/notify', 'notify')->name('course.payumoney.notify');
});
/** End Of Route For Mollie Payment To Sell The Courses **/

/** Route For Offline Payment To Sell The Courses **/
Route::controller(App\Http\Controllers\Payment\Course\OfflineController::class)->group(function () {
  Route::post('/course/offline/{gatewayid}/submit', 'store')->name('course.offline.submit');
});
/** End Of Route For Offline Payment To Sell The Courses **/

/** Route For Flutterwave Payment To Sell The Courses **/
Route::controller(App\Http\Controllers\Payment\Course\FlutterwaveGatewayController::class)->group(function () {
  Route::post('/course/payment/flutterwave', 'redirectToFlutterwave')->name('course.payment.flutterwave');
  Route::post('/course/payment/flutterwave/notify', 'notify')->name('course.flutterwave.notify');
  // in Flutterwave the complete url have to be same as the notify url, otherwise it will not work
  Route::get('/course/payment/flutterwave/notify', 'complete')->name('course.flutterwave.complete');
  Route::get('/course/payment/flutterwave/notify_cancel', 'cancel')->name('course.flutterwave.cancel');
});
/** End Of Route For Flutterwave Payment To Sell The Courses **/

/** Route For MercadoPago Payment To Sell The Courses **/
Route::controller(App\Http\Controllers\Payment\Course\MercadoPagoGatewayController::class)->group(function () {
  Route::post('/course/payment/mercadopago', 'redirectToMercadoPago')->name('course.payment.mercadopago');
  Route::post('/course/payment/mercadopago/notify', 'notify')->name('course.mercadopago.notify');
  Route::get('/course/payment/mercadopago/complete', 'complete')->name('course.mercadopago.complete');
  Route::get('/course/payment/mercadopago/cancel', 'cancel')->name('course.mercadopago.cancel');
});
/** End Of Route For MercadoPago Payment To Sell The Courses **/

/** Route For Paystack Payment To Sell The Courses **/
Route::controller(App\Http\Controllers\Payment\Course\PaystackGatewayController::class)->group(function () {
  Route::post('/course/payment/paystack', 'redirectToPaystack')->name('course.payment.paystack');
  Route::get('/course/payment/paystack/notify', 'notify')->name('course.paystack.notify');
  Route::get('/course/payment/paystack/complete', 'complete')->name('course.paystack.complete');
  Route::get('/course/payment/paystack/cancel', 'cancel')->name('course.paystack.cancel');
});
/** End Of Route For Paystack Payment To Sell The Courses **/

Route::group(['prefix' => 'user', 'middleware' => ['auth', 'userstatus', 'setlang']], function () {
  // Summernote image upload
  Route::controller(App\Http\Controllers\User\SummernoteController::class)->group(function () {
    Route::post('/summernote/upload', 'upload')->name('user.summernote.upload');
  });

  Route::controller(App\Http\Controllers\User\UserController::class)->group(function () {
    Route::get('/dashboard', 'index')->name('user-dashboard');
    Route::get('/reset', 'resetform')->name('user-reset');
    Route::post('/reset', 'reset')->name('user-reset-submit');
    Route::get('/profile', 'profile')->name('user-profile');
    Route::post('/profile', 'profileupdate')->name('user-profile-update');
    Route::get('/shipping/details', 'shippingdetails')->name('shpping-details');
    Route::post('/shipping/details/update', 'shippingupdate')->name('user-shipping-update');
    Route::get('/billing/details', 'billingdetails')->name('billing-details');
    Route::post('/billing/details/update', 'billingupdate')->name('billing-update');
    Route::get('/packages', 'packages')->name('user-packages');
  });

  Route::controller(App\Http\Controllers\User\OrderController::class)->group(function () {
    Route::get('/orders', 'index')->name('user-orders');
    Route::get('/order/{id}', 'orderdetails')->name('user-orders-details');
    Route::post('/digital/download', 'digitalDownload')->name('user-digital-download');
  });

  Route::controller(App\Http\Controllers\User\EventController::class)->group(function () {
    Route::get('/events', 'index')->name('user-events');
    Route::get('/event/{id}', 'eventdetails')->name('user-event-details');
  });

  Route::controller(App\Http\Controllers\User\DonationController::class)->group(function () {
    Route::get('/donations', 'index')->name('user-donations');
  });

  Route::controller(App\Http\Controllers\User\CourseOrderController::class)->group(function () {
    Route::get('/course_orders', 'index')->name('user.course_orders');
    Route::get('/course/{id}/lessons', 'courseLessons')->name('user.course.lessons');
  });

  Route::controller(App\Http\Controllers\User\TicketController::class)->group(function () {
    Route::get('/tickets', 'index')->name('user-tickets');
    Route::get('/ticket/create', 'create')->name('user-ticket-create');
    Route::get('/ticket/messages/{id}', 'messages')->name('user-ticket-messages');
    Route::post('/ticket/store/', 'ticketstore')->name('user.ticket.store');
    Route::post('/ticket/reply/{id}', 'ticketreply')->name('user.ticket.reply');
    Route::post('/zip-file/upload', 'zip_upload')->name('zip.upload');
  });

  Route::controller(App\Http\Controllers\User\PackageController::class)->group(function () {
    Route::get('/package/orders', 'index')->name('user-package-orders');
    Route::get('/package/order/{id}', 'orderdetails')->name('user-package-order-details');
  });
});

/*=======================================================
******************** Admin Routes **********************
=======================================================*/


Route::group(['prefix' => 'admin', 'middleware' => 'guest:admin'], function () {
  Route::controller(App\Http\Controllers\Admin\LoginController::class)->group(function () {
    Route::post('/login', 'authenticate')->name('admin.auth');
  });
  Route::controller(App\Http\Controllers\Admin\ForgetController::class)->group(function () {
    Route::get('/mail-form', 'mailForm')->name('admin.forget.form');
    Route::post('/sendmail', 'sendmail')->name('admin.forget.mail');
  });
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'checkstatus', 'setLfmPath']], function () {

  // RTL check
  Route::middleware(['can:language'])->controller(App\Http\Controllers\Admin\LanguageController::class)->group(function () {
    Route::get('/rtlcheck/{langid}', 'rtlcheck')->name('admin.rtlcheck');
    // Admin Language Routes
    Route::get('/languages', 'index')->name('admin.language.index');
    Route::get('/language/{id}/edit', 'edit')->name('admin.language.edit');
    Route::get('/language/{id}/edit/keyword', 'editKeyword')->name('admin.language.editKeyword');
    Route::post('/language/store', 'store')->name('admin.language.store');
    Route::post('/language/upload', 'upload')->name('admin.language.upload');
    Route::post('/language/{id}/uploadUpdate', 'uploadUpdate')->name('admin.language.uploadUpdate');
    Route::post('/language/{id}/default', 'default')->name('admin.language.default');
    Route::post('/language/{id}/delete', 'delete')->name('admin.language.delete');
    Route::post('/language/update', 'update')->name('admin.language.update');
    Route::post('/language/{id}/update/keyword', 'updateKeyword')->name('admin.language.updateKeyword');
  });

  // Summernote image upload
  Route::controller(App\Http\Controllers\Admin\SummernoteController::class)->group(function () {
    Route::post('/summernote/upload', 'upload')->name('admin.summernote.upload');
  });

  // Admin logout Route
  Route::controller(App\Http\Controllers\Admin\LoginController::class)->group(function () {
    Route::get('/logout', 'logout')->name('admin.logout');
  });

  // Admin Dashboard Routes
  Route::middleware(['can:dashboard'])->controller(App\Http\Controllers\Admin\DashboardController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('admin.dashboard');
  });

  // Admin Profile Routes
  Route::controller(App\Http\Controllers\Admin\ProfileController::class)->group(function () {
    Route::get('/changePassword', 'changePass')->name('admin.changePass')->middleware('can:change-password');
    Route::post('/profile/updatePassword', 'updatePassword')->name('admin.updatePassword')->middleware('can:change-password');
    Route::get('/profile/edit', 'editProfile')->name('admin.editProfile')->middleware('can:profile');
    Route::post('/propic/update', 'updatePropic')->name('admin.propic.update')->middleware('can:profile');
    Route::post('/profile/update', 'updateProfile')->name('admin.updateProfile')->middleware('can:profile');
  });

  // Admin Home Version Setting Routes
  Route::middleware(['can:settings'])->controller(App\Http\Controllers\Admin\BasicController::class)->group(function () {
    Route::get('/home-settings', 'homeSettings')->name('admin.homeSettings');
    Route::post('/homeSettings/post', 'updateHomeSettings')->name('admin.homeSettings.update');
  });

  Route::middleware(['can:audit_trail'])->controller(App\Http\Controllers\AuditReportController::class)->group(function () {
    Route::get('/audit', 'index')->name('audit-from');
  });

  // Admin File Manager Routes
  Route::controller(App\Http\Controllers\Admin\BasicController::class)->group(function () {
    Route::get('/file-manager', 'fileManager')->name('admin.file-manager')->middleware('can:file_manager');
    // Admin Logo Routes
    Route::get('/logo', 'logo')->name('admin.logo')->middleware('can:logo_and_images');
    Route::post('/logo/post', 'updatelogo')->name('admin.logo.update')->middleware('can:logo_and_images');
    // Admin preloader Routes
    Route::get('/preloader', 'preloader')->name('admin.preloader')->middleware('can:preloader');
    Route::post('/preloader/post', 'updatepreloader')->name('admin.preloader.update')->middleware('can:preloader');
    // Admin Scripts Routes
    Route::get('/feature/settings', 'featuresettings')->name('admin.featuresettings')->middleware('can:preferences');
    Route::post('/feature/settings/update', 'updatefeatrue')->name('admin.featuresettings.update')->middleware('can:preferences');
    // Admin Basic Information Routes
    Route::get('/basicinfo', 'basicinfo')->name('admin.basicinfo');
    Route::post('/basicinfo/{langid}/post', 'updatebasicinfo')->name('admin.basicinfo.update');

    Route::get('/support', 'support')->name('admin.support')->middleware('can:support_information');
    Route::post('/support/{langid}/post', 'updatesupport')->name('admin.support.update')->middleware('can:support_information');
    // Admin Page Heading Routes
    Route::get('/heading', 'heading')->name('admin.heading')->middleware('can:page_headings');
    Route::post('/heading/{langid}/update', 'updateheading')->name('admin.heading.update')->middleware('can:page_headings');
    // Admin Scripts Routes
    Route::get('/script', 'script')->name('admin.script')->middleware('can:plugins');
    Route::post('/script/update', 'updatescript')->name('admin.script.update')->middleware('can:plugins');
    // Admin SEO Information Routes
    Route::get('/seo', 'seo')->name('admin.seo')->middleware('can:seo_information');
    Route::post('/seo/{langid}/update', 'updateseo')->name('admin.seo.update')->middleware('can:seo_information');
    // Admin Maintanance Mode Routes
    Route::get('/maintainance', 'maintainance')->name('admin.maintainance')->middleware('can:maintenance_mode');
    Route::post('/maintainance/update', 'updatemaintainance')->name('admin.maintainance.update')->middleware('can:maintenance_mode');

    // Admin Offer Banner Routes
    Route::get('/announcement', 'announcement')->name('admin.announcement');
    Route::post('/announcement/{langid}/update', 'updateannouncement')->name('admin.announcement.update');

    // Admin Section Customization Routes
    Route::get('/sections', 'sections')->name('admin.sections.index')->middleware('can:sections_customization');
    Route::post('/sections/update', 'updatesections')->name('admin.sections.update')->middleware('can:sections_customization');
    // Admin Cookie Alert Routes
    Route::get('/cookie-alert', 'cookiealert')->name('admin.cookie.alert')->middleware('can:cookie_alert');
    Route::post('/cookie-alert/{langid}/update', 'updatecookie')->name('admin.cookie.update')->middleware('can:cookie_alert');
  });

  Route::middleware(['can:email_settings'])->controller(App\Http\Controllers\Admin\EmailController::class)->group(function () {
    // Admin Email Settings Routes
    Route::get('/mail-from-admin', 'mailFromAdmin')->name('admin.mailFromAdmin');
    Route::post('/mail-from-admin/update', 'updateMailFromAdmin')->name('admin.mailfromadmin.update');
    Route::get('/mail-to-admin', 'mailToAdmin')->name('admin.mailToAdmin');
    Route::post('/mail-to-admin/update', 'updateMailToAdmin')->name('admin.mailtoadmin.update');
    Route::get('/email-templates', 'templates')->name('admin.email.templates');
    Route::get('/email-template/{id}/edit', 'editTemplate')->name('admin.email.editTemplate');
    Route::post('/emailtemplate/{id}/update', 'templateUpdate')->name('admin.email.templateUpdate');
  });

  // Admin Social Routes
  Route::middleware(['can:social_links'])->controller(App\Http\Controllers\Admin\SocialController::class)->group(function () {
    Route::get('/social', 'index')->name('admin.social.index');
    Route::post('/social/store', 'store')->name('admin.social.store');
    Route::get('/social/{id}/edit', 'edit')->name('admin.social.edit');
    Route::post('/social/update', 'update')->name('admin.social.update');
    Route::post('/social/delete', 'delete')->name('admin.social.delete');
  });

  // Admin Payment Gateways
  Route::middleware(['can:payment_gateways'])->controller(App\Http\Controllers\Admin\GatewayController::class)->group(function () {
    Route::get('/gateways', 'index')->name('admin.gateway.index');
    Route::post('/stripe/update', 'stripeUpdate')->name('admin.stripe.update');
    Route::post('/paypal/update', 'paypalUpdate')->name('admin.paypal.update');
    Route::post('/paystack/update', 'paystackUpdate')->name('admin.paystack.update');
    Route::post('/paytm/update', 'paytmUpdate')->name('admin.paytm.update');
    Route::post('/flutterwave/update', 'flutterwaveUpdate')->name('admin.flutterwave.update');
    Route::post('/instamojo/update', 'instamojoUpdate')->name('admin.instamojo.update');
    Route::post('/mollie/update', 'mollieUpdate')->name('admin.mollie.update');
    Route::post('/razorpay/update', 'razorpayUpdate')->name('admin.razorpay.update');
    Route::post('/mercadopago/update', 'mercadopagoUpdate')->name('admin.mercadopago.update');
    Route::post('/payumoney/update', 'payumoneyUpdate')->name('admin.payumoney.update');
    Route::get('/offline/gateways', 'offline')->name('admin.gateway.offline');
    Route::post('/offline/gateway/store', 'store')->name('admin.gateway.offline.store');
    Route::post('/offline/gateway/update', 'update')->name('admin.gateway.offline.update');
    Route::post('/offline/status', 'status')->name('admin.offline.status');
    Route::post('/offline/gateway/delete', 'delete')->name('admin.offline.gateway.delete');
  });

  // Admin Sitemap Routes
  Route::middleware(['can:sitemap'])->controller(App\Http\Controllers\Admin\SitemapController::class)->group(function () {
    Route::get('/sitemap', 'index')->name('admin.sitemap.index');
    Route::post('/sitemap/store', 'store')->name('admin.sitemap.store');
    Route::get('/sitemap/{id}/update', 'update')->name('admin.sitemap.update');
    Route::post('/sitemap/{id}/delete', 'delete')->name('admin.sitemap.delete');
    Route::post('/sitemap/download', 'download')->name('admin.sitemap.download');
  });

  // Admin Database Backup
  Route::middleware(['can:database_backup'])->controller(App\Http\Controllers\Admin\BackupController::class)->group(function () {
    Route::get('/backup', 'index')->name('admin.backup.index');
    Route::post('/backup/store', 'store')->name('admin.backup.store');
    Route::post('/backup/{id}/delete', 'delete')->name('admin.backup.delete');
    Route::post('/backup/download', 'download')->name('admin.backup.download');
  });

  // Admin Cache Clear Routes
  Route::middleware(['can:clear_cache'])->controller(App\Http\Controllers\Admin\CacheController::class)->group(function () {
    Route::get('/cache-clear', 'clear')->name('admin.cache.clear');
  });

  // Admin Hero Section (Static Version) Routes
  Route::controller(App\Http\Controllers\Admin\HerosectionController::class)->group(function () {
    Route::get('/herosection/static', 'static')->name('admin.herosection.static')->middleware('can:static_version');
    Route::post('/herosection/{langid}/update', 'update')->name('admin.herosection.update')->middleware('can:static_version');
    // Admin Hero Section (Video Version) Routes
    Route::get('/herosection/video', 'video')->name('admin.herosection.video')->middleware('can:video_version');
    Route::post('/herosection/video/{langid}/update', 'videoupdate')->name('admin.herosection.video.update')->middleware('can:video_version');
  });

  // Admin Hero Section (Slider Version) Routes
  Route::middleware(['can:slider_version'])->controller(App\Http\Controllers\Admin\SliderController::class)->group(function () {
    Route::get('/herosection/sliders', 'index')->name('admin.slider.index');
    Route::post('/herosection/slider/store', 'store')->name('admin.slider.store');
    Route::get('/herosection/slider/{id}/edit', 'edit')->name('admin.slider.edit');
    Route::post('/herosection/sliderupdate', 'update')->name('admin.slider.update');
    Route::post('/herosection/slider/delete', 'delete')->name('admin.slider.delete');
  });

  // Admin Feature Routes
  Route::middleware(['can:features'])->controller(App\Http\Controllers\Admin\FeatureController::class)->group(function () {
    Route::get('/features', 'index')->name('admin.feature.index');
    Route::post('/feature/store', 'store')->name('admin.feature.store');
    Route::get('/feature/{id}/edit', 'edit')->name('admin.feature.edit');
    Route::post('/feature/update', 'update')->name('admin.feature.update');
    Route::post('/feature/delete', 'delete')->name('admin.feature.delete');
  });

  // Admin Intro Section Routes
  Route::middleware(['can:intro_section'])->controller(App\Http\Controllers\Admin\IntrosectionController::class)->group(function () {
    Route::get('/introsection', 'index')->name('admin.introsection.index');
    Route::post('/introsection/{langid}/update', 'update')->name('admin.introsection.update');
  });

  // Admin Service Section Routes
  Route::middleware(['can:service_section'])->controller(App\Http\Controllers\Admin\ServicesectionController::class)->group(function () {
    Route::get('/servicesection', 'index')->name('admin.servicesection.index');
    Route::post('/servicesection/{langid}/update', 'update')->name('admin.servicesection.update');
  });

  // Admin Approach Section Routes
  Route::middleware(['can:approach_section'])->controller(App\Http\Controllers\Admin\ApproachController::class)->group(function () {
    Route::get('/approach', 'index')->name('admin.approach.index');
    Route::post('/approach/store', 'store')->name('admin.approach.point.store');
    Route::get('/approach/{id}/pointedit', 'pointedit')->name('admin.approach.point.edit');
    Route::post('/approach/{langid}/update', 'update')->name('admin.approach.update');
    Route::post('/approach/pointupdate', 'pointupdate')->name('admin.approach.point.update');
    Route::post('/approach/pointdelete', 'pointdelete')->name('admin.approach.pointdelete');
  });

  // Admin Statistic Section Routes
  Route::middleware(['can:statistics_section'])->controller(App\Http\Controllers\Admin\StatisticsController::class)->group(function () {
    Route::get('/statistics', 'index')->name('admin.statistics.index');
    Route::post('/statistics/{langid}/upload', 'upload')->name('admin.statistics.upload');
    Route::post('/statistics/store', 'store')->name('admin.statistics.store');
    Route::get('/statistics/{id}/edit', 'edit')->name('admin.statistics.edit');
    Route::post('/statistics/update', 'update')->name('admin.statistics.update');
    Route::post('/statistics/delete', 'delete')->name('admin.statistics.delete');
  });

  // Admin Call to Action Section Routes
  Route::middleware(['can:call_to_action_section'])->controller(App\Http\Controllers\Admin\CtaController::class)->group(function () {
    Route::get('/cta', 'index')->name('admin.cta.index');
    Route::post('/cta/{langid}/update', 'update')->name('admin.cta.update');
  });

  // Admin Testimonial Routes
  Route::middleware(['can:testimonials'])->controller(App\Http\Controllers\Admin\TestimonialController::class)->group(function () {
    Route::get('/testimonials', 'index')->name('admin.testimonial.index');
    Route::post('/testimonial/store', 'store')->name('admin.testimonial.store');
    Route::get('/testimonial/{id}/edit', 'edit')->name('admin.testimonial.edit');
    Route::post('/testimonial/update', 'update')->name('admin.testimonial.update');
    Route::post('/testimonial/delete', 'delete')->name('admin.testimonial.delete');
    Route::post('/testimonialtext/{langid}/update', 'textupdate')->name('admin.testimonialtext.update');
  });

  // Admin Blog Section Routes
  Route::controller(App\Http\Controllers\Admin\BlogsectionController::class)->group(function () {
    Route::get('/blogsection', 'index')->name('admin.blogsection.index');
    Route::post('/blogsection/{langid}/update', 'update')->name('admin.blogsection.update');
  });

  // Admin Partner Routes
  Route::middleware(['can:partners'])->controller(App\Http\Controllers\Admin\PartnerController::class)->group(function () {
    Route::get('/partners', 'index')->name('admin.partner.index');
    Route::post('/partner/store', 'store')->name('admin.partner.store');
    Route::get('/partner/{id}/edit', 'edit')->name('admin.partner.edit');
    Route::post('/partner/update', 'update')->name('admin.partner.update');
    Route::post('/partner/delete', 'delete')->name('admin.partner.delete');
  });

  // Admin Member Routes
  Route::middleware(['can:team_section'])->controller(App\Http\Controllers\Admin\MemberController::class)->group(function () {
    Route::get('/members', 'index')->name('admin.member.index');
    Route::get('/member/create', 'create')->name('admin.member.create');
    Route::post('/member/store', 'store')->name('admin.member.store');
    Route::get('/member/{id}/edit', 'edit')->name('admin.member.edit');
    Route::post('/member/update', 'update')->name('admin.member.update');
    Route::post('/member/delete', 'delete')->name('admin.member.delete');
    Route::post('/teamtext/{langid}/update', 'textupdate')->name('admin.teamtext.update');
    Route::post('/member/feature', 'feature')->name('admin.member.feature');
  });

  // Admin Package Background Routes
  Route::middleware(['can:package_background'])->controller(App\Http\Controllers\Admin\PackageController::class)->group(function () {
    Route::get('/package/background', 'background')->name('admin.package.background');
    Route::post('/package/{langid}/background-upload', 'uploadBackground')->name('admin.package.background.upload');
  });

  // Admin Footer Logo Text Routes
  Route::middleware(['can:logo_and_text'])->controller(App\Http\Controllers\Admin\FooterController::class)->group(function () {
    Route::get('/footers', 'index')->name('admin.footer.index');
    Route::post('/footer/{langid}/update', 'update')->name('admin.footer.update');
  });

  // Admin Ulink Routes
  Route::middleware(['can:useful_links'])->controller(App\Http\Controllers\Admin\UlinkController::class)->group(function () {
    Route::get('/ulinks', 'index')->name('admin.ulink.index');
    Route::post('/ulink/store', 'store')->name('admin.ulink.store');
    Route::get('/ulink/{id}/edit', 'edit')->name('admin.ulink.edit');
    Route::post('/ulink/update', 'update')->name('admin.ulink.update');
    Route::post('/ulink/delete', 'delete')->name('admin.ulink.delete');
  });

  // Admin alink Routes
  Route::middleware(['can:about_us'])->controller(App\Http\Controllers\Admin\AlinkController::class)->group(function () {
    Route::get('/alinks', 'index')->name('admin.alink.index');
    Route::post('/alink/store', 'store')->name('admin.alink.store');
    Route::get('/alink/{id}/edit', 'edit')->name('admin.alink.edit');
    Route::post('/alink/update', 'update')->name('admin.alink.update');
    Route::post('/alink/delete', 'delete')->name('admin.alink.delete');
  });

  // Admin dlink Routes
  Route::middleware(['can:department_links'])->controller(App\Http\Controllers\Admin\DlinkController::class)->group(function () {
    Route::get('/dlinks', 'index')->name('admin.dlink.index');
    Route::post('/dlink/store', 'store')->name('admin.dlink.store');
    Route::get('/dlink/{id}/edit', 'edit')->name('admin.dlink.edit');
    Route::post('/dlink/update', 'update')->name('admin.dlink.update');
    Route::post('/dlink/delete', 'delete')->name('admin.dlink.delete');
  });

  // Service Settings Route
  Route::controller(App\Http\Controllers\Admin\ServiceController::class)->group(function () {
    Route::get('/service/settings', 'settings')->name('admin.service.settings')->middleware('can:settings_services');
    Route::post('/service/updateSettings', 'updateSettings')->name('admin.service.updateSettings')->middleware('can:settings_services');
    // Admin Services Routes
    Route::get('/services', 'index')->name('admin.service.index')->middleware('can:settings_services');
    Route::post('/service/store', 'store')->name('admin.service.store')->middleware('can:services');
    Route::get('/service/{id}/edit', 'edit')->name('admin.service.edit')->middleware('can:services');
    Route::post('/service/update', 'update')->name('admin.service.update')->middleware('can:services');
    Route::post('/service/delete', 'delete')->name('admin.service.delete')->middleware('can:services');
    Route::post('/service/bulk-delete', 'bulkDelete')->name('admin.service.bulk.delete')->middleware('can:services');
    Route::get('/service/{langid}/getcats', 'getcats')->name('admin.service.getcats')->middleware('can:services');
    Route::post('/service/feature', 'feature')->name('admin.service.feature')->middleware('can:services');
    Route::post('/service/sidebar', 'sidebar')->name('admin.service.sidebar')->middleware('can:services');
  });

  // Admin Service Category Routes
  Route::middleware(['can:scategory'])->controller(App\Http\Controllers\Admin\ScategoryController::class)->group(function () {
    Route::get('/scategorys', 'index')->name('admin.scategory.index');
    Route::post('/scategory/store', 'store')->name('admin.scategory.store');
    Route::get('/scategory/{id}/edit', 'edit')->name('admin.scategory.edit');
    Route::post('/scategory/update', 'update')->name('admin.scategory.update');
    Route::post('/scategory/delete', 'delete')->name('admin.scategory.delete');
    Route::post('/scategory/bulk-delete', 'bulkDelete')->name('admin.scategory.bulk.delete');
    Route::post('/scategory/feature', 'feature')->name('admin.scategory.feature');
  });

  // Admin Portfolio Section Routes
  Route::middleware(['can:add_portfolio'])->controller(App\Http\Controllers\Admin\PortfoliosectionController::class)->group(function () {
    Route::get('/portfoliosection', 'index')->name('admin.portfoliosection.index');
    Route::post('/portfoliosection/{langid}/update', 'update')->name('admin.portfoliosection.update');
  });

  // Admin Portfolio Routes
  Route::middleware(['can:portfolios'])->controller(App\Http\Controllers\Admin\PortfolioController::class)->group(function () {
    Route::get('/portfolios', 'index')->name('admin.portfolio.index');
    Route::get('/portfolio/create', 'create')->name('admin.portfolio.create');
    Route::post('/portfolio/sliderstore', 'sliderstore')->name('admin.portfolio.sliderstore');
    Route::post('/portfolio/sliderrmv', 'sliderrmv')->name('admin.portfolio.sliderrmv');
    Route::post('/portfolio/store', 'store')->name('admin.portfolio.store');
    Route::get('/portfolio/{id}/edit', 'edit')->name('admin.portfolio.edit');
    Route::get('/portfolio/{id}/images', 'images')->name('admin.portfolio.images');
    Route::post('/portfolio/sliderupdate', 'sliderupdate')->name('admin.portfolio.sliderupdate');
    Route::post('/portfolio/update', 'update')->name('admin.portfolio.update');
    Route::post('/portfolio/delete', 'delete')->name('admin.portfolio.delete');
    Route::post('/portfolio/bulk-delete', 'bulkDelete')->name('admin.portfolio.bulk.delete');
    Route::get('portfolio/{id}/getservices', 'getservices')->name('admin.portfolio.getservices');
    Route::post('/portfolio/feature', 'feature')->name('admin.portfolio.feature');
  });

  // Admin Blog Category Routes
  Route::middleware(['can:bcategory'])->controller(App\Http\Controllers\Admin\BcategoryController::class)->group(function () {
    Route::get('/bcategorys', 'index')->name('admin.bcategory.index');
    Route::post('/bcategory/store', 'store')->name('admin.bcategory.store');
    Route::post('/bcategory/update', 'update')->name('admin.bcategory.update');
    Route::post('/bcategory/delete', 'delete')->name('admin.bcategory.delete');
    Route::post('/bcategory/bulk-delete', 'bulkDelete')->name('admin.bcategory.bulk.delete');
  });

  // Admin Blog Routes
  Route::middleware(['can:blogs'])->controller(App\Http\Controllers\Admin\BlogController::class)->group(function () {
    Route::get('/blogs', 'index')->name('admin.blog.index');
    Route::post('/blog/store', 'store')->name('admin.blog.store');
    Route::get('/blog/{id}/edit', 'edit')->name('admin.blog.edit');
    Route::post('/blog/update', 'update')->name('admin.blog.update');
    Route::post('/blog/delete', 'delete')->name('admin.blog.delete');
    Route::post('/blog/bulk-delete', 'bulkDelete')->name('admin.blog.bulk.delete');
    Route::get('/blog/{langid}/getcats', 'getcats')->name('admin.blog.getcats');
    Route::post('/blog/sidebar', 'sidebar')->name('admin.blog.sidebar');
  });

  // Admin Blog Archive Routes
  Route::middleware(['can:archives'])->controller(App\Http\Controllers\Admin\ArchiveController::class)->group(function () {
    Route::get('/archives', 'index')->name('admin.archive.index');
    Route::post('/archive/store', 'store')->name('admin.archive.store');
    Route::post('/archive/update', 'update')->name('admin.archive.update');
    Route::post('/archive/delete', 'delete')->name('admin.archive.delete');
  });

  // Admin Gallery Settings Routes
  Route::controller(App\Http\Controllers\Admin\GalleryCategoryController::class)->group(function () {
    Route::get('/gallery/settings', 'settings')->name('admin.gallery.settings')->middleware('can:gallery_settings');
    Route::post('/gallery/update_settings', 'updateSettings')->name('admin.gallery.update_settings')->middleware('can:gallery_settings');
    // Admin Gallery Category Routes
    Route::get('/gallery/categories', 'index')->name('admin.gallery.categories')->middleware('can:gallery_categories');
    Route::post('/gallery/store_category', 'store')->name('admin.gallery.store_category')->middleware('can:gallery_categories');
    Route::post('/gallery/update_category', 'update')->name('admin.gallery.update_category')->middleware('can:gallery_categories');
    Route::post('/gallery/delete_category', 'delete')->name('admin.gallery.delete_category')->middleware('can:gallery_categories');
    Route::post('/gallery/bulk_delete_category', 'bulkDelete')->name('admin.gallery.bulk_delete_category')->middleware('can:gallery_categories');
  });

  // Admin Gallery Routes
  Route::middleware(['can:gallery'])->controller(App\Http\Controllers\Admin\GalleryController::class)->group(function () {
    Route::get('/gallery', 'index')->name('admin.gallery.index');
    Route::get('/gallery/{langId}/get_categories', 'getCategories');
    Route::post('/gallery/store', 'store')->name('admin.gallery.store');
    Route::get('/gallery/{id}/edit', 'edit')->name('admin.gallery.edit');
    Route::post('/gallery/update', 'update')->name('admin.gallery.update');
    Route::post('/gallery/delete', 'delete')->name('admin.gallery.delete');
    Route::post('/gallery/bulk-delete', 'bulkDelete')->name('admin.gallery.bulk.delete');
  });

  // Admin FAQ Settings Routes
  Route::controller(App\Http\Controllers\Admin\FAQCategoryController::class)->group(function () {
    Route::get('/faq/settings', 'settings')->name('admin.faq.settings')->middleware('can:faq_settings');
    Route::post('/faq/update_settings', 'updateSettings')->name('admin.faq.update_settings')->middleware('can:faq_settings');
    // Admin FAQ Category Routes
    Route::get('/faq/categories', 'index')->name('admin.faq.categories')->middleware('can:faq_categories');
    Route::post('/faq/store_category', 'store')->name('admin.faq.store_category')->middleware('can:faq_categories');
    Route::post('/faq/update_category', 'update')->name('admin.faq.update_category')->middleware('can:faq_categories');
    Route::post('/faq/delete_category', 'delete')->name('admin.faq.delete_category')->middleware('can:faq_categories');
    Route::post('/faq/bulk_delete_category', 'bulkDelete')->name('admin.faq.bulk_delete_category')->middleware('can:faq_categories');
  });

  // Admin FAQ Routes
  Route::middleware(['can:faqs'])->controller(App\Http\Controllers\Admin\FaqController::class)->group(function () {
    Route::get('/faqs', 'index')->name('admin.faq.index');
    Route::get('/faq/{langId}/get_categories', 'getCategories');
    Route::post('/faq/store', 'store')->name('admin.faq.store');
    Route::get('/faq/{id}/edit', 'edit')->name('admin.faq.edit');
    Route::post('/faq/update', 'update')->name('admin.faq.update');
    Route::post('/faq/delete', 'delete')->name('admin.faq.delete');
    Route::post('/faq/bulk-delete', 'bulkDelete')->name('admin.faq.bulk.delete');
  });

  // Admin Job Category Routes
  Route::middleware(['can:jcategories'])->controller(App\Http\Controllers\Admin\JcategoryController::class)->group(function () {
    Route::get('/jcategorys', 'index')->name('admin.jcategory.index');
    Route::post('/jcategory/store', 'store')->name('admin.jcategory.store');
    Route::get('/jcategory/{id}/edit', 'edit')->name('admin.jcategory.edit');
    Route::post('/jcategory/update', 'update')->name('admin.jcategory.update');
    Route::post('/jcategory/delete', 'delete')->name('admin.jcategory.delete');
    Route::post('/jcategory/bulk-delete', 'bulkDelete')->name('admin.jcategory.bulk.delete');
  });

  // Admin Jobs Routes
  Route::controller(App\Http\Controllers\Admin\JobController::class)->group(function () {
    Route::get('/jobs', 'index')->name('admin.job.index')->middleware('can:job_management');
    Route::get('/job/create', 'create')->name('admin.job.create')->middleware('can:post_job');
    Route::post('/job/store', 'store')->name('admin.job.store')->middleware('can:post_job');
    Route::get('/job/{id}/edit', 'edit')->name('admin.job.edit')->middleware('can:post_job');
    Route::post('/job/update', 'update')->name('admin.job.update')->middleware('can:post_job');
    Route::post('/job/delete', 'delete')->name('admin.job.delete')->middleware('can:post_job');
    Route::post('/job/bulk-delete', 'bulkDelete')->name('admin.job.bulk.delete')->middleware('can:post_job');
    Route::get('/job/{langid}/getcats', 'getcats')->name('admin.job.getcats')->middleware('can:post_job');
  });

  // Admin Contact Routes
  Route::middleware(['can:contact_page'])->controller(App\Http\Controllers\Admin\ContactController::class)->group(function () {
    Route::get('/contact', 'index')->name('admin.contact.index');
    Route::post('/contact/{langid}/post', 'update')->name('admin.contact.update');
  });

  // Mega Menus Management Routes
  Route::controller(App\Http\Controllers\Admin\MenuBuilderController::class)->group(function () {
    Route::get('/megamenus', 'megamenus')->name('admin.megamenus')->middleware('can:mega_menu');
    Route::get('/megamenus/edit', 'megaMenuEdit')->name('admin.megamenu.edit')->middleware('can:mega_menu');
    Route::post('/megamenus/update', 'megaMenuUpdate')->name('admin.megamenu.update')->middleware('can:mega_menu');
    // Menus Builder Management Routes
    Route::get('/menu-builder', 'index')->name('admin.menu_builder.index')->middleware('can:main_menu');
    Route::post('/menu-builder/update', 'update')->name('admin.menu_builder.update')->middleware('can:main_menu');
    // Permalinks Routes
    Route::get('/permalinks', 'permalinks')->name('admin.permalinks.index')->middleware('can:permalinks');
    Route::post('/permalinks/update', 'permalinksUpdate')->name('admin.permalinks.update')->middleware('can:permalinks');
  });

  Route::middleware(['can:popups'])->controller(App\Http\Controllers\Admin\PopupController::class)->group(function () {
    Route::get('popups', 'index')->name('admin.popup.index');
    Route::get('popup/types', 'types')->name('admin.popup.types');
    Route::get('popup/{id}/edit', 'edit')->name('admin.popup.edit');
    Route::get('popup/create', 'create')->name('admin.popup.create');
    Route::post('popup/store', 'store')->name('admin.popup.store');
    Route::post('popup/delete', 'delete')->name('admin.popup.delete');
    Route::post('popup/bulk-delete', 'bulkDelete')->name('admin.popup.bulk.delete');
    Route::post('popup/status', 'status')->name('admin.popup.status');
    Route::post('popup/update', 'update')->name('admin.popup.update');
  });

  // Menu Manager Routes
  Route::middleware(['can:main_menu'])->controller(App\Http\Controllers\Admin\PageController::class)->group(function () {
    Route::get('/page/settings', 'settings')->name('admin.page.settings')->middleware('can:pages_settings');
    Route::post('/page/update-settings', 'updateSettings')->name('admin.page.updateSettings')->middleware('can:pages_settings');
    Route::get('/page/create', 'create')->name('admin.page.create')->middleware('can:create_page');
    Route::post('/page/store', 'store')->name('admin.page.store')->middleware('can:create_page');
    Route::get('/page/{menuID}/edit', 'edit')->name('admin.page.edit')->middleware('can:create_page');
    Route::post('/page/update', 'update')->name('admin.page.update')->middleware('can:create_page');
    Route::post('/page/delete', 'delete')->name('admin.page.delete')->middleware('can:create_page');
    Route::post('/page/bulk-delete', 'bulkDelete')->name('admin.page.bulk.delete')->middleware('can:create_page');
    Route::post('/upload/pagebuilder', 'uploadPbImage')->name('admin.pb.upload')->middleware('can:create_page');
    Route::post('/remove/img/pagebuilder', 'removePbImage')->name('admin.pb.remove')->middleware('can:create_page');
    Route::post('/upload/tui/pagebuilder', 'uploadPbTui')->name('admin.pb.tui.upload')->middleware('can:create_page');
    Route::get('/pages', 'index')->name('admin.page.index')->middleware('can:pages');
  });

  // Page Builder Routes
  Route::controller(App\Http\Controllers\Admin\PageBuilderController::class)->group(function () {
    Route::get('/pagebuilder/content', 'content')->name('admin.pagebuilder.content');
    Route::post('/pagebuilder/save', 'save')->name('admin.pagebuilder.save');
    Route::post('/front/test_api', 'test_api')->name('front.test_api');
  });

  Route::middleware(['can:category'])->controller(App\Http\Controllers\Admin\ProductCategory::class)->group(function () {
    Route::get('/category', 'index')->name('admin.category.index');
    Route::post('/category/store', 'store')->name('admin.category.store');
    Route::get('/category/{id}/edit', 'edit')->name('admin.category.edit');
    Route::post('/category/update', 'update')->name('admin.category.update');
    Route::post('/category/feature', 'feature')->name('admin.category.feature');
    Route::post('/category/home', 'home')->name('admin.category.home');
    Route::post('/category/delete', 'delete')->name('admin.category.delete');
    Route::post('/category/bulk-delete', 'bulkDelete')->name('admin.pcategory.bulk.delete');
  });

  Route::middleware(['can:shipping_charges'])->controller(App\Http\Controllers\Admin\ShopSettingController::class)->group(function () {
    Route::get('/shipping', 'index')->name('admin.shipping.index');
    Route::post('/shipping/store', 'store')->name('admin.shipping.store');
    Route::get('/shipping/{id}/edit', 'edit')->name('admin.shipping.edit');
    Route::post('/shipping/update', 'update')->name('admin.shipping.update');
    Route::post('/shipping/delete', 'delete')->name('admin.shipping.delete');
  });

  Route::controller(App\Http\Controllers\Admin\ProductController::class)->group(function () {
    Route::get('/product', 'index')->name('admin.product.index')->middleware('can:products');
    Route::get('/product/type', 'type')->name('admin.product.type')->middleware('can:products');
    Route::get('/product/create', 'create')->name('admin.product.create')->middleware('can:products');
    Route::post('/product/store', 'store')->name('admin.product.store')->middleware('can:products');
    Route::get('/product/{id}/edit', 'edit')->name('admin.product.edit')->middleware('can:products');
    Route::post('/product/feature', 'feature')->name('admin.product.feature')->middleware('can:products');
    Route::get('/product/populer/tags/', 'populerTag')->name('admin.product.tags')->middleware('can:popular_tags');
    Route::post('/product/populer/tags/update', 'populerTagupdate')->name('admin.popular-tag.update')->middleware('can:popular_tags');
    Route::post('/product/paymentStatus', 'paymentStatus')->name('admin.product.paymentStatus')->middleware('can:products');
    Route::get('product/{id}/getcategory', 'getCategory')->name('admin.product.getcategory')->middleware('can:products');
    Route::post('/product/delete', 'delete')->name('admin.product.delete')->middleware('can:products');
    Route::post('/product/bulk-delete', 'bulkDelete')->name('admin.product.bulk.delete')->middleware('can:products');
    Route::post('/product/{id}/uploadUpdate', 'uploadUpdate')->name('admin.product.uploadUpdate')->middleware('can:products');
    Route::post('/product/update', 'update')->name('admin.product.update')->middleware('can:products');
    Route::get('/product/{id}/images', 'images')->name('admin.product.images')->middleware('can:products');
    Route::get('/product/settings', 'settings')->name('admin.product.settings')->middleware('can:shop_settings');
    Route::post('/product/settings', 'updateSettings')->name('admin.product.settings')->middleware('can:shop_settings');
  });

  // Admin Coupon Routes
  Route::middleware(['can:coupons'])->controller(App\Http\Controllers\Admin\CouponController::class)->group(function () {
    Route::get('/coupon', 'index')->name('admin.coupon.index');
    Route::post('/coupon/store', 'store')->name('admin.coupon.store');
    Route::get('/coupon/{id}/edit', 'edit')->name('admin.coupon.edit');
    Route::post('/coupon/update', 'update')->name('admin.coupon.update');
    Route::post('/coupon/delete', 'delete')->name('admin.coupon.delete');
  });
  // Admin Coupon Routes End

  // Product Order
  Route::controller(App\Http\Controllers\Admin\ProductOrderController::class)->group(function () {
    Route::get('/product/all/orders', 'all')->name('admin.all.product.orders')->middleware('can:all_orders');
    Route::get('/product/pending/orders', 'pending')->name('admin.pending.product.orders')->middleware('can:pending_orders');
    Route::get('/product/processing/orders', 'processing')->name('admin.processing.product.orders')->middleware('can:processing_orders');
    Route::get('/product/completed/orders', 'completed')->name('admin.completed.product.orders')->middleware('can:completed_orders');
    Route::get('/product/rejected/orders', 'rejected')->name('admin.rejected.product.orders')->middleware('can:rejected_orders');
    Route::post('/product/orders/status', 'status')->name('admin.product.orders.status')->middleware('can:all_orders');
    Route::get('/product/orders/detais/{id}', 'details')->name('admin.product.details')->middleware('can:all_orders');
    Route::post('/product/order/delete', 'orderDelete')->name('admin.product.order.delete')->middleware('can:all_orders');
    Route::post('/product/order/bulk-delete', 'bulkOrderDelete')->name('admin.product.order.bulk.delete')->middleware('can:all_orders');
    Route::get('/product/orders/report', 'report')->name('admin.orders.report')->middleware('can:order_report');
    Route::get('/product/export/report', 'exportReport')->name('admin.orders.export')->middleware('can:order_report');
  });
  // Product Order end

  //Event Manage Routes
  Route::middleware(['can:event_categories'])->controller(App\Http\Controllers\Admin\EventCategoryController::class)->group(function () {
    Route::get('/event/categories', 'index')->name('admin.event.category.index');
    Route::post('/event/category/store', 'store')->name('admin.event.category.store');
    Route::post('/event/category/update', 'update')->name('admin.event.category.update');
    Route::post('/event/category/delete', 'delete')->name('admin.event.category.delete');
    Route::post('/event/categories/bulk-delete', 'bulkDelete')->name('admin.event.category.bulk.delete');
  });

  // Admin Event Routes
  Route::controller(App\Http\Controllers\Admin\EventController::class)->group(function () {
    Route::get('/event/settings', 'settings')->name('admin.event.settings')->middleware('can:event_settings');
    Route::post('/event/settings', 'updateSettings')->name('admin.event.settings')->middleware('can:event_settings');
    Route::get('/events', 'index')->name('admin.event.index')->middleware('can:all_events');
    Route::post('/event/upload', 'upload')->name('admin.event.upload')->middleware('can:all_events');
    Route::post('/event/slider/remove', 'sliderRemove')->name('admin.event.slider-remove')->middleware('can:all_events');
    Route::post('/event/store', 'store')->name('admin.event.store')->middleware('can:all_events');
    Route::get('/event/{id}/edit', 'edit')->name('admin.event.edit')->middleware('can:all_events');
    Route::get('/event/{id}/images', 'images')->name('admin.event.images')->middleware('can:all_events');
    Route::post('/event/update', 'update')->name('admin.event.update')->middleware('can:all_events');
    Route::post('/event/{id}/uploadUpdate', 'uploadUpdate')->name('admin.event.uploadUpdate')->middleware('can:all_events');
    Route::post('/event/delete', 'delete')->name('admin.event.delete')->middleware('can:all_events');
    Route::post('/event/bulk-delete', 'bulkDelete')->name('admin.event.bulk.delete')->middleware('can:all_events');
    Route::get('/event/{lang_id}/get-categories', 'getCategories')->name('admin.event.get-categories');
    Route::get('/events/payment-log', 'paymentLog')->name('admin.event.payment.log')->middleware('can:bookings');
    Route::post('/events/payment-log/delete', 'paymentLogDelete')->name('admin.event.payment.delete')->middleware('can:bookings');
    Route::post('/events/payment/bulk-delete', 'paymentLogBulkDelete')->name('admin.event.payment.bulk.delete')->middleware('can:bookings');
    Route::post('/events/payment-log-update', 'paymentLogUpdate')->name('admin.event.payment.log.update')->middleware('can:bookings');
    Route::get('/events/report', 'report')->name('admin.event.report')->middleware('can:event_report');
    Route::get('/events/export', 'exportReport')->name('admin.event.export')->middleware('can:event_report');
  });

  //Donation Manage Routes
  Route::controller(App\Http\Controllers\Admin\DonationController::class)->group(function () {
    Route::get('/donations', 'index')->name('admin.donation.index')->middleware('can:all_causes');
    Route::get('/donation/settings', 'settings')->name('admin.donation.settings')->middleware('can:donation_settings');
    Route::post('/donation/settings', 'updateSettings')->name('admin.donation.settings')->middleware('can:donation_settings');
    Route::post('/donation/store', 'store')->name('admin.donation.store')->middleware('can:all_causes');
    Route::get('/donation/{id}/edit', 'edit')->name('admin.donation.edit')->middleware('can:all_causes');
    Route::post('/donation/update', 'update')->name('admin.donation.update')->middleware('can:all_causes');
    Route::post('/donation/{id}/uploadUpdate', 'uploadUpdate')->name('admin.donation.uploadUpdate')->middleware('can:all_causes');
    Route::post('/donation/delete', 'delete')->name('admin.donation.delete')->middleware('can:all_causes');
    Route::post('/donation/bulk-delete', 'bulkDelete')->name('admin.donation.bulk.delete')->middleware('can:all_causes');
    Route::get('/donations/payment-log', 'paymentLog')->name('admin.donation.payment.log')->middleware('can:donations');
    Route::post('/donations/payment/delete', 'paymentDelete')->name('admin.donation.payment.delete')->middleware('can:donations');
    Route::post('/donations/bulk/delete', 'bulkPaymentDelete')->name('admin.donation.payment.bulk.delete')->middleware('can:donations');
    Route::post('/donations/payment-log-update', 'paymentLogUpdate')->name('admin.donation.payment.log.update')->middleware('can:donations');
    Route::get('/donation/report', 'report')->name('admin.donation.report')->middleware('can:donation_report');
    Route::get('/donation/export', 'exportReport')->name('admin.donation.export')->middleware('can:donation_report');
  });

  // Admin Event Calendar Routes
  Route::middleware(['can:event_calender'])->controller(App\Http\Controllers\Admin\CalendarController::class)->group(function () {
    Route::get('/calendars', 'index')->name('admin.calendar.index');
    Route::post('/calendar/store', 'store')->name('admin.calendar.store');
    Route::post('/calendar/update', 'update')->name('admin.calendar.update');
    Route::post('/calendar/delete', 'delete')->name('admin.calendar.delete');
    Route::post('/calendar/bulk-delete', 'bulkDelete')->name('admin.calendar.bulk.delete');
  });

  // Admin Article Category Routes
  Route::middleware(['can:article_categories'])->controller(App\Http\Controllers\Admin\ArticleCategoryController::class)->group(function () {
    Route::get('/article_categories', 'index')->name('admin.article_category.index');
    Route::post('/article_category/store', 'store')->name('admin.article_category.store');
    Route::post('/article_category/update', 'update')->name('admin.article_category.update');
    Route::post('/article_category/delete', 'delete')->name('admin.article_category.delete');
    Route::post('/article_category/bulk_delete', 'bulkDelete')->name('admin.article_category.bulk_delete');
  });

  // Admin Article Routes
  Route::middleware(['can:articles'])->controller(App\Http\Controllers\Admin\ArticleController::class)->group(function () {
    Route::get('/articles', 'index')->name('admin.article.index');
    Route::get('/article/{langId}/get_categories', 'getCategories');
    Route::post('/article/store', 'store')->name('admin.article.store');
    Route::get('/article/{id}/edit', 'edit')->name('admin.article.edit');
    Route::post('/article/update', 'update')->name('admin.article.update');
    Route::post('/article/delete', 'delete')->name('admin.article.delete');
    Route::post('/article/bulk_delete', 'bulkDelete')->name('admin.article.bulk_delete');
  });

  // Admin Course Category Routes
  Route::middleware(['can:course_categories'])->controller(App\Http\Controllers\Admin\CourseCategoryController::class)->group(function () {
    Route::get('/course_categories', 'index')->name('admin.course_category.index');
    Route::post('/course_category/store', 'store')->name('admin.course_category.store');
    Route::post('/course_category/update', 'update')->name('admin.course_category.update');
    Route::post('/course_category/delete', 'delete')->name('admin.course_category.delete');
    Route::post('/course_category/bulk_delete', 'bulkDelete')->name('admin.course_category.bulk_delete');
  });

  // Admin Course Routes
  Route::controller(App\Http\Controllers\Admin\CourseController::class)->group(function () {
    Route::get('/courses', 'index')->name('admin.course.index')->middleware('can:courses');
    Route::get('/course/create', 'create')->name('admin.course.create')->middleware('can:courses');
    Route::get('/course/{langId}/get_categories', 'getCategories')->middleware('can:courses');
    Route::post('/course/store', 'store')->name('admin.course.store')->middleware('can:courses');
    Route::get('/course/{id}/edit', 'edit')->name('admin.course.edit')->middleware('can:courses');
    Route::post('/course/update', 'update')->name('admin.course.update')->middleware('can:courses');
    Route::post('/course/delete', 'delete')->name('admin.course.delete')->middleware('can:courses');
    Route::post('/course/bulk_delete', 'bulkDelete')->name('admin.course.bulk_delete')->middleware('can:courses');
    Route::post('/course/featured', 'featured')->name('admin.course.featured')->middleware('can:courses');
    Route::get('/course/purchase-log', 'purchaseLog')->name('admin.course.purchaseLog')->middleware('can:courses');
    Route::post('/course/purchase/payment-status', 'purchasePaymentStatus')->name('admin.course.purchasePaymentStatus')->middleware('can:courses');
    Route::post('/course/purchase/delete', 'purchaseDelete')->name('admin.course.purchaseDelete')->middleware('can:courses');
    Route::post('/course/purchase/bulk_delete', 'purchaseBulkOrderDelete')->name('admin.course.purchaseBulkOrderDelete')->middleware('can:courses');
    Route::get('/course/settings', 'settings')->name('admin.course.settings')->middleware('can:course_settings');
    Route::post('/course/settings', 'updateSettings')->name('admin.course.settings')->middleware('can:course_settings');
    // Admin Course Enroll Report Routes
    Route::get('/course/enrolls/report', 'report')->name('admin.enrolls.report')->middleware('can:course_report');
    Route::get('/course/export/report', 'exportReport')->name('admin.enrolls.export')->middleware('can:course_report');
  });

  // Admin Course Modules Routes
  Route::controller(App\Http\Controllers\Admin\ModuleController::class)->group(function () {
    Route::get('/course/{id?}/modules', 'index')->name('admin.course.module.index');
    Route::post('/course/module/store', 'store')->name('admin.course.module.store');
    Route::post('/course/module/update', 'update')->name('admin.course.module.update');
    Route::post('/course/module/delete', 'delete')->name('admin.course.module.delete');
    Route::post('/course/module/bulk_delete', 'bulkDelete')->name('admin.course.module.bulk_delete');
  });

  // Admin Module Lessons Routes
  Route::controller(App\Http\Controllers\Admin\LessonController::class)->group(function () {
    Route::get('/module/{id}/lessons', 'index')->name('admin.module.lesson.index');
    Route::post('/module/lesson/store', 'store')->name('admin.module.lesson.store');
    Route::post('module/lesson/update', 'update')->name('admin.module.lesson.update');
    Route::post('/module/lesson/delete', 'delete')->name('admin.module.lesson.delete');
    Route::post('/module/lesson/bulk_delete', 'bulkDelete')->name('admin.module.lesson.bulk_delete');
  });

  // Admin RSS feed Routes
  Route::controller(App\Http\Controllers\Admin\RssFeedsController::class)->group(function () {
    Route::get('/rss', 'index')->name('admin.rss.index')->middleware('can:rss_posts');
    Route::get('/rss/feeds', 'feed')->name('admin.rss.feed')->middleware('can:rss_feeds');
    Route::get('/rss/create', 'create')->name('admin.rss.create')->middleware('can:import_rss_feeds');
    Route::post('/rss', 'store')->name('admin.rss.store')->middleware('can:import_rss_feeds');
    Route::get('/rss/edit/{id}', 'edit')->name('admin.rss.edit')->middleware('can:rss_feeds');
    Route::post('/rss/update', 'update')->name('admin.rss.update')->middleware('can:rss_feeds');
    Route::post('/rss/delete', 'rssdelete')->name('admin.rssfeed.delete')->middleware('can:rss_feeds');
    Route::post('/rss/feed/delete', 'delete')->name('admin.rss.delete')->middleware('can:rss_feeds');
    Route::post('/rss-posts/bulk/delete', 'bulkDelete')->name('admin.rss.bulk.delete')->middleware('can:rss_posts');
    Route::get('rss-feed/update/{id}', 'feedUpdate')->name('admin.rss.feedUpdate')->middleware('can:rss_feeds');
  });

  // Register User start
  Route::middleware(['can:registered_users'])->controller(App\Http\Controllers\Admin\RegisterUserController::class)->group(function () {
    Route::get('register/users', 'index')->name('admin.register.user');
    Route::post('register/users/ban', 'userban')->name('register.user.ban');
    Route::post('register/users/email', 'emailStatus')->name('register.user.email');
    Route::get('register/user/details/{id}', 'view')->name('register.user.view');
    Route::post('register/user/delete', 'delete')->name('register.user.delete');
    Route::post('register/user/bulk-delete', 'bulkDelete')->name('register.user.bulk.delete');
    Route::get('register/user/{id}/changePassword', 'changePass')->name('register.user.changePass');
    Route::post('register/user/updatePassword', 'updatePassword')->name('register.user.updatePassword');
  });
  //Register User end

  // Admin Push Notification Routes
  Route::middleware(['can:notification_settings'])->controller(App\Http\Controllers\Admin\PushController::class)->group(function () {
    Route::get('/pushnotification/settings', 'settings')->name('admin.pushnotification.settings');
    Route::post('/pushnotification/update/settings', 'updateSettings')->name('admin.pushnotification.updateSettings');
    Route::get('/pushnotification/send', 'send')->name('admin.pushnotification.send');
    Route::post('/push', 'push')->name('admin.pushnotification.push');
  });

  // Admin Subscriber Routes
  Route::middleware(['can:subscribers'])->controller(App\Http\Controllers\Admin\SubscriberController::class)->group(function () {
    Route::get('/subscribers', 'index')->name('admin.subscriber.index');
    Route::get('/mailsubscriber', 'mailsubscriber')->name('admin.mailsubscriber');
    Route::post('/subscribers/sendmail', 'subscsendmail')->name('admin.subscribers.sendmail');
    Route::post('/subscriber/delete', 'delete')->name('admin.subscriber.delete');
    Route::post('/subscriber/bulk-delete', 'bulkDelete')->name('admin.subscriber.bulk.delete');
  });

  // Admin Support Ticket Routes
  Route::controller(App\Http\Controllers\Admin\TicketController::class)->group(function () {
    Route::get('/all/tickets', 'all')->name('admin.tickets.all')->middleware('can:all_tickets');
    Route::get('/pending/tickets', 'pending')->name('admin.tickets.pending')->middleware('can:pending_tickets');
    Route::get('/open/tickets', 'open')->name('admin.tickets.open')->middleware('can:open_tickets');
    Route::get('/closed/tickets', 'closed')->name('admin.tickets.closed')->middleware('can:closed_tickets');
    Route::get('/ticket/messages/{id}', 'messages')->name('admin.ticket.messages')->middleware('can:all_tickets');
    Route::post('/zip-file/upload/', 'zip_file_upload')->name('admin.zip_file.upload')->middleware('can:all_tickets');
    Route::post('/ticket/reply/{id}', 'ticketReply')->name('admin.ticket.reply')->middleware('can:all_tickets');
    Route::get('/ticket/close/{id}', 'ticketclose')->name('admin.ticket.close')->middleware('can:all_tickets');
    Route::post('/ticket/assign/staff', 'ticketAssign')->name('ticket.assign.staff')->middleware('can:all_tickets');
    Route::get('/ticket/settings', 'settings')->name('admin.ticket.settings')->middleware('can:ticket_settings');
    Route::post('/ticket/settings', 'updateSettings')->name('admin.ticket.settings')->middleware('can:ticket_settings');
  });

  // Admin Package Form Builder Routes
  Route::controller(App\Http\Controllers\Admin\PackageController::class)->group(function () {
    Route::get('/package/settings', 'settings')->name('admin.package.settings')->middleware('can:package_settings');
    Route::post('/package/settings', 'updateSettings')->name('admin.package.settings')->middleware('can:package_settings');
    Route::get('/package/form', 'form')->name('admin.package.form')->middleware('can:package_form_builder');
    Route::post('/package/form/store', 'formstore')->name('admin.package.form.store')->middleware('can:package_form_builder');
    Route::post('/package/inputDelete', 'inputDelete')->name('admin.package.inputDelete')->middleware('can:package_form_builder');
    Route::get('/package/{id}/inputEdit', 'inputEdit')->name('admin.package.inputEdit')->middleware('can:package_form_builder');
    Route::get('/package/{id}/options', 'options')->name('admin.package.options')->middleware('can:package_form_builder');
    Route::post('/package/inputUpdate', 'inputUpdate')->name('admin.package.inputUpdate')->middleware('can:package_form_builder');
    Route::post('/package/feature', 'feature')->name('admin.package.feature')->middleware('can:package_form_builder');
    // Admin Packages Routes
    Route::get('/packages', 'index')->name('admin.package.index')->middleware('can:packages');
    Route::get('/package/{langId}/get_categories', 'getCategories')->middleware('can:packages');
    Route::post('/package/store', 'store')->name('admin.package.store')->middleware('can:packages');
    Route::get('/package/{id}/edit', 'edit')->name('admin.package.edit')->middleware('can:packages');
    Route::post('/package/update', 'update')->name('admin.package.update')->middleware('can:packages');
    Route::post('/package/delete', 'delete')->name('admin.package.delete')->middleware('can:packages');
    Route::post('/package/bulk-delete', 'bulkDelete')->name('admin.package.bulk.delete')->middleware('can:packages');
    Route::post('/package/payment-status', 'paymentStatus')->name('admin.package.paymentStatus')->middleware('can:packages');
    // Admin Package Orders Routes
    Route::get('/all/orders', 'all')->name('admin.all.orders');
    Route::get('/pending/orders', 'pending')->name('admin.pending.orders');
    Route::get('/processing/orders', 'processing')->name('admin.processing.orders');
    Route::get('/completed/orders', 'completed')->name('admin.completed.orders');
    Route::get('/rejected/orders', 'rejected')->name('admin.rejected.orders');
    Route::post('/orders/status', 'status')->name('admin.orders.status');
    Route::post('/orders/mail', 'mail')->name('admin.orders.mail');
    Route::post('/package/order/delete', 'orderDelete')->name('admin.package.order.delete');
    Route::post('/order/bulk-delete', 'bulkOrderDelete')->name('admin.order.bulk.delete');
    Route::get('/package/order/report', 'report')->name('admin.package.report');
    Route::get('/package/order/export', 'exportReport')->name('admin.package.export');
  });

  // Admin Package Category Routes
  Route::middleware(['can:package_categories'])->controller(App\Http\Controllers\Admin\PackageCategoryController::class)->group(function () {
    Route::get('/package/categories', 'index')->name('admin.package.categories');
    Route::post('/package/store_category', 'store')->name('admin.package.store_category');
    Route::post('/package/update_category', 'update')->name('admin.package.update_category');
    Route::post('/package/delete_category', 'delete')->name('admin.package.delete_category');
    Route::post('/package/bulk_delete_category', 'bulkDelete')->name('admin.package.bulk_delete_category');
  });

  // Admin Subscription Routes
  Route::middleware(['can:subscriptions'])->controller(App\Http\Controllers\Admin\SubscriptionController::class)->group(function () {
    Route::get('/subscriptions', 'subscriptions')->name('admin.subscriptions');
    Route::post('/subscription/mail', 'mail')->name('admin.subscription.mail');
    Route::post('/package/subscription/delete', 'subDelete')->name('admin.package.subDelete');
    Route::post('/package/subscription/status', 'status')->name('admin.subscription.status');
    Route::post('/sub/bulk-delete', 'bulkSubDelete')->name('admin.sub.bulk.delete');
  });

  // Admin Quote Form Builder Routes
  Route::controller(App\Http\Controllers\Admin\QuoteController::class)->group(function () {
    Route::get('/quote/visibility', 'visibility')->name('admin.quote.visibility')->middleware('can:visibility');
    Route::post('/quote/visibility/update', 'updateVisibility')->name('admin.quote.visibility.update')->middleware('can:visibility');
    Route::get('/quote/form', 'form')->name('admin.quote.form')->middleware('can:form_builder');
    Route::post('/quote/form/store', 'formstore')->name('admin.quote.form.store')->middleware('can:form_builder');
    Route::post('/quote/inputDelete', 'inputDelete')->name('admin.quote.inputDelete')->middleware('can:form_builder');
    Route::get('/quote/{id}/inputEdit', 'inputEdit')->name('admin.quote.inputEdit')->middleware('can:form_builder');
    Route::get('/quote/{id}/options', 'options')->name('admin.quote.options')->middleware('can:form_builder');
    Route::post('/quote/inputUpdate', 'inputUpdate')->name('admin.quote.inputUpdate')->middleware('can:form_builder');
    Route::post('/quote/delete', 'delete')->name('admin.quote.delete')->middleware('can:form_builder');
    Route::post('/quote/bulk-delete', 'bulkDelete')->name('admin.quote.bulk.delete')->middleware('can:form_builder');

    // Admin Quote Routes
    Route::get('/all/quotes', 'all')->name('admin.all.quotes')->middleware('can:all_quotes');
    Route::get('/pending/quotes', 'pending')->name('admin.pending.quotes')->middleware('can:pending_quotes');
    Route::get('/processing/quotes', 'processing')->name('admin.processing.quotes')->middleware('can:processing_quotes');
    Route::get('/completed/quotes', 'completed')->name('admin.completed.quotes')->middleware('can:completed_quotes');
    Route::get('/rejected/quotes', 'rejected')->name('admin.rejected.quotes')->middleware('can:rejected_quotes');
    Route::post('/quotes/status', 'status')->name('admin.quotes.status')->middleware('can:all_quotes');
    Route::post('/quote/mail', 'mail')->name('admin.quotes.mail')->middleware('can:all_quotes');
  });

  // Admin Roles Routes
  Route::middleware(['can:roles'])->controller(App\Http\Controllers\Admin\RoleController::class)->group(function () {
    Route::get('/roles', 'index')->name('admin.role.index');
    Route::post('/role/store', 'store')->name('admin.role.store');
    Route::post('/role/update', 'update')->name('admin.role.update');
    Route::post('/role/delete', 'delete')->name('admin.role.delete');
    Route::get('role/{id}/permissions/manage', 'managePermissions')->name('admin.role.permissions.manage');
    Route::post('role/permissions/update', 'updatePermissions')->name('admin.role.permissions.update');
  });

  Route::controller(App\Http\Controllers\Admin\PermissionController::class)->group(function() {
    Route::get('/permissions', 'index')->name('admin.permission.index');
    Route::post('/permissions/store', 'store')->name('admin.permission.store');
    Route::post('/permissions/update', 'update')->name('admin.permission.update');
    Route::post('/permissions/delete', 'delete')->name('admin.permission.delete');
  });

  // Admin Users Routes
  Route::middleware(['can:admins'])->controller(App\Http\Controllers\Admin\UserController::class)->group(function () {
    Route::get('/users', 'index')->name('admin.user.index');
    Route::post('/user/store', 'store')->name('admin.user.store');
    Route::get('/user/{id}/edit', 'edit')->name('admin.user.edit');
    Route::post('/user/update', 'update')->name('admin.user.update');
    Route::post('/user/delete', 'delete')->name('admin.user.delete');
  });

  // Admin View Client Feedbacks Routes
  Route::controller(App\Http\Controllers\Admin\FeedbackController::class)->group(function () {
    Route::get('/feedbacks', 'feedbacks')->name('admin.client_feedbacks');
    Route::post('/delete_feedback', 'deleteFeedback')->name('admin.delete_feedback');
    Route::post('/feedback/bulk-delete', 'bulkDelete')->name('admin.feedback.bulk.delete');
  });
});

// Dynamic Routes
Route::group(['middleware' => ['setlang']], function () {

  $wdPermalinks = Permalink::where('details', 1)->get();
  foreach ($wdPermalinks as $pl) {
    $type = $pl->type;
    $permalink = $pl->permalink;

    if ($type == 'package_order') {
      Route::get("$permalink/{id}", 'Front\FrontendController@packageorder')->name('front.packageorder.index');
    } elseif ($type == 'service_details') {
      Route::get("$permalink/{slug}", 'Front\FrontendController@servicedetails')->name('front.servicedetails');
    } elseif ($type == 'portfolio_details') {
      Route::get("$permalink/{slug}", 'Front\FrontendController@portfoliodetails')->name('front.portfoliodetails');
    } elseif ($type == 'product_details') {
      Route::get("$permalink/{slug}", 'Front\ProductController@productDetails')->name('front.product.details');
    } elseif ($type == 'course_details') {
      Route::get("$permalink/{slug}", 'Front\CourseController@courseDetails')->name('course_details');
    } elseif ($type == 'cause_details') {
      Route::get("$permalink/{slug}", 'Front\FrontendController@causeDetails')->name('front.cause_details');
    } elseif ($type == 'event_details') {
      Route::get("$permalink/{slug}", 'Front\FrontendController@eventDetails')->name('front.event_details');
    } elseif ($type == 'career_details') {
      Route::get("$permalink/{slug}", 'Front\FrontendController@careerdetails')->name('front.careerdetails');
    } elseif ($type == 'knowledgebase_details') {
      Route::get("$permalink/{slug}", 'Front\FrontendController@knowledgebase_details')->name('front.knowledgebase_details');
    } elseif ($type == 'blog_details') {
      Route::get("$permalink/{slug}", 'Front\FrontendController@blogdetails')->name('front.blogdetails');
    } elseif ($type == 'rss_details') {
      Route::get("$permalink/{slug}/{id}", 'Front\FrontendController@rssdetails')->name('front.rssdetails');
    }
  }
});

// Dynamic Routes
Route::group(['middleware' => ['setlang']], function () {

  $wdPermalinks = Permalink::where('details', 0)->get();
  foreach ($wdPermalinks as $pl) {
    $type = $pl->type;
    $permalink = $pl->permalink;

    if ($type == 'packages') {
      $action = 'Front\FrontendController@packages';
      $routeName = 'front.packages';
    } elseif ($type == 'services') {
      $action = 'Front\FrontendController@services';
      $routeName = 'front.services';
    } elseif ($type == 'portfolios') {
      $action = 'Front\FrontendController@portfolios';
      $routeName = 'front.portfolios';
    } elseif ($type == 'products') {
      $action = 'Front\ProductController@product';
      $routeName = 'front.product';
    } elseif ($type == 'cart') {
      $action = 'Front\ProductController@cart';
      $routeName = 'front.cart';
    } elseif ($type == 'product_checkout') {
      $action = 'Front\ProductController@checkout';
      $routeName = 'front.checkout';
    } elseif ($type == 'team') {
      $action = 'Front\FrontendController@team';
      $routeName = 'front.team';
    } elseif ($type == 'courses') {
      $action = 'Front\CourseController@courses';
      $routeName = 'courses';
    } elseif ($type == 'causes') {
      $action = 'Front\FrontendController@causes';
      $routeName = 'front.causes';
    } elseif ($type == 'events') {
      $action = 'Front\FrontendController@events';
      $routeName = 'front.events';
    } elseif ($type == 'career') {
      $action = 'Front\FrontendController@career';
      $routeName = 'front.career';
    } elseif ($type == 'event_calendar') {
      $action = 'Front\FrontendController@calendar';
      $routeName = 'front.calendar';
    } elseif ($type == 'knowledgebase') {
      $action = 'Front\FrontendController@knowledgebase';
      $routeName = 'front.knowledgebase';
    } elseif ($type == 'gallery') {
      $action = 'Front\FrontendController@gallery';
      $routeName = 'front.gallery';
    } elseif ($type == 'faq') {
      $action = 'Front\FrontendController@faq';
      $routeName = 'front.faq';
    } elseif ($type == 'blogs') {
      $action = 'Front\FrontendController@blogs';
      $routeName = 'front.blogs';
    } elseif ($type == 'rss') {
      $action = 'Front\FrontendController@rss';
      $routeName = 'front.rss';
    } elseif ($type == 'contact') {
      $action = 'Front\FrontendController@contact';
      $routeName = 'front.contact';
    } elseif ($type == 'quote') {
      $action = 'Front\FrontendController@quote';
      $routeName = 'front.quote';
    } elseif ($type == 'login') {
      $action = 'User\LoginController@showLoginForm';
      $routeName = 'user.login';
    } elseif ($type == 'register') {
      $action = 'User\RegisterController@registerPage';
      $routeName = 'user-register';
    } elseif ($type == 'forget_password') {
      $action = 'User\ForgotController@showforgotform';
      $routeName = 'user-forgot';
    } elseif ($type == 'admin_login') {
      $action = 'Admin\LoginController@login';
      $routeName = 'admin.login';
      Route::get("$permalink", "$action")->name("$routeName")->middleware('guest:admin');
      continue;
    }

    Route::get("$permalink", "$action")->name("$routeName");
  }
});

// Dynamic Page Routes
Route::group(['middleware' => 'setlang'], function () {
  Route::get('/{slug}', 'Front\FrontendController@dynamicPage')->name('front.dynamicPage');
});

//Commented Routes

//Route::get('/flutterwave/notify',[App\Http\Controllers\Payment\FlutterWaveController::class,'success'] )->name('front.flutterwave.success');

//Admin Hero Section (Parallax Version) Routes
//Route::get('/herosection/parallax', 'parallax')->name('admin.herosection.parallax');
//Route::post('/herosection/parallax/update', 'parallaxupdate')->name('admin.herosection.parallax.update');

//Route::controller(App\Http\Controllers\Admin\TestimonialController::class)->group(function () {
//Route::get('/testimonial/create', 'create')->name('admin.testimonial.create');   });

//Route::controller(App\Http\Controllers\Admin\UlinkController::class)->group(function () {
//Route::get('/ulink/create', 'create')->name('admin.ulink.create');
//});

//Route::controller(App\Http\Controllers\Admin\AlinkController::class)->group(function () { 
//Route::get('/alink/create', 'create')->name('admin.alink.create');
//});

//Route::controller(App\Http\Controllers\Admin\DlinkController::class)->group(function () {
//Route::get('/dlink/create', 'create')->name('admin.dlink.create');
//});

//Route::controller(App\Http\Controllers\Admin\PortfolioController::class)->group(function () {
//Route::post('/portfolio/sliderupdate', 'sliderupdate')->name('admin.portfolio.sliderupdate');
//});

//Route::controller(App\Http\Controllers\Admin\FaqController::class)->group(function () {
//Route::get('/faq/create', 'create')->name('admin.faq.create');
//});

//Route::controller(App\Http\Controllers\Admin\ProductController::class)->group(function () {
//Route::post('/product/sliderupdate', 'sliderupdate')->name('admin.product.sliderupdate');
//});

//Route::controller(App\Http\Controllers\Admin\CourseController::class)->group(function () {
//Route::post('/course/purchase/delete', 'purchaseDelete')->name('admin.course.purchase.delete');
//});

//Route::controller(App\Http\Controllers\Admin\RssFeedsController::class)->group(function () {
//Route::get('rss-feed/cronJobUpdate', 'cronJobUpdate')->name('rss.cronJobUpdate');
//});