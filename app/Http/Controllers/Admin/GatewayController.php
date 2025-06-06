<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\OfflineGateway;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class GatewayController extends Controller
{
    public function index() {
        $data['paypal'] = PaymentGateway::find(15);
        $data['stripe'] = PaymentGateway::find(14);
        $data['paystack'] = PaymentGateway::find(12);
        $data['paytm'] = PaymentGateway::find(11);
        $data['flutterwave'] = PaymentGateway::find(6);
        $data['instamojo'] = PaymentGateway::find(13);
        $data['mollie'] = PaymentGateway::find(17);
        $data['razorpay'] = PaymentGateway::find(9);
        $data['mercadopago'] = PaymentGateway::find(19);
        $data['payumoney'] = PaymentGateway::find(18);

        return view('admin.gateways.index', $data);
    }

    public function paypalUpdate(Request $request) {
        $paypal = PaymentGateway::find(15);
        $paypal->status = $request->status;

        $information = [];
        $information['client_id'] = $request->client_id;
        $information['client_secret'] = $request->client_secret;
        $information['sandbox_check'] = $request->sandbox_check;
        $information['text'] = "Pay via your PayPal account.";

        $paypal->information = json_encode($information);

        $paypal->save();

        Session::flash('success', "Paypal informations updated successfully!");

        return back();
    }

    public function stripeUpdate(Request $request) {
        $stripe = PaymentGateway::find(14);
        $stripe->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['secret'] = $request->secret;
        $information['text'] = "Pay via your Credit account.";

        $stripe->information = json_encode($information);

        $stripe->save();

        Session::flash('success', "Stripe informations updated successfully!");

        return back();
    }

    public function paystackUpdate(Request $request) {
        $paystack = PaymentGateway::find(12);
        $paystack->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['secret_key'] = $request->secret_key;
        $information['email'] = $request->email;
        $information['text'] = "Pay via your Paystack account.";

        $paystack->information = json_encode($information);

        $paystack->save();

        Session::flash('success', "Paystack informations updated successfully!");

        return back();
    }

    public function paytmUpdate(Request $request) {
        $paytm = PaymentGateway::find(11);
        $paytm->status = $request->status;

        $information = [];
        $information['merchant'] = $request->merchant;
        $information['secret'] = $request->secret;
        $information['website'] = $request->website;
        $information['industry'] = $request->industry;
        $information['text'] = "Pay via your paytm account.";

        $paytm->information = json_encode($information);

        $paytm->save();

        Session::flash('success', "Paytm informations updated successfully!");

        return back();
    }

    public function flutterwaveUpdate(Request $request) {
        $flutterwave = PaymentGateway::find(6);
        $flutterwave->status = $request->status;

        $information = [];
        $information['public_key'] = $request->public_key;
        $information['secret_key'] = $request->secret_key;
        $information['text'] = "Pay via your Flutterwave account.";

        $flutterwave->information = json_encode($information);

        $flutterwave->save();

        Session::flash('success', "Flutterwave informations updated successfully!");

        return back();
    }

    public function instamojoUpdate(Request $request) {
        $instamojo = PaymentGateway::find(13);
        $instamojo->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['token'] = $request->token;
        $information['sandbox_check'] = $request->sandbox_check;
        $information['text'] = "Pay via your Instamojo account.";

        $instamojo->information = json_encode($information);

        $instamojo->save();

        Session::flash('success', "Instamojo informations updated successfully!");

        return back();
    }

    public function mollieUpdate(Request $request) {
        $mollie = PaymentGateway::find(17);
        $mollie->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['text'] = "Pay via your Mollie Payment account.";

        $mollie->information = json_encode($information);

        $mollie->save();

        $arr = ['MOLLIE_KEY' => $request->key];
        setEnvironmentValue($arr);
        Artisan::call('config:clear');

        Session::flash('success', "Mollie Payment informations updated successfully!");

        return back();
    }

    public function razorpayUpdate(Request $request) {
        $razorpay = PaymentGateway::find(9);
        $razorpay->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['secret'] = $request->secret;
        $information['text'] = "Pay via your Razorpay account.";

        $razorpay->information = json_encode($information);

        $razorpay->save();

        Session::flash('success', "Razorpay informations updated successfully!");

        return back();
    }

    public function mercadopagoUpdate(Request $request) {
        $mercadopago = PaymentGateway::find(19);
        $mercadopago->status = $request->status;

        $information = [];
        $information['token'] = $request->token;
        $information['sandbox_check'] = $request->sandbox_check;
        $information['text'] = "Pay via your Mercado Pago account.";

        $mercadopago->information = json_encode($information);

        $mercadopago->save();

        Session::flash('success', "Mercado Pago informations updated successfully!");

        return back();

    }

    public function payumoneyUpdate(Request $request) {
        $payumoney = PaymentGateway::find(18);
        $payumoney->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['salt'] = $request->salt;
        $information['text'] = "Pay via your PayUmoney account.";
        $information['sandbox_check'] = $request->sandbox_check;

        $payumoney->information = json_encode($information);

        $payumoney->save();

        $arr = ['INDIPAY_MERCHANT_KEY' => $request->key,'INDIPAY_SALT' => $request->salt];
        setEnvironmentValue($arr);
        Artisan::call('config:clear');

        Session::flash('success', "PayUmoney informations updated successfully!");

        return back();
    }

    public function offline(Request $request) {
        $lang_code = isset($request->language) ?  $request->language : 'en';
        $lang = Language::where('code', $lang_code)->first();

        $lang_id = $lang->id;
        $data['ogateways'] = OfflineGateway::where('language_id', $lang_id)->orderBy('id', 'DESC')->paginate(10);
        $data['lang_id'] = $lang_id;

        return view('admin.gateways.offline.index', $data);
    }

    public function store(Request $request) {
        $messages = [
            'language_id.required' => 'The language field is required',
        ];

        $rules = [
            'language_id' => 'required',
            'name' => 'required|max:100',
            'short_description' => 'nullable',
            'serial_number' => 'required|integer',
            'is_receipt' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $in = $request->all();
        $in['instructions'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->instructions);

        OfflineGateway::create($in);

        Session::flash('success', 'Gateway added successfully!');
        return "success";
    }

    public function update(Request $request) {

        $rules = [
            'name' => 'required|max:100',
            'short_description' => 'nullable',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $in = $request->except('_token', 'ogateway_id');
        $in['instructions'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->instructions);

        OfflineGateway::where('id', $request->ogateway_id)->update($in);

        Session::flash('success', 'Gateway updated successfully!');
        return "success";
    }

    public function status(Request $request)
    {
        $og = OfflineGateway::find($request->ogateway_id);
        if (!empty($request->type) && $request->type == 'product') {
            $og->product_checkout_status = $request->product_checkout_status;
        }
        elseif (!empty($request->type) && $request->type == 'package') {
            $og->package_order_status = $request->package_order_status;
        }
        elseif (!empty($request->type) && $request->type == 'course') {
            $og->course_checkout_status = $request->course_checkout_status;
        }
        elseif (!empty($request->type) && $request->type == 'donation') {
            $og->donation_checkout_status = $request->donation_checkout_status;
        }
        elseif (!empty($request->type) && $request->type == 'event') {
            $og->event_checkout_status = $request->event_checkout_status;
        }
        $og->save();

        Session::flash('success', 'Gateway status changed successfully!');
        return back();
    }

    public function delete(Request $request)
    {
        $ogateway = OfflineGateway::findOrFail($request->ogateway_id);
        $ogateway->delete();

        Session::flash('success', 'Gateway deleted successfully!');
        return back();
    }

}
