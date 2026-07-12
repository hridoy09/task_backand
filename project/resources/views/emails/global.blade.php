@extends('emails.layout')
@section('content')
@php echo $mailBody ?? ''; @endphp
@endsection
