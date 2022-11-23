@extends('layout.layout')
{{-- @dump($users) --}}
@foreach ($users as $user)
    @continue($user['type'] == 1)
 
    <li>{{ $loop->count }}</li>
 
    @break($user['type'] == 5)
@endforeach
