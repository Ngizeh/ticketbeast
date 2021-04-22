@extends('layouts.app')

@section('content')
<div class="container my-10 mx-auto w-full px-4 md:w-2/3 md:p-4 lg:w-2/5 lg:p-5">
    <div class="bg-white p-12 space-y-6 shadow-lg rounded">
        <div class="pb-3">
            <h3 class="text-5xl text-gray-800 font-bold py-2">{{ $concert->title }}</h3>
            <p class="text-gray-8   00 text-xl font-medium tracking-wide">{{ $concert->subtitle }}</p>
        </div>
        <div class="flex items-center">
            <span>
                <svg class="h-6 w-6 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M1 4c0-1.1.9-2 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4zm2 2v12h14V6H3zm2-6h2v2H5V0zm8 0h2v2h-2V0zM5 9h2v2H5V9zm0 4h2v2H5v-2zm4-4h2v2H9V9zm0 4h2v2H9v-2zm4-4h2v2h-2V9zm0 4h2v2h-2v-2z"/>
                </svg>
            </span>
             <p class="text-gray-700 font-semibold pl-3 text-xl">{{ $concert->formatted_date }}</p>
        </div>
        <div class="flex items-center">
            <span>
                <svg class="h-6 w-6 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-1-7.59V4h2v5.59l3.95 3.95-1.41 1.41L9 10.41z"/>
                </svg>
            </span>
            <p class="text-gray-700 font-semibold pl-3 text-xl">Door open at {{ $concert->formatted_time }}</p>
        </div>
        <div class="flex items-center">
            <span>
                <svg class="h-6 w-6 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm1-5h1a3 3 0 0 0 0-6H7.99a1 1 0 0 1 0-2H14V5h-3V3H9v2H8a3 3 0 1 0 0 6h4a1 1 0 1 1 0 2H6v2h3v2h2v-2z"/>
                </svg>
            </span>
            <p class="text-gray-700 font-semibold pl-3 text-xl">{{ $concert->formatted_price }} </p>
        </div>
        <div class="flex">
            <span>
                <svg class="h-6 w-6 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M10 20S3 10.87 3 7a7 7 0 1 1 14 0c0 3.87-7 13-7 13zm0-11a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                </svg>
            </span>
            <div class="pl-3 space-y-1">
                <p class="text-gray-700 font-semibold text-xl">{{ $concert->avenue }} </p>
                <p class="text-gray-500 text-lg font-meduim">{{ $concert->address }} </p>
                <p class="text-gray-500 text-lg font-meduim">{{ $concert->city}}, {{ $concert->state }} {{ $concert->zip }} </p>
            </div>
        </div>
        <div class="flex flex-col">
            <span class="flex items-center">
                <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zM9 11v4h2V9H9v2zm0-6v2h2V5H9z"/>
                </svg>
                <p class="pl-3 text-gray-700 font-semibold text-xl">Additional Information</p>
            </span>
            <div class="pl-10 py-3">
                <p class="text-gray-500 text-lg font-meduim">{{ $concert->additional_information }} </p>
            </div>
        </div>
        <ticket-checkout
            :price ="{{ $concert->formatted_price }}"
            :concert-id="{{ $concert->id }}"
            :concert-title="'{{ $concert->title }}'"
        ></ticket-checkout>
    </div>
    <div class="w-1/3 mx-auto mt-10 text-gray-500 font-semibold text-xl sm:w-full text-center">Powered by TicketBeast</div>
</div>

@endsection
