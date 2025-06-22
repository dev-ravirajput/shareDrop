@extends('layouts.app')

@section('title', 'Share Expired - ShareDrop')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-clock me-2"></i> Share Expired
                    </h3>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <img src="{{ asset('image/waiting.gif') }}" 
                        alt="Hourglass animation" 
                        class="mb-3 h-full w-auto">
                    </div>
                    <h2 class="mb-3">This share has expired</h2>
                    <p class="text-muted mb-4">
                        The content you're trying to access is no longer available as it has passed its expiration date.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="/" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i> Return Home
                        </a>
                    </div>

                    @if($share->expires_at ?? false)
                        <p class="text-muted small mt-4">
                            Expired {{ $share->expires_at->diffForHumans() }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection