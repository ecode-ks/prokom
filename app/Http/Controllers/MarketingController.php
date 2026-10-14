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

    public function erp()
    {
        return view('marketing.module', [
            'page' => 'erp',
            'module' => 'erp',
            'shots' => [
                ['file' => 'dashboard1.png', 'label' => __('marketing.shot_dashboard'), 'caption' => __('marketing.shot_dashboard_d')],
                ['file' => 'projektet1.png', 'label' => __('marketing.shot_projects'), 'caption' => __('marketing.shot_projects_d')],
                ['file' => 'raportiProjektit.png', 'label' => __('marketing.shot_project_report'), 'caption' => __('marketing.shot_project_report_d')],
                ['file' => 'detajetDetyres.png', 'label' => __('marketing.shot_task'), 'caption' => __('marketing.shot_task_d')],
                ['file' => 'checkin.png', 'label' => __('marketing.shot_checkin'), 'caption' => __('marketing.shot_checkin_d')],
                ['file' => 'listacheckin.png', 'label' => __('marketing.shot_checkin_list'), 'caption' => __('marketing.shot_checkin_list_d')],
                ['file' => 'hartaPrezences.png', 'label' => __('marketing.shot_map'), 'caption' => __('marketing.shot_map_d')],
            ],
        ]);
    }

    public function crm()
    {
        return view('marketing.module', [
            'page' => 'crm',
            'module' => 'crm',
            'shots' => [
                ['file' => 'crm.png', 'label' => __('marketing.shot_crm'), 'caption' => __('marketing.shot_crm_d')],
                ['file' => 'detajetTransaksionit.png', 'label' => __('marketing.shot_transaction'), 'caption' => __('marketing.shot_transaction_d')],
                ['file' => 'oferta1.png', 'label' => __('marketing.shot_offers'), 'caption' => __('marketing.shot_offers_d')],
            ],
        ]);
    }

    public function hr()
    {
        return view('marketing.module', [
            'page' => 'hr',
            'module' => 'hr',
            'shots' => [
                ['file' => 'punetoret.png', 'label' => __('marketing.shot_staff'), 'caption' => __('marketing.shot_staff_d')],
                ['file' => 'pushimet.png', 'label' => __('marketing.shot_vacations'), 'caption' => __('marketing.shot_vacations_d')],
                ['file' => 'kaluklimiPagave.png', 'label' => __('marketing.shot_salary'), 'caption' => __('marketing.shot_salary_d')],
            ],
        ]);
    }

    public function faq()
    {
        return view('marketing.faq', ['page' => 'faq']);
    }

    public function contact()
    {
        return view('marketing.contact', ['page' => 'contact']);
    }

    public function privacy()
    {
        return view('marketing.privacy', ['page' => 'privacy']);
    }

    public function setLocale(Request $request, string $locale)
    {
        if (!in_array($locale, ['sq', 'en', 'de'], true)) {
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
                    "WORKOM demo request\n\n"
                    . "Name: {$data['name']}\n"
                    . "Company: {$data['company']}\n"
                    . "Email: {$data['email']}\n"
                    . "Phone: " . ($data['phone'] ?? '-') . "\n"
                    . "Intent: {$data['intent']}\n\n"
                    . $data['message'],
                    function ($message) use ($to, $data) {
                        $message->to($to)
                            ->replyTo($data['email'], $data['name'])
                            ->subject('WORKOM — ' . $data['intent'] . ' — ' . $data['company']);
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
