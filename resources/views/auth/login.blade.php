@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="login-header">
    <div class="login-logo">
        <i class="fas fa-plane"></i>
        <span>AS Reporting</span>
    </div>
    <p class="login-title">Sistema de Reportes de AS Travel Perú</p>
</div>

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<form method="POST" action="{{ route('login') }}" id="loginForm">
    @csrf
    <div class="form-group">
        <i class="fas fa-envelope"></i>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder=" " required autofocus>
        <label for="email" class="floating-label">Correo electrónico</label>
    </div>
    
    <div class="form-group">
        <i class="fas fa-lock"></i>
        <input type="password" class="form-control" id="password" name="password" placeholder=" " required>
        <label for="password" class="floating-label">Contraseña</label>
    </div>
    
    <div class="form-actions">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">Recordarme</label>
        </div>
        {{-- <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a> --}}
    </div>
    
    <button type="submit" class="btn-login" id="loginButton">
        <span id="buttonText">Iniciar Sesión</span>
        <span id="buttonSpinner" style="display: none;">
            <i class="fas fa-spinner fa-spin"></i> Procesando...
        </span>
    </button>
    
    {{-- <div class="divider">
        <span>O</span>
    </div>
    
    <div class="social-login">
        <a href="#" class="social-btn google">
            <i class="fab fa-google"></i>
        </a>
        <a href="#" class="social-btn facebook">
            <i class="fab fa-facebook-f"></i>
        </a>
    </div>
    
    <div class="signup-link">
        ¿No tienes una cuenta? <a href="#">Regístrate</a>
    </div> --}}
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');
        const buttonText = document.getElementById('buttonText');
        const buttonSpinner = document.getElementById('buttonSpinner');
        
        loginForm.addEventListener('submit', function() {
            // Deshabilitar el botón y mostrar spinner
            loginButton.disabled = true;
            buttonText.style.display = 'none';
            buttonSpinner.style.display = 'inline';
        });
    });
</script>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginButton = document.getElementById('loginButton');
        
        function validateForm() {
            const emailValid = emailInput.value.trim() !== '' && emailInput.checkValidity();
            const passwordValid = passwordInput.value.trim() !== '';
            
            loginButton.disabled = !(emailValid && passwordValid);
        }
        
        emailInput.addEventListener('input', validateForm);
        passwordInput.addEventListener('input', validateForm);
        
        // Validación inicial
        validateForm();
    });
</script>
@endpush