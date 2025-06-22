@extends('layouts.app')

@section('title', 'ShareDrop | Instant File & Text Sharing')

@section('content')
<link rel="icon" href="{{ asset('image/share.png') }}" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>   

    <div class="container">
        <!-- Header -->
        <header>
            <div class="logo-icon floating">
                <i class="fas fa-share-nodes"></i>
            </div>
            <h1>ShareDrop</h1>
            <p class="subtitle">Instantly share files and text across all your devices with a simple link or QR code</p>
        </header>

        <!-- Main Card -->
        <div class="main-card">
            <!-- Tab Navigation -->
            <div class="tab-nav">
                <button id="file-tab" class="tab-button active" data-tab="file">
                    <i class="fas fa-file-upload"></i>Share Files
                </button>
                <button id="text-tab" class="tab-button" data-tab="text">
                    <i class="fas fa-font"></i>Share Text
                </button>
            </div>

            <!-- File Tab Content -->
            <div id="file-tab-content" class="tab-content active">
                <!-- Drop Zone -->
                <div id="drop-zone">
                    <div class="drop-zone-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h3>Drag & Drop Files Here</h3>
                    <p class="instructions">Or click to browse your files. Supports multiple files up to 10MB each.</p>
                    <input type="file" id="file-input" multiple>
                    <button id="browse-btn"><i class="fas fa-folder-open"></i> Select Files</button>
                </div>

                <!-- Selected Files Preview -->
                <div id="file-preview">
                    <h4><i class="fas fa-paperclip"></i> Selected Files</h4>
                    <div id="file-list">
                        <!-- Files will be listed here dynamically -->
                    </div>
                </div>
            </div>

            <!-- Text Tab Content -->
            <div id="text-tab-content" class="tab-content">
                <label for="share-text">Enter text to share:</label>
                <textarea id="share-text" placeholder="Type or paste your text here..."></textarea>
                <div class="char-count"><span id="char-count">0</span>/5000 characters</div>
            </div>

            <!-- Settings Section -->
            <div class="settings-section">
                <h4><i class="fas fa-cog"></i> Sharing Options</h4>
                <div class="settings-grid">
                    <div class="setting">
                        <label>
                            <input type="checkbox" id="password-protect">
                            Password protect
                        </label>
                        <div id="password-field">
                            <input type="password" id="share-password" placeholder="Set a password">
                            <button id="toggle-password"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="setting">
                        <label>Expires after:</label>
                        <select id="expiration">
                            <option value="1">1 hour</option>
                            <option value="24" selected>1 day</option>
                            <option value="168">1 week</option>
                            <option value="720">1 month</option>
                            <option value="0">Never</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Generate Button -->
            <div class="generate-section">
                <button id="generate-btn">
                    <i class="fas fa-bolt"></i> Generate Share Link
                </button>
            </div>
        </div>

        <!-- Result Section -->
        <div id="result-section">
            <div class="result-icon">
                <i class="fas fa-check"></i>
            </div>
            <h3>Your content is ready to share!</h3>
            <p>Scan the QR code or copy the link below to share your content across devices</p>
            
            <!-- QR Code -->
            <div id="qr-code" class="qr-code"></div>
            
            <!-- Share URL -->
            <div class="share-url-container">
                <input type="text" id="share-url" readonly>
                <button id="copy-url"><i class="fas fa-copy"></i> Copy</button>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <button id="new-share"><i class="fas fa-plus"></i> Create New Share</button>
                <button id="download-qr"><i class="fas fa-download"></i> Download QR</button>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tabId = button.getAttribute('data-tab');
                    
                    // Update active tab button
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    
                    // Update active tab content
                    tabContents.forEach(content => content.classList.remove('active'));
                    document.getElementById(`${tabId}-tab-content`).classList.add('active');
                });
            });
            
            // Password protection toggle
            const passwordProtect = document.getElementById('password-protect');
            const passwordField = document.getElementById('password-field');
            
            passwordProtect.addEventListener('change', () => {
                if (passwordProtect.checked) {
                    passwordField.classList.add('show');
                } else {
                    passwordField.classList.remove('show');
                }
            });
            
            // Toggle password visibility
            const togglePassword = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('share-password');
            
            togglePassword.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                togglePassword.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
            
            // Character counter for text area
            const shareText = document.getElementById('share-text');
            const charCount = document.getElementById('char-count');
            
            shareText.addEventListener('input', () => {
                charCount.textContent = shareText.value.length;
            });
            
            // File drop zone functionality
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('file-input');
            const browseBtn = document.getElementById('browse-btn');
            const filePreview = document.getElementById('file-preview');
            const fileList = document.getElementById('file-list');
            
            let files = [];
            
            // Handle click on drop zone
            dropZone.addEventListener('click', () => {
                fileInput.click();
            });
            
            browseBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                fileInput.click();
            });
            
            // Handle file selection
            fileInput.addEventListener('change', () => {
                files = Array.from(fileInput.files);
                updateFilePreview();
            });
            
            // Handle drag over
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            // Highlight drop zone when item is dragged over it
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                dropZone.classList.add('highlight');
            }
            
            function unhighlight() {
                dropZone.classList.remove('highlight');
            }
            
            // Handle dropped files
            dropZone.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                files = Array.from(dt.files);
                updateFilePreview();
            }
            
            // Update file preview
            function updateFilePreview() {
                if (files.length > 0) {
                    filePreview.style.display = 'block';
                    fileList.innerHTML = '';
                    
                    files.forEach((file, index) => {
                        const fileCard = document.createElement('div');
                        fileCard.className = 'file-card';
                        
                        const fileIcon = document.createElement('div');
                        fileIcon.className = 'file-icon';
                        
                        // Get appropriate icon based on file type
                        let iconClass = 'fa-file';
                        if (file.type.startsWith('image/')) {
                            iconClass = 'fa-file-image';
                        } else if (file.type.startsWith('video/')) {
                            iconClass = 'fa-file-video';
                        } else if (file.type.startsWith('audio/')) {
                            iconClass = 'fa-file-audio';
                        } else if (file.type.includes('pdf')) {
                            iconClass = 'fa-file-pdf';
                        } else if (file.type.includes('zip') || file.type.includes('compressed')) {
                            iconClass = 'fa-file-archive';
                        } else if (file.type.includes('word')) {
                            iconClass = 'fa-file-word';
                        } else if (file.type.includes('excel')) {
                            iconClass = 'fa-file-excel';
                        } else if (file.type.includes('powerpoint')) {
                            iconClass = 'fa-file-powerpoint';
                        } else if (file.type.includes('text')) {
                            iconClass = 'fa-file-alt';
                        }
                        
                        fileIcon.innerHTML = `<i class="fas ${iconClass}"></i>`;
                        
                        const fileInfo = document.createElement('div');
                        fileInfo.className = 'file-info';
                        
                        const fileName = document.createElement('div');
                        fileName.className = 'file-name';
                        fileName.textContent = file.name;
                        
                        const fileSize = document.createElement('div');
                        fileSize.className = 'file-size';
                        fileSize.textContent = formatFileSize(file.size);
                        
                        fileInfo.appendChild(fileName);
                        fileInfo.appendChild(fileSize);
                        
                        const fileRemove = document.createElement('button');
                        fileRemove.className = 'file-remove';
                        fileRemove.innerHTML = '<i class="fas fa-times"></i>';
                        fileRemove.addEventListener('click', (e) => {
                            e.stopPropagation();
                            files.splice(index, 1);
                            updateFilePreview();
                        });
                        
                        fileCard.appendChild(fileIcon);
                        fileCard.appendChild(fileInfo);
                        fileCard.appendChild(fileRemove);
                        
                        fileList.appendChild(fileCard);
                    });
                } else {
                    filePreview.style.display = 'none';
                }
            }
            
            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1) + ' ' + sizes[i]);
            }
            
            // Generate share link
            const generateBtn = document.getElementById('generate-btn');
            const resultSection = document.getElementById('result-section');
            const shareUrl = document.getElementById('share-url');
            const copyUrlBtn = document.getElementById('copy-url');
            const newShareBtn = document.getElementById('new-share');
            const downloadQrBtn = document.getElementById('download-qr');
            const toast = document.getElementById('toast');
            
            generateBtn.addEventListener('click', async () => {
            // Validate input
            if (document.getElementById('file-tab-content').classList.contains('active')) {
                if (files.length === 0) {
                    showToast('Please select at least one file', 'error');
                    return;
                }
            } else {
                if (shareText.value.trim() === '') {
                    showToast('Please enter some text to share', 'error');
                    return;
                }
            }
            
            // Show loading state
            generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            generateBtn.classList.add('loading');
            
            try {
                // First create the share
                const shareData = {
                    type: document.getElementById('file-tab-content').classList.contains('active') ? 'file' : 'text',
                    text: shareText.value,
                    password: passwordProtect.checked ? passwordInput.value : null,
                    expires: document.getElementById('expiration').value
                };
                
                const shareResponse = await fetch('/api/share', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },

                    body: JSON.stringify(shareData)
                });
                
                const shareResult = await shareResponse.json();
                
                if (shareResponse.ok) {
                    // If files were selected, upload them
                    if (files.length > 0) {
                        const formData = new FormData();
                        files.forEach(file => formData.append('files[]', file));
                        
                        const uploadResponse = await fetch(`/api/share/${shareResult.share_id}/files`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: formData
                        });
                        
                        if (!uploadResponse.ok) {
                            throw new Error('File upload failed');
                        }
                    }
                    
                    // Update UI with the share URL
                    shareUrl.value = shareResult.url;
                    resultSection.style.display = 'block';
                    resultSection.scrollIntoView({ behavior: 'smooth' });
                    
                    // Generate QR code (you can use a library like QRCode.js)
                    generateQRCode(shareResult.url);
                    
                } else {
                    throw new Error(shareResult.message || 'Failed to create share');
                }
            } catch (error) {
                showToast(error.message, 'error');
            } finally {
                generateBtn.innerHTML = '<i class="fas fa-bolt"></i> Generate Share Link';
                generateBtn.classList.remove('loading');
            }
        });
            
            // Copy URL to clipboard
            copyUrlBtn.addEventListener('click', () => {
                shareUrl.select();
                document.execCommand('copy');
                showToast('Link copied to clipboard!');
            });
            
            // Create new share
            newShareBtn.addEventListener('click', () => {
                // Reset form
                files = [];
                fileInput.value = '';
                updateFilePreview();
                shareText.value = '';
                document.getElementById('char-count').textContent = '0';
                passwordProtect.checked = false;
                passwordField.classList.remove('show');
                document.getElementById('expiration').value = '24';
                
                // Show file tab by default
                document.querySelector('.tab-button.active').classList.remove('active');
                document.querySelector('.tab-content.active').classList.remove('active');
                document.getElementById('file-tab').classList.add('active');
                document.getElementById('file-tab-content').classList.add('active');
                
                // Hide result section
                resultSection.style.display = 'none';
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            
            // Download QR code (placeholder functionality)
            downloadQrBtn.addEventListener('click', () => {
                showToast('QR code downloaded (simulated)');
            });
            
            // Show toast notification
            function showToast(message, type = 'success') {
                toast.innerHTML = type === 'success' 
                    ? `<i class="fas fa-check-circle"></i> ${message}`
                    : `<i class="fas fa-exclamation-circle"></i> ${message}`;
                
                toast.classList.add('show');
                
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 3000);
            }
        });

        function generateQRCode(text) {
    const qrContainer = document.getElementById('qr-code');
    qrContainer.innerHTML = ''; // Clear previous QR or icon

    QRCode.toCanvas(qrContainer, text, {
        width: 200,
        margin: 2,
        color: {
            dark: '#000',
            light: '#fff'
        }
    }, function (error) {
        if (error) {
            console.error('QR code generation failed', error);
            qrContainer.innerHTML = '<p>Failed to generate QR code</p>';
        }
    });
}
    </script>
@endsection