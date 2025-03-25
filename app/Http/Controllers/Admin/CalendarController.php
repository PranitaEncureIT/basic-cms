<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\Language;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $lang_code = isset($request->language) ?  $request->language : 'en';
		$lang = Language::where('code', $lang_code)->first();

        $lang_id = $lang->id;
        $data['events'] = CalendarEvent::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();

        $data['lang_id'] = $lang_id;

        return view('admin.calendar.index', $data);
    }

    public function store(Request $request)
    {
        // dd($request->start_date, $request->end_date);
        $rules = [
            'title' => 'required|max:255',
            'start_date' => 'required',
            'end_date' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        try {
            // Convert date format properly
            $startDate = Carbon::createFromFormat('m/d/Y h:i A', trim($request->start_date))->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('m/d/Y h:i A', trim($request->end_date))->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format. Expected MM/DD/YYYY hh:mm A.']);
        }

        $calendar = new CalendarEvent;
        $calendar->title = $request->title;
        $calendar->start_date = $startDate;
        $calendar->end_date = $endDate;
        $calendar->save();

        Session::flash('success', 'Event added to calendar successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $rules = [
            'title' => 'required|max:255',
            'start_date' => 'required',
            'end_date' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            // Detect and fix the format
            $startDate = Carbon::parse(trim($request->start_date))->format('Y-m-d H:i:s');
            $endDate = Carbon::parse(trim($request->end_date))->format('Y-m-d H:i:s');

        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format. Expected MM/DD/YYYY hh:mm A.']);
        }

        $calendar = CalendarEvent::findOrFail($request->event_id);
        $calendar->title = $request->title;
        $calendar->start_date = $startDate;
        $calendar->end_date = $endDate;
        $calendar->save();

        Session::flash('success', 'Event date updated in calendar successfully!');
        return "success";
    }

    public function delete(Request $request)
    {
        $calendar = CalendarEvent::findOrFail($request->event_id);
        $calendar->delete();

        Session::flash('success', 'Event deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $calendar = CalendarEvent::findOrFail($id);
            $calendar->delete();
        }

        Session::flash('success', 'Events deleted successfully!');
        return "success";
    }
}
