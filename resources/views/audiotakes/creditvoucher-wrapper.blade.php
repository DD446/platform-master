@php
$hideNav = true;
@endphp
@extends('main')

@section('content')

    <div class="page-mt">
        @include('audiotakes.creditvoucher')
    </div>

    @push('styles')
        <style>
            .page-mt {
                margin-top: 6rem;
            }
            img.img-fluid {
                width: 285px;
            }
            .table thead.bg-secondary {
                background: #f8f9fa !important;
            }
        </style>
    @endpush

@endsection('content')
