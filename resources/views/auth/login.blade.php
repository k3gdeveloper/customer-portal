@extends('layouts.app')

@section('content')
    <main class="page-center">
        <article class="sign-up flex flex-col items-center">

            <form class="sign-up-form form w-96" action="{{ route('login') }}" method="POST">
                <!-- Logo no meio -->
                <div class="logologin"></div>
                <!-- Texto de login abaixo da logo -->
                <h2 class="login-text mt-4 mb-8" style="text-align: center;">Login</h2>
                @csrf
                <label class="form-label-wrapper">
                    <p class="form-label">Email Address</p>
                    <input id="email" name="email" type="email" class="form-input" required
                        value="{{ old('email') }}">
                    @error('email')
                        <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                    @enderror
                </label>
                <label class="form-label-wrapper">
                    <p class="form-label">Password</p>
                    <input id="password" name="password" type="password" class="form-input" required>
                    @error('password')
                        <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                    @enderror
                </label>
                <label class="form-checkbox-wrapper">
                    <input id="remember" name="remember" type="checkbox" class="form-checkbox"
                        {{ old('remember') ? 'checked' : '' }}>
                    <span class="form-checkbox-label">Remember me</span>
                </label>
                <button type="submit" class="form-btn primary-default-btn transparent-btn">Entrar</button>
            </form>
        </article>
        <footer class="footer-fixed">
            <p class="text-xs">&copy; Todos os direitos Reservados <span id="currentYear"></span> - Criado por K3G
                Solutions</p>
        </footer>
    </main>

    <script>
        // Define o ano atual no elemento com id "currentYear"
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    </script>
@endsection
