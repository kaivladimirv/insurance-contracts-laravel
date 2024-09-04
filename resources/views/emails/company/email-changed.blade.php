<h2>{{__('Hello')}}!</h2>

<p>{{__('company-email-changed.you-have-changed-the-email', ['company_name' => $company->name, 'app_name' => env('APP_NAME')])}}
    .</p>

<p>{{ __('company-email-changed.to-confirm-your-email', ['token' => $company->new_email_confirm_token]) }}</p>
