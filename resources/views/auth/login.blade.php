@extends('layouts.app')

@section('content')
    <main class="page-center">
        <article class="sign-up">

            <form class="sign-up-form form" action="{{ route('login') }}" method="POST">
                <!-- Logo no meio -->
                <div class="logo">
                    <img src="URL_DA_LOGO_AQUI" alt="Logo" style="display: block; margin: 0 auto;">
                </div>
                <!-- Texto de login abaixo da logo -->
                <h2 class="login-text" style="text-align: center; margin-top: 20px;">Login</h2>
                <p style="text-align: center;">Acesse sua conta</p>
                @csrf
                <label class="form-label-wrapper">
                    <p class="form-label">Email Address</p>
                    <input id="email" name="email" type="email" class="form-input" {{-- placeholder="Enter your email" --}} required
                        value="{{ old('email') }}">
                    @error('email')
                        <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                    @enderror
                </label>
                <label class="form-label-wrapper">
                    <p class="form-label">Password</p>
                    <input id="password" name="password" type="password" class="form-input" {{-- placeholder="Enter your password" --}} required>
                    @error('password')
                        <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                    @enderror
                </label>
                {{-- <a class="link-info forget-link" href="{{ route('password.request') }}">Forgot your password?</a> --}}
                <label class="form-checkbox-wrapper">
                    <input id="remember" name="remember" type="checkbox" class="form-checkbox"
                        {{ old('remember') ? 'checked' : '' }}>
                    <span class="form-checkbox-label">Remember me</span>
                </label>
                <button type="submit" class="form-btn primary-default-btn transparent-btn">Entrar</button>
            </form>
        </article>
    </main>
@endsection
