document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    const MAX_FILE_SIZE = parseInt(fileInput.dataset.maxSize) || 256 * 1024 * 1024; // Fallback to 256MB if not set

    // Handle drag and drop events
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });

    // Handle click to select files
    dropZone.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        handleFiles(fileInput.files);
    });

    function validateFile(file) {
        if (file.size > MAX_FILE_SIZE) {
            return {
                valid: false,
                error: `File size (${formatFileSize(file.size)}) exceeds maximum limit of ${formatFileSize(MAX_FILE_SIZE)}`
            };
        }
        return { valid: true };
    }

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            // Validate file size before upload
            const validation = validateFile(file);
            const fileItem = createFileListItem(file);
            fileList.appendChild(fileItem);

            if (!validation.valid) {
                showError(fileItem, validation.error);
                return;
            }

            // Start upload if validation passes
            uploadFile(file, fileItem);
        });
    }

    function createFileListItem(file) {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.innerHTML = `
            <div class="row align-items-center">
                <div class="col-6 file-name">
                    ${file.name} (${formatFileSize(file.size)})
                </div>
                <div class="col-6 file-status uploading">
                    <div class="upload-progress">
                        <div class="progress file-progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%" 
                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                    </div>
                    <div class="file-url">
                        <div class="input-group">
                            <input type="text" class="form-control" readonly>
                            <button class="btn btn-outline-primary copy-btn">Copy</button>
                        </div>
                    </div>
                    <div class="error-message"></div>
                </div>
            </div>
        `;
        return fileItem;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function uploadFile(file, fileItem) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();
        formData.append('file', file);

        const fileStatus = fileItem.querySelector('.file-status');
        const progressBar = fileItem.querySelector('.progress-bar');
        const urlInput = fileItem.querySelector('input[type="text"]');
        const copyBtn = fileItem.querySelector('.copy-btn');
        const errorDiv = fileItem.querySelector('.error-message');

        // Upload progress event
        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
                progressBar.textContent = Math.round(percentComplete) + '%';
            }
        });

        // Upload complete event
        xhr.addEventListener('load', () => {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Show URL
                        fileStatus.className = 'col-6 file-status complete';
                        urlInput.value = response.url;

                        // Add copy functionality
                        copyBtn.addEventListener('click', () => {
                            navigator.clipboard.writeText(response.url)
                                .then(() => {
                                    copyBtn.textContent = 'Copied!';
                                    setTimeout(() => {
                                        copyBtn.textContent = 'Copy';
                                    }, 2000);
                                });
                        });
                    } else {
                        showError(fileItem, response.error || 'Upload failed');
                    }
                } catch (e) {
                    showError(fileItem, 'Invalid response from server');
                }
            } else {
                showError(fileItem, 'Upload failed with status: ' + xhr.status);
            }
        });

        // Upload error event
        xhr.addEventListener('error', () => {
            showError(fileItem, 'Upload failed');
        });

        // Send the file
        xhr.open('POST', 'upload.php', true);
        xhr.send(formData);
    }

    function showError(fileItem, error) {
        const fileStatus = fileItem.querySelector('.file-status');
        const errorDiv = fileItem.querySelector('.error-message');
        fileStatus.className = 'col-6 file-status error';
        errorDiv.textContent = error;
    }
});