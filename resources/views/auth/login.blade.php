<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <br>
            <h4 id="preloader" style="display: none;" class="text-center">Loading...</h4>
            <div id="errorJS" style="display: none;" class="alert alert-danger"></div>
            <select style="display: none;" name="account" id="account" class="form-control">
            </select>
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3" id="loginButton">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript">
        let searchEmail = '';
        let timeController = '';

        function switchPreloader(casePreloader){
            const myButton = document.getElementById('loginButton');
            switch (casePreloader) {
                case 'preloader':
                    myButton.style.opacity = 0.7;
                    myButton.disabled = true;
                    $('#account').css('display', 'none');
                    $('#errorJS').css('display', 'none');
                    $('#preloader').css('display', '');
                    if ($("span.select2-container").length > 0)
                    {
                        $("#account").select2('destroy');
                    }
                    break;
                case 'account':
                    myButton.style.opacity = 1;
                    myButton.disabled = false;
                    $('#errorJS').css('display', 'none');
                    $('#preloader').css('display', 'none');
                    $('#account').css('display', '');
                    break;
                case 'error':
                    myButton.style.opacity = 0.7;
                    myButton.disabled = true;
                    $('#account').css('display', 'none');
                    $('#preloader').css('display', 'none');
                    $('#errorJS').css('display', '');
                    break;
                default:
                    myButton.style.opacity = 0.7;
                    myButton.disabled = true;
                    $('#account').css('display', 'none');
                    $('#preloader').css('display', 'none');
                    $('#errorJS').css('display', 'none');
                    break;
            };
        };

        function ajaxCall(){
            const params = {'searchEmail': searchEmail};
            $.ajax({
              type: 'POST',
              data:  params,
              url: "{!! url('api/accounts') !!}",
            })
            .done(function(response){
              createOption(response);
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                $('#errorJS').empty();
                $('#errorJS').append('Vuelva a ingresar su email');
                switchPreloader('error');
            });
        };

        function createOption(data) {
            if (data.error) {
                $('#errorJS').empty();
                $('#errorJS').append(data.error);
                switchPreloader('error');
            } else if (data.accounts){
                var htmlTags = '';
                data.accounts.forEach((valued, index) => {
                    htmlTags += '<option value="'+  valued.id + '">' + valued.name + '</option>';
                });
                $('#account').empty();
                $('#account').append(htmlTags);
                switchPreloader('account');
                $('#account').select2();
            }
        };

        $(window).on("load", function () {
            const myButton = document.getElementById('loginButton');
            myButton.style.opacity = 0.7;
            $('input#email').on('change', function () {
                switchPreloader('preloader');
                if($("input#email").val().indexOf('@', 0) == -1 || $("input#email").val().indexOf('.', 0) == -1) {
                    $('#errorJS').empty();
                    $('#errorJS').append('Introduzca un email valido.');
                    switchPreloader('error');
                    return false;
                };
                searchEmail = $(this).val();
                clearTimeout(timeController);
                timeController = setTimeout(ajaxCall, 1000);
            });
        });
    </script>
</x-guest-layout>
