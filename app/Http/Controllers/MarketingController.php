<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MarketingController extends Controller
{
    public function home()
    {
        return view('marketing.home', ['page' => 'home']);
    }

    public function product()
    {
        return view('marketing.product', ['page' => 'product']);
    }

    public function faq()
    {
        return view('marketing.faq', ['page' => 'faq']);
    }

    public function contact()
    {
        return view('marketing.contact', ['page' => 'contact']);
    }

    public function setLocale(Request $request, string $locale)
    {
        if (!in_array($locale, ['sq', 'en'], true)) {
            $locale = 'sq';
        }

        session()->put('marketing_locale', $locale);
        app()->setLocale($locale);

        $fallback = route('home');
        $previous = url()->previous();

        if ($previous && str_starts_with($previous, rtrim(url('/'), '/') )) {
            return redirect()->to($previous);
        }

        return redirect()->to($fallback);
    }

    public function submitContact(Request $request)
    {
        $locale = session('marketing_locale', 'sq');
        app()->setLocale($locale);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:120',
            'company' => 'required|string|min:2|max:120',
            'email' => 'required|email|max:120',
            'phone' => 'nullable|string|max:40',
            'intent' => 'required|in:demo,pricing,question',
            'message' => 'required|string|min:10|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('marketing.contact')
                ->withErrors($validator)
                ->withInput()
                ->with('contact_error', __('marketing.contact_error'));
        }

        $data = $validator->validated();

        try {
            $to = config('mail.from.address');
            if ($to) {
                Mail::raw(
                    "WorkOM demo request\n\n"
                    . "Name: {$data['name']}\n"
                    . "Company: {$data['company']}\n"
                    . "Email: {$data['email']}\n"
                    . "Phone: " . ($data['phone'] ?? '-') . "\n"
                    . "Intent: {$data['intent']}\n\n"
                    . $data['message'],
                    function ($message) use ($to, $data) {
                        $message->to($to)
                            ->replyTo($data['email'], $data['name'])
                            ->subject('WorkOM — ' . $data['intent'] . ' — ' . $data['company']);
                    }
                );
            }
        } catch (\Throwable $e) {
            // Mail may be unconfigured locally.
        }

        return redirect()
            ->route('marketing.contact')
            ->with('contact_success', __('marketing.contact_success'));
    }
}
