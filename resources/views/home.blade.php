@extends('layout.layout')

@section('content')
    @foreach ($users as $user)
        @continue($user['type'] == 1)

        <li>{{ $loop->count }}</li>

        @break($user['type'] == 5)
    @endforeach




    <h1># Conditional Classes</h1>
    @ php
    $isActive = false;
    $hasError = true;
    @ endphp
    < span @ class(['font-bold'=> $isActive, 'bg-red' => $hasError])></ span>

    <h1># Component</h1>
    <p>Manually Registering Components </p>
    <ul>
        <li> Create a servise provider. see example in RegisterComponentProvider.php</li>
        <li> Register provider in config/app.php 'providers' => [ RegisterComponentProvider:class ]</li>
    </ul>
    @php($data = 'df')
    <x-custom-alert type="error" :message="$data"></x-custom-alert>
@endsection
