@extends('layouts.app')

@section('content')
    <main class="page-center">
        <article class="sign-up">
            <h1 class="sign-up__title">Welcome back!</h1>
            <p class="sign-up__subtitle">Sign in to your account to continue</p>
            <form class="sign-up-form form" action="{{ route('login') }}" method="POST">
                @csrf
                <label class="form-label-wrapper">
                    <p class="form-label">Email</p>
                    <input id="email" name="email" type="email" class="form-input" placeholder="Enter your email"
                        required value="{{ old('email') }}">
                    @error('email')
                        <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                    @enderror
                </label>
                <label class="form-label-wrapper">
                    <p class="form-label">Password</p>
                    <input id="password" name="password" type="password" class="form-input"
                        placeholder="Enter your password" required>
                    @error('password')
                        <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                    @enderror
                </label>
                <a class="link-info forget-link" href="{{ route('password.request') }}">Forgot your password?</a>
                <label class="form-checkbox-wrapper">
                    <input id="remember" name="remember" type="checkbox" class="form-checkbox"
                        {{ old('remember') ? 'checked' : '' }}>
                    <span class="form-checkbox-label">Remember me next time</span>
                </label>
                <button type="submit" class="form-btn primary-default-btn transparent-btn">Sign in</button>
            </form>
        </article>
    </main>
@endsection
