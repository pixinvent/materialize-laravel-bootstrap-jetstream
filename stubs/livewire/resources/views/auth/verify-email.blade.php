@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Verify Email')

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
@endsection

@section('content')
<div class="authentication-wrapper authentication-basic px-4">
  <div class="authentication-inner py-4">

    <!-- Logo -->
    <div class="app-brand justify-content-center mb-5">
      <a href="{{url('/')}}" class="app-brand-link gap-2">
        <span class="app-brand-logo demo bg-primary">@include('_partials.macros',["height"=>20,"withbg"=>'fill: #fff;'])</span>
        <span class="app-brand-text demo text-body fw-bold">{{config('variables.templateName')}}</span>
      </a>
    </div>
    <!-- /Logo -->

    <!--  Verify email -->
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg position-relative py-sm-5 px-4 py-4">
      <div class="w-px-400 mx-auto pt-5 pt-lg-0">
        <h4 class="mb-2">Verify your email ✉️</h4>

        @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success" role="alert">
          <div class="alert-body">
            A new verification link has been sent to the email address you provided during registration.
          </div>
        </div>
        @endif
        <p class="text-start">
          Account activation link sent to your email address: <span class="fw-medium">{{Auth::user()->email}}</span> Please follow the link inside to continue.
        </p>
        <div class="mt-4 d-flex flex-column justify-content-between gap-2">
          <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-100 btn btn-label-secondary">
              click here to request another
            </button>
          </form>

            <form method="POST" action="{{route('logout')}}">
              @csrf

            <button type="submit" class="w-100 btn btn-danger">
              Log Out
            </button>
          </form>
        </div>
      </div>
    </div>
    <!-- / Verify email -->
  </div>
</div>
@endsection