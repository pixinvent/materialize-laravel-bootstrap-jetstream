@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Confirm Password')

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">
  <!-- Logo -->
  <a href="{{url('/')}}" class="auth-cover-brand d-flex align-items-center gap-2">
    <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'#666cff'])</span>
    <span class="app-brand-text demo text-heading fw-bold">{{config('variables.templateName')}}</span>
  </a>
  <!-- /Logo -->
  <div class="authentication-inner row m-0">

    <!-- /Left Section -->
    <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center justify-content-center p-5 pb-2">
      <img src="{{asset('assets/img/illustrations/auth-forgot-password-illustration-'.$configData['style'].'.png') }}" class="auth-cover-illustration w-100" alt="auth-illustration" data-app-light-img="illustrations/auth-forgot-password-illustration-light.png" data-app-dark-img="illustrations/auth-forgot-password-illustration-dark.png" />
      <img src="{{asset('assets/img/illustrations/auth-cover-forgot-password-mask-'.$configData['style'].'.png') }}" class="authentication-image" alt="mask" data-app-light-img="illustrations/auth-cover-forgot-password-mask-light.png" data-app-dark-img="illustrations/auth-cover-forgot-password-mask-dark.png" />
    </div>
    <!-- /Left Section -->

    <!-- Confirm Password -->
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
        <h4 class="mb-2 fw-semibold">Confirm Password? 🔒</h4>
        <p class="mb-4">Please confirm your password before continuing.</p>
        <form id="twoStepsForm" class="mb-3" action="{{ route('password.confirm') }}" method="POST">
          @csrf
          <div class="form-password-toggle">
            <div class="input-group input-group-merge mb-3 @error('password') is-invalid @enderror">
              <div class="form-floating form-floating-outline">
                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                <label for="password">Enter Password</label>
              </div>
              <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
              @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>
          </div>
          <button class="btn btn-primary d-grid w-100">Confirm Password</button>
        </form>
        @if (!Auth::check())
        <div class="text-center">
          @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}">
            {{ __('Forgot Your Password?') }}
          </a>
          @endif
        </div>
        @endif
      </div>
    </div>
    <!-- /Confirm Password -->
  </div>
</div>
@endsection
