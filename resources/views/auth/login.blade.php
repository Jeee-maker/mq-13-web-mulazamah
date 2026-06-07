@extends('layouts.app')

@section('title', 'Login - Organisasi MQ-13')

@section('content')
<div class="login-wrapper">
    <div class="glass-panel login-card">
        <div class="login-header">
            <h1 class="text-center">ATAS BERKAT ROCHMAT ALLOH YANG MAHA KUASA</h1>
            <img src="{{ asset('asset-image/MQ.png') }}" alt="Logo MQ-13" class="login-logo">
            <p class="login-subtitle">Selamat Datang di Web Organisasi Persaudaraan Putra Dan Putri MQ-13</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div class="mb-1">{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group" style="text-align: left;">
                <label for="username" class="form-label">Masukkan Username</label>
                <input type="text" id="username" name="username" class="form-control" required autofocus placeholder="Username...">
            </div>

            <div class="form-group" style="text-align: left;">
                <label for="password" class="form-label">Masukkan Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Password...">
            </div>

            <button type="submit" class="btn-gold" style="width: 100%; margin-top: 1rem;">Login</button>
        </form>
    </div>
</div>
@endsection
