@extends('layouts.index')
@section('title', 'Perangkingan')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/index.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h2>{{ $userTryout->tryout->nama }}</h2>
    </div>
    <div class="col-12">
        <p>
            {{ \Carbon\Carbon::now()->locale('id_ID')->translatedFormat('l, j F Y') }}
        </p>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0">Jumlah Peserta: {{$totalUser}}</h6>
            </div>
            <div class="card-body" style="overflow-x: auto;">
                @foreach ($allUserRank as $rank)
                <div
                    class="row py-2 align-items-center {{ $rank->user_id == Auth::user()->id ? 'alternative-background' : ''}}">
                    <div class="col-1">{{$rank->rank}}</div>
                    <div class="col-4">
                        <div class="d-flex">
                            <div class="col-2">
                                <img src="{{$rank->user->avatar ?? 'https://ui-avatars.com/api/?name='.substr($rank->user->name, 0, 1).'&color=FFFFFF&background=09090b'}}"
                                    alt="" class="rounded-circle" style="width: 50px; height: 50px;">
                            </div>
                            <div class="d-flex flex-column">
                                <p class="mb-0">
                                    {{$rank->user->name}}
                                </p>
                                <p class="mb-0 text-muted">
                                    {{$rank->user->email}}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 primary-text">{{ strtoupper($rank->status_lulus) }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection