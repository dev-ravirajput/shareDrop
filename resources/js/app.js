
document.addEventListener('DOMContentLoaded', function() {
    
    // Tab switching with smooth transitions
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');
            
            // Animate tab switch
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.style.transform = 'translateY(0)';
            });
            button.classList.add('active');
            button.style.transform = 'translateY(-2px)';
            
            // Animate content switch
            tabContents.forEach(content => {
                if (content.classList.contains('active')) {
                    content.style.opacity = 0;
                    content.style.transform = 'translateY(10px)';
                    setTimeout(() => {
                        content.classList.remove('active');
                    }, 200);
                }
                
                if (content.id === `${tabId}-tab-content`) {
                    setTimeout(() => {
                        content.classList.add('active');
                        content.style.opacity = 1;
                        content.style.transform = 'translateY(0)';
                    }, 200);
                }
            });
        });
    });

    // Initialize tab content transitions
    tabContents.forEach(content => {
        content.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
    });

    // File drop zone with enhanced interactions
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const browseBtn = document.getElementById('browse-btn');
    const filePreview = document.getElementById('file-preview');
    const fileList = document.getElementById('file-list');

    // Click handlers
    dropZone.addEventListener('click', () => fileInput.click());
    browseBtn.addEventListener('click', () => fileInput.click());

    // File selection handler
    fileInput.addEventListener('change', handleFiles);

    // Drag and drop with enhanced visual feedback
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.add('border-indigo-500', 'bg-indigo-50/50');
            dropZone.style.transform = 'scale(1.02)';
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50/50');
            dropZone.style.transform = 'scale(1)';
        });
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles({ target: { files } });
    });

    // Character counter for text area
    const shareText = document.getElementById('share-text');
    const charCount = document.getElementById('char-count');
    
    shareText.addEventListener('input', () => {
        const count = shareText.value.length;
        charCount.textContent = count;
        
        if (count > 4500) {
            charCount.classList.add('text-amber-500');
            charCount.classList.remove('text-gray-400');
        } else {
            charCount.classList.remove('text-amber-500');
            charCount.classList.add('text-gray-400');
        }
    });

    // Password protection toggle
    const passwordProtect = document.getElementById('password-protect');
    const passwordField = document.getElementById('password-field');
    const togglePassword = document.getElementById('toggle-password');
    const sharePassword = document.getElementById('share-password');
    
    passwordProtect.addEventListener('change', () => {
        if (passwordProtect.checked) {
            passwordField.style.display = 'block';
            setTimeout(() => {
                passwordField.classList.remove('hidden');
                passwordField.style.opacity = 1;
            }, 10);
        } else {
            passwordField.style.opacity = 0;
            setTimeout(() => {
                passwordField.classList.add('hidden');
            }, 200);
        }
    });

    // Toggle password visibility
    togglePassword.addEventListener('click', () => {
        const type = sharePassword.getAttribute('type') === 'password' ? 'text' : 'password';
        sharePassword.setAttribute('type', type);
        togglePassword.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    // Generate share link with loading animation
    const generateBtn = document.getElementById('generate-btn');
    const resultSection = document.getElementById('result-section');
    const shareUrl = document.getElementById('share-url');
    const qrCodeContainer = document.getElementById('qr-code');
    
    generateBtn.addEventListener('click', async () => {
        // Validate input
        const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
        if (activeTab === 'file' && !fileInput.files.length) {
            showToast('Please select at least one file', 'error');
            return;
        }
        if (activeTab === 'text' && !shareText.value.trim()) {
            showToast('Please enter some text to share', 'error');
            return;
        }
        if (passwordProtect.checked && !sharePassword.value.trim()) {
            showToast('Please set a password', 'error');
            return;
        }

        // Show loading state
        generateBtn.disabled = true;
        generateBtn.innerHTML = `
            <span class="relative z-10 flex items-center justify-center">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                Generating...
            </span>
        `;

        // Simulate API call (replace with actual fetch to your Laravel backend)
        try {
            const response = await simulateApiCall();
            
            // Display result
            shareUrl.value = response.url;
            generateQRCode(response.url);
            
            // Animate result section appearance
            resultSection.style.opacity = 0;
            resultSection.style.transform = 'translateY(20px)';
            resultSection.classList.remove('hidden');
            
            setTimeout(() => {
                resultSection.style.opacity = 1;
                resultSection.style.transform = 'translateY(0)';
            }, 10);
            
            // Scroll to result smoothly
            resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
        } catch (error) {
            showToast('Error generating share link. Please try again.', 'error');
        } finally {
            // Reset generate button
            generateBtn.disabled = false;
            generateBtn.innerHTML = `
                <span class="relative z-10 flex items-center justify-center">
                    <i class="fas fa-bolt mr-2"></i>
                    Generate Share Link
                </span>
            `;
        }
    });

    // Simulate API call (replace with actual fetch)
    function simulateApiCall() {
        return new Promise((resolve) => {
            setTimeout(() => {
                const shareId = Math.random().toString(36).substring(2, 10);
                resolve({
                    success: true,
                    url: `${window.location.origin}/share/${shareId}`,
                    qrCode: '<svg>...</svg>' // In real app, generate actual QR code
                });
            }, 1500);
        });
    }

    // Generate QR code (placeholder - use a library in real implementation)
    function generateQRCode(url) {
        qrCodeContainer.innerHTML = `
            <div class="text-center">
                <div class="inline-block p-2 bg-white rounded">
                    <div class="grid grid-cols-5 gap-1">
                        ${Array(25).fill().map((_, i) => 
                            `<div class="w-6 h-6 ${i % 2 === 0 ? 'bg-black' : 'bg-white'} rounded-sm"></div>`
                        ).join('')}
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-2">Scan to access content</p>
            </div>
        `;
    }

    // Copy URL to clipboard
    const copyUrlBtn = document.getElementById('copy-url');
    copyUrlBtn.addEventListener('click', () => {
        shareUrl.select();
        document.execCommand('copy');
        showToast('Link copied to clipboard!', 'success');
    });

    // Create new share
    const newShareBtn = document.getElementById('new-share');
    newShareBtn.addEventListener('click', () => {
        // Animate out result section
        resultSection.style.opacity = 0;
        resultSection.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            resultSection.classList.add('hidden');
            // Reset form
            fileInput.value = '';
            filePreview.classList.add('hidden');
            fileList.innerHTML = '';
            shareText.value = '';
            charCount.textContent = '0';
            passwordProtect.checked = false;
            passwordField.classList.add('hidden');
            sharePassword.value = '';
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }, 200);
    });

    // Download QR code
    const downloadQrBtn = document.getElementById('download-qr');
    downloadQrBtn.addEventListener('click', () => {
        showToast('QR code downloaded!', 'success');
        // In real app, implement actual download
    });

    // Share via email
    const shareViaEmail = document.getElementById('share-via-email');
    shareViaEmail.addEventListener('click', () => {
        // In real app, implement email sharing
        showToast('Email sharing coming soon!', 'info');
    });

    // Show toast notifications
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-6 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded-lg shadow-lg text-white font-medium ${
            type === 'error' ? 'bg-red-500' : 
            type === 'success' ? 'bg-green-500' : 'bg-indigo-500'
        }`;
        toast.textContent = message;
        toast.style.opacity = 0;
        toast.style.transform = 'translateY(20px)';
        toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = 1;
            toast.style.transform = 'translateY(0)';
        }, 10);
        
        setTimeout(() => {
            toast.style.opacity = 0;
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }

    // Handle file selection and preview
    function handleFiles(e) {
        const files = e.target.files;
        if (files.length === 0) return;

        // Clear previous files
        fileList.innerHTML = '';

        // Display selected files with icons based on file type
        Array.from(files).forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-card relative p-4 bg-white/70 border border-gray-200/70 rounded-lg hover:shadow-md transition-all duration-200';
            
            // Determine file type icon
            const fileType = getFileType(file.type || file.name.split('.').pop());
            const fileIcon = {
                image: 'fa-image',
                audio: 'fa-music',
                video: 'fa-video',
                pdf: 'fa-file-pdf',
                word: 'fa-file-word',
                excel: 'fa-file-excel',
                powerpoint: 'fa-file-powerpoint',
                archive: 'fa-file-archive',
                code: 'fa-file-code',
                text: 'fa-file-alt',
                default: 'fa-file'
            }[fileType];
            
            const fileColor = {
                image: 'text-blue-500',
                audio: 'text-purple-500',
                video: 'text-red-500',
                pdf: 'text-red-400',
                word: 'text-blue-400',
                excel: 'text-green-500',
                powerpoint: 'text-orange-500',
                archive: 'text-yellow-500',
                code: 'text-gray-500',
                text: 'text-gray-400',
                default: 'text-indigo-400'
            }[fileType];
            
            fileItem.innerHTML = `
                <div class="flex items-start">
                    <i class="fas ${fileIcon} ${fileColor} text-2xl mr-4 mt-1"></i>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-800 truncate">${file.name}</div>
                        <div class="text-xs text-gray-500">${formatFileSize(file.size)} • ${fileType.toUpperCase()}</div>
                    </div>
                </div>
                <div class="file-action absolute top-2 right-2 opacity-0 transform translate-y-1 transition-all duration-200">
                    <button class="text-red-400 hover:text-red-600 p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mt-3 w-full bg-gray-200 rounded-full h-1.5">
                    <div class="bg-indigo-500 h-1.5 rounded-full upload-progress" style="width: 0%"></div>
                </div>
            `;
            
            fileList.appendChild(fileItem);
        });

        // Show file preview section with animation
        filePreview.style.display = 'block';
        setTimeout(() => {
            filePreview.classList.remove('hidden');
            filePreview.style.opacity = 1;
        }, 10);
    }

    // Helper function to determine file type
    function getFileType(typeOrExt) {
        if (!typeOrExt) return 'default';
        
        const type = typeOrExt.toLowerCase();
        
        if (type.startsWith('image/')) return 'image';
        if (type.startsWith('audio/')) return 'audio';
        if (type.startsWith('video/')) return 'video';
        if (type.includes('pdf')) return 'pdf';
        if (type.includes('word') || type.includes('doc')) return 'word';
        if (type.includes('excel') || type.includes('xls')) return 'excel';
        if (type.includes('powerpoint') || type.includes('ppt')) return 'powerpoint';
        if (type.includes('zip') || type.includes('rar') || type.includes('tar') || type.includes('7z')) return 'archive';
        if (type.includes('text') || type.includes('txt') || type === 'md') return 'text';
        if (type.includes('javascript') || type.includes('json') || type.includes('html') || type.includes('css') || type.includes('php')) return 'code';
        
        return 'default';
    }

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});