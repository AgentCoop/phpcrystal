@extends('email.layouts.master.index')

@section('content')
    @include('email.layouts.master.incs.para_start')
    Error code: {{ $errCode }}
    @include('email.layouts.master.incs.para_end')

    @include('email.layouts.master.incs.para_start')
    Error message: {{ $errMessage }}
    @include('email.layouts.master.incs.para_end')
@endsection