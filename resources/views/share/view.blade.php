@extends('layouts.app')

@section('title', 'View Share - ShareDrop')

<style>
    .share-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .share-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .share-icon {
        font-size: 3rem;
        color: #4e73df;
        margin-bottom: 1rem;
    }
    .share-content {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
    }
    .file-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid #eee;
    }
    .file-item:last-child {
        border-bottom: none;
    }
    .file-icon {
        font-size: 1.5rem;
        margin-right: 1rem;
        color: #4e73df;
    }
    .file-info {
        flex-grow: 1;
    }
    .file-name {
        font-weight: 500;
    }
    .file-size {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .share-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 2rem;
    }
    .qr-code {
        text-align: center;
        margin: 2rem 0;
    }
    .share-url {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        word-break: break-word;
        margin-bottom: 1rem;
    }
    .expiry-info {
        text-align: center;
        color: #6c757d;
        margin-top: 1rem;
    }
</style>

@section('content')
<div class="share-container">
    <div class="share-header">
        <div class="share-icon">
            @if($share->type === 'text')
                <i class="fas fa-font"></i>
            @else
                <i class="fas fa-file-archive"></i>
            @endif
        </div>
        <h2>
            @if($share->type === 'text')
                Text Share
            @else
                {{ $share->files->count() }} File{{ $share->files->count() > 1 ? 's' : '' }}
            @endif
        </h2>
        @if($share->expires_at)
            <p class="expiry-info">
                Expires {{ $share->expires_at->diffForHumans() }}
                ({{ $share->expires_at->format('M j, Y g:i A') }})
            </p>
        @endif
    </div>

    <div class="share-url">
        <strong>Share URL:</strong><br>
        {{ route('share.view', $share->share_id) }}
    </div>

    <div class="qr-code">
        <canvas id="qrcode"></canvas>
        <p>Scan to share</p>
    </div>

    @if($share->type === 'text')
        <div class="share-content">
            <pre style="white-space: pre-wrap; word-wrap: break-word;">{{ $share->content }}</pre>
        </div>
    @else
        <div class="share-content">
            @foreach($share->files as $file)
                <div class="file-item">
                    <div class="file-icon">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="file-info">
                        <div class="file-name">{{ $file->original_name }}</div>
                        <div class="file-size">{{ formatBytes($file->size) }}</div>
                    </div>
                    <div>
                        <a href="{{ route('share.file.download', ['shareId' => $share->share_id, 'fileId' => $file->id]) }}" 
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="share-actions">
        <button onclick="copyToClipboard('{{ route('share.view', $share->share_id) }}')" 
                class="btn btn-outline-secondary">
            <i class="fas fa-copy"></i> Copy Link
        </button>
        <a href="/" class="btn btn-outline-primary">
            <i class="fas fa-plus"></i> Create New Share
        </a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
<script>
    QRCode.toCanvas(document.getElementById('qrcode'), '{{ route('share.view', $share->share_id) }}', {
        width: 200,
        margin: 2,
        color: {
            dark: '#000000',
            light: '#ffffff'
        }
    }, function (error) {
        if (error) console.error(error);
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function () {
            alert('Link copied to clipboard!');
        }, function (err) {
            console.error('Could not copy text: ', err);
        });
    }
</script>
@endsection
