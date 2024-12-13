@extends('layouts.app')

@section('content')
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-r from-gray-700 via-gray-900 to-black py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-gray-800 shadow-xl rounded-lg p-8 space-y-8">
            <div class="flex justify-center">
                <img class="h-12 w-auto" src="{{ asset('logo_azul.png') }}" alt="Logo">
            </div>
            <div>
                <h2 class="mt-6 text-center text-3xl leading-9 font-extrabold text-gray-100">
                    {{ __('Login') }}
                </h2>
                <p class="mt-2 text-center text-sm leading-5 text-gray-400">
                    {{ __('Acesse sua conta') }}
                </p>
            </div>
            <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300">
                            {{ __('Email Address') }}
                        </label>
                        <input id="email" name="email" type="email" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-700 placeholder-blac text-black rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-500 @enderror"
                            value="{{ old('email') }}" autocomplete="email" autofocus>
                        @error('email')
                            <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300">
                            {{ __('Password') }}
                        </label>
                        <input id="password" name="password" type="password" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-700 placeholder-black text-black rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-500 @enderror"
                            autocomplete="current-password">
                        @error('password')
                            <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-700 rounded"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="ml-2 block text-sm text-gray-300">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                    {{-- @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}"
                                class="font-medium text-blue-500 hover:text-blue-400">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        </div>
                    @endif --}}
                </div>
                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg hover:shadow-xl">
                        {{ __('Entrar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
