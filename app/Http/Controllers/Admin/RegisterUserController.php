<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class RegisterUserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $draw = $request->input('draw');
            $start = $request->input('start');
            $length = $request->input('length');
            $search = $request->input('search')['value'] ?? null;

            $query = User::select('users.*');

            // Apply search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('email', 'LIKE', "%$search%")
                        ->orWhere('username', 'LIKE', "%$search%")
                        ->orWhere('number', 'LIKE', "%$search%");
                });
            }

            // Get total count before applying pagination
            $recordsTotal = $query->count();

            // Apply ordering, pagination, and fetch data
            $users = $query->orderBy('id', 'DESC')->skip($start)->take($length)->get();

            // Modify each user for DataTables response
            foreach ($users as $key => $user) {
                $user->sr_no = $key + 1 + $start;

                // Checkbox column
                $user->checkbox = "<input type='checkbox' class='bulk-check' data-val='{$user->id}'>";

                // Name & Email formatted
                $user->username = "<div class='d-flex flex-column'>
                                    <p href='javascript:void(0)' class='text-gray-800 text-hover-primary mb-1'>" . convertUtf8($user->username) . "</p>
                                   </div>";

                // Status switch
                $statusClass = $user->status == 1 ? 'bg-success' : 'bg-danger';
                $user->status = "<form id='userForm{$user->id}' action='" . route('register.user.ban') . "' method='post'>
                                    " . csrf_field() . "
                                    <select class='form-control form-control-sm $statusClass' 
                                        name='status' onchange='document.getElementById(\"userForm{$user->id}\").submit();'>
                                        <option value='1' " . ($user->status == 1 ? 'selected' : '') . ">Active</option>
                                        <option value='0' " . ($user->status == 0 ? 'selected' : '') . ">Inactive</option>
                                    </select>
                                    <input type='hidden' name='user_id' value='{$user->id}'>
                                 </form>";

                // Email verification status
                $emailClass = strtolower($user->email_verified) == 'yes' ? 'bg-success' : 'bg-danger';
                $user->email_verified = "<form id='emailForm{$user->id}' action='" . route('register.user.email') . "' method='post'>
                                            " . csrf_field() . "
                                            <select class='form-control form-control-sm $emailClass' 
                                                name='email_verified' onchange='document.getElementById(\"emailForm{$user->id}\").submit();'>
                                                <option value='Yes' " . (strtolower($user->email_verified) == 'yes' ? 'selected' : '') . ">Verify</option>
                                                <option value='No' " . (strtolower($user->email_verified) == 'no' ? 'selected' : '') . ">Unverify</option>
                                            </select>
                                            <input type='hidden' name='user_id' value='{$user->id}'>
                                         </form>";

                // Action buttons
                $user->action = "<div class='d-flex align-items-center gap-2'>
                <a href='" . route('register.user.view', $user->id) . "' class='btn btn-sm btn-primary' title='View'>
                    <i class='fa fa-eye'></i>
                </a>
                <a href='" . route('register.user.changePass', $user->id) . "' class='btn btn-sm btn-warning' title='Change Password'>
                    <i class='fa fa-key'></i>
                </a>
                <a class='btn btn-sm btn-danger deletebutton' data-id='" . $user->id . "' title='Delete'>
                     <i class='fas fa-trash-alt'></i>
                </a>
                </div>";
            }

            // Prepare DataTables response
            $response = [
                'draw' => intval($draw),
                'recordsTotal' => $recordsTotal ?? 0, // Ensure this is not null
                'recordsFiltered' => $recordsTotal ?? 0, // Ensure this is not null
                'data' => $users->toArray() ?? [], // Ensure it's an array
            ];

            return response()->json($response);
        }

        return view('admin.register_user.index');
    }


    public function view($id)
    {
        $user = User::findOrFail($id);
        $orders = $user->orders()->paginate(10);
        return view('admin.register_user.details', compact('user', 'orders'));
    }


    public function userban(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->update([
            'status' => $request->status,
        ]);

        Session::flash('success', $user->username . ' status update successfully!');
        return back();
    }


    public function emailStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->update([
            'email_verified' => $request->email_verified,
        ]);

        Session::flash('success', 'Email status updated for ' . $user->username);
        return back();
    }

    public function delete(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        if ($user->conversations()->count() > 0) {
            $convs = $user->conversations()->get();
            foreach ($convs as $key => $conv) {
                @unlink('assets/front/user-suppor-file/' . $conv->file);
                $conv->delete();
            }
        }

        if ($user->courseOrder()->count() > 0) {
            $coursePurchases = $user->courseOrder()->get();
            foreach ($coursePurchases as $key => $cp) {
                @unlink('assets/front/receipt/' . $cp->receipt);
                @unlink('assets/front/invoices/course/' . $cp->invoice);
                $cp->delete();
            }
        }

        if ($user->course_reviews()->count() > 0) {
            $user->course_reviews()->delete();
        }

        if ($user->donation_details()->count() > 0) {
            $donations = $user->donation_details()->get();
            foreach ($donations as $key => $donation) {
                @unlink('assets/front/receipt/' . $donation->receipt);
                $donation->delete();
            }
        }

        if ($user->event_details()->count() > 0) {
            $bookings = $user->event_details()->get();
            foreach ($bookings as $key => $booking) {
                @unlink('assets/front/receipt/' . $booking->receipt);
                @unlink('assets/front/invoices/' . $booking->invoice);
                $booking->delete();
            }
        }

        if ($user->order_items()->count() > 0) {
            $user->order_items()->delete();
        }

        if ($user->package_orders()->count() > 0) {
            $pos = $user->package_orders()->get();
            foreach ($pos as $key => $po) {
                @unlink('assets/front/receipt/' . $po->receipt);
                @unlink('assets/front/invoices/' . $po->invoice);
                $po->delete();
            }
        }

        if ($user->orders()->count() > 0) {
            $orders = $user->orders()->get();
            foreach ($orders as $key => $order) {
                @unlink('assets/front/receipt/' . $order->receipt);
                @unlink('assets/front/invoices/product/' . $order->invoice_number);
                $order->delete();
            }
        }

        if ($user->product_reviews()->count() > 0) {
            $user->product_reviews()->delete();
        }

        if ($user->subscription()->count() > 0) {
            @unlink('assets/front/receipt/' . $user->subscription->receipt);
            @unlink('assets/front/invoices/' . $user->subscription->invoice);
            $user->subscription()->delete();
        }

        if ($user->tickets()->count() > 0) {
            $tickets = $user->tickets()->get();
            foreach ($tickets as $key => $ticket) {
                @unlink('assets/front/user-suppor-file/' . $ticket->zip_file);
                $ticket->delete();
            }
        }

        @unlink('assets/front/img/user/' . $user->photo);
        $user->delete();

        Session::flash('success', 'User deleted successfully!');

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }


        return back();
    }

    // public function bulkDelete(Request $request)
    // {
    //     $ids = $request->ids;

    //     foreach ($ids as $id) {
    //         $user = User::findOrFail($id);

    //         if ($user->conversations()->count() > 0) {
    //             $convs = $user->conversations()->get();
    //             foreach ($convs as $key => $conv) {
    //                 @unlink('assets/front/user-suppor-file/' . $conv->file);
    //                 $conv->delete();
    //             }
    //         }

    //         if ($user->courseOrder()->count() > 0) {
    //             $coursePurchases = $user->courseOrder()->get();
    //             foreach ($coursePurchases as $key => $cp) {
    //                 @unlink('assets/front/receipt/' . $cp->receipt);
    //                 @unlink('assets/front/invoices/course/' . $cp->invoice);
    //                 $cp->delete();
    //             }
    //         }

    //         if ($user->course_reviews()->count() > 0) {
    //             $user->course_reviews()->delete();
    //         }

    //         if ($user->donation_details()->count() > 0) {
    //             $donations = $user->donation_details()->get();
    //             foreach ($donations as $key => $donation) {
    //                 @unlink('assets/front/receipt/' . $donation->receipt);
    //                 $donation->delete();
    //             }
    //         }

    //         if ($user->event_details()->count() > 0) {
    //             $bookings = $user->event_details()->get();
    //             foreach ($bookings as $key => $booking) {
    //                 @unlink('assets/front/receipt/' . $booking->receipt);
    //                 @unlink('assets/front/invoices/' . $booking->invoice);
    //                 $booking->delete();
    //             }
    //         }

    //         if ($user->order_items()->count() > 0) {
    //             $user->order_items()->delete();
    //         }

    //         if ($user->package_orders()->count() > 0) {
    //             $pos = $user->package_orders()->get();
    //             foreach ($pos as $key => $po) {
    //                 @unlink('assets/front/receipt/' . $po->receipt);
    //                 @unlink('assets/front/invoices/' . $po->invoice);
    //                 $po->delete();
    //             }
    //         }

    //         if ($user->orders()->count() > 0) {
    //             $orders = $user->orders()->get();
    //             foreach ($orders as $key => $order) {
    //                 @unlink('assets/front/receipt/' . $order->receipt);
    //                 @unlink('assets/front/invoices/product/' . $order->invoice_number);
    //                 $order->delete();
    //             }
    //         }

    //         if ($user->product_reviews()->count() > 0) {
    //             $user->product_reviews()->delete();
    //         }

    //         if ($user->subscription()->count() > 0) {
    //             @unlink('assets/front/receipt/' . $user->subscription->receipt);
    //             @unlink('assets/front/invoices/' . $user->subscription->invoice);
    //             $user->subscription()->delete();
    //         }

    //         if ($user->tickets()->count() > 0) {
    //             $tickets = $user->tickets()->get();
    //             foreach ($tickets as $key => $ticket) {
    //                 @unlink('assets/front/user-suppor-file/' . $ticket->zip_file);
    //                 $ticket->delete();
    //             }
    //         }

    //         @unlink('assets/front/img/user/' . $user->photo);
    //         $user->delete();
    //     }

    //     Session::flash('success', 'Users deleted successfully!');
    //     return "success";
    // }


    public function changePass($id)
    {
        $data['user'] = User::findOrFail($id);
        return view('admin.register_user.password', $data);
    }


    public function updatePassword(Request $request)
    {

        $messages = [
            'cpass.required' => 'Current password is required',
            'npass.required' => 'New password is required',
            'cfpass.required' => 'Confirm password is required',
        ];

        $request->validate([
            'cpass' => 'required',
            'npass' => 'required',
            'cfpass' => 'required',
        ], $messages);


        $user = User::findOrFail($request->user_id);
        if ($request->cpass) {
            if (Hash::check($request->cpass, $user->password)) {
                if ($request->npass == $request->cfpass) {
                    $input['password'] = Hash::make($request->npass);
                } else {
                    return back()->with('error', __('Confirm password does not match.'));
                }
            } else {
                return back()->with('error', __('Current password Does not match.'));
            }
        }

        $user->update($input);

        Session::flash('success', 'Password update for ' . $user->username);
        return back();
    }
}
