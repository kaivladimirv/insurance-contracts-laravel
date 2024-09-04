<h2>{{__('Hello')}}!</h2>

<p>{{__('company-registered.has-been-registered', ['company_name' => $company->name, 'app_name' => env('APP_NAME')])}}.</p>

<p>{{__('company-registered.to-confirm-your-account', ['token' => $company->email_confirm_token])}}</p>
