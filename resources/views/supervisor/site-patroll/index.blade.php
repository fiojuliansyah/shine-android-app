@extends('layouts.app')

@section('title', 'Site Patroll')
@section('content')
<div class="page-content pt-5">

    <div class="content mt-0 mb-0">
        <div class="list-group list-custom-large">

            @foreach ($sites as $site)
                <a href="{{ route('supervisor.site-patroll.show', $site->id) }}" target="_blank">
                    <i class="fas fa-map-marker-alt font-20 color-blue-dark"></i>

                    <span>{{ $site->name }}</span>
                    <strong>{{ $site->address ?? 'Alamat tidak tersedia' }}</strong>

                    <i class="fa fa-angle-right"></i>
                </a>
            @endforeach

        </div>
    </div>

</div>
@endsection
