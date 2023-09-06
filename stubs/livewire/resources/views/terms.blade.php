@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Terms Of Service')

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
@endsection

@section('content')
<div class="position-relative">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">
      <div class="card p-2">
        <!-- Logo -->
        <div class="app-brand justify-content-center mt-5">
          <a href="{{url('/')}}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'var(--bs-primary)'])</span>
            <span class="app-brand-text demo text-heading fw-bold">{{config('variables.templateName')}}</span>
          </a>
        </div>
        <!-- /Logo -->
        <div class="card-body mt-2">{!! $terms !!}</div>
      </div>
      <img alt="mask" src="{{asset('assets/img/illustrations/auth-basic-forgot-password-mask-'.$configData['style'].'.png') }}" class="authentication-image d-none d-lg-block" data-app-light-img="illustrations/auth-basic-forgot-password-mask-light.png" data-app-dark-img="illustrations/auth-basic-forgot-password-mask-dark.png" />
    </div>
  </div>
</div>
@endsection
