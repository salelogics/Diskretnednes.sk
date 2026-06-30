@extends('layouts.main')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1>Test inzerátu</h1>
    <p>ID: {{ $ad->id }}</p>
    <p>Nickname: {{ $ad->nickname }}</p>
    <p>Status: {{ $ad->status }}</p>
    <p>Subscription: {{ $ad->subscription_status }}</p>
</div>
@endsection 