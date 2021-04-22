@extends('layouts.app')

@section('content')
<div class="bg-gray-50 -mb-20">
	<div class="container mx-auto w-2/3 pb-20">
		<div class="flex justify-between items-center border-b border-gray-400 pb-6 pt-20">
			<h2 class="text-3xl text-gray-700">Order Summary</h2>
			<a class="text-xl text-blue-700">#{{ $order->confirmation_order }}</a>
		</div>
		<div class="pt-4 space-y-2 border-b border-gray-400 py-2">
			<p class=" text-gray-700 font-bold text-2xl">
				Order Total ${{ number_format($order->amount/100, 2) }}
			</p>
			<p class=" text-gray-400 text-2xl">Billed to card #: **** **** **** {{ $order->card_last_four }} </p>
		</div>
		<div class="pt-10 pb-6">
			<p class="text-3xl font-normal text-gray-600">Your tickets</p>
		</div>
		<div>
			@foreach ($order->tickets as $ticket)
			<div class="shadow-md">
				<div class="bg-gray-600 flex justify-between items-center px-8 py-8 rounded-t-sm ">
					<div class="">
						<p class="text-3xl font- text-gray-200">{{ $ticket->concert->title }}</p>
						<p class="text-gray-300 font-light text-xl">{{ $ticket->concert->subtitle }}</p>
					</div>
					<div>
						<p class="text-xl text-gray-100 font-bold">
							General Admission
						</p>
						<p class="text-gray-200 text-lg">
							Admit one
						</p>
					</div>
				</div>
				<div class="bg-white mb-10">
					<div class="flex justify-between border-b border-gray-400 mx-10 py-6">
						<div class="flex">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="w-8 h-8 stroke-current stroke-0 fill-current text-blue-400 mt-1">
								<path d="M1 4c0-1.1.9-2 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4zm2 2v12h14V6H3zm2-6h2v2H5V0zm8 0h2v2h-2V0zM5 9h2v2H5V9zm0 4h2v2H5v-2zm4-4h2v2H9V9zm0 4h2v2H9v-2zm4-4h2v2h-2V9zm0 4h2v2h-2v-2z"/>
							</svg>
							<div class="pl-4">
								<time datetime="{{ $ticket->concert->date->format('Y-m-d H:i') }}" class="text-xl font-bold text-gray-700">
									{{ $ticket->concert->date->format('l, F j, Y') }}
								</time>
								<div class="text-lg text-gray-400 font-semibold">
									Doors open at {{ $ticket->concert->date->format('g:ia') }}
								</div>
							</div>
						</div>
						<div class="flex">
							<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 stroke-current stroke-0 fill-current text-blue-400 mt-1"
							viewBox="0 0 20 20"><path d="M10 20S3 10.87 3 7a7 7 0 1 1 14 0c0 3.87-7 13-7 13zm0-11a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
							</svg>
							<div class="pl-4">
								<p class="text-xl font-bold text-gray-700">{{ $ticket->concert->avenue }}</p>
								<p>{{ $ticket->concert->address }}</p> 
								<p>{{ $ticket->concert->city }}, {{ $ticket->concert->state }} {{ $ticket->concert->zip }}</p>
							</div>
						</div>
					</div>
					<div class="flex justify-between mx-10 py-6">
						<p class="text-3xl font-medium">{{ $ticket->ticket_code }}</p>
						<p class="text-lg">{{ $order->email }}</p>
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>
</div>

@endsection