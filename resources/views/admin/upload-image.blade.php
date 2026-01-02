<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>העלאת תמונה - {{ $trainer->full_name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/site/style.css">
    <style>
        .upload-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .upload-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .upload-header h1 {
            color: var(--primary, #007bff);
            margin-bottom: 0.5rem;
        }
        .trainer-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .trainer-info strong {
            color: var(--text-main, #333);
        }
        .upload-area {
            border: 3px dashed #ddd;
            border-radius: 12px;
            padding: 3rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fafafa;
        }
        .upload-area:hover {
            border-color: var(--primary, #007bff);
            background: #f0f7ff;
        }
        .upload-area.dragover {
            border-color: var(--primary, #007bff);
            background: #e3f2fd;
            transform: scale(1.02);
        }
        .upload-icon {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 1rem;
        }
        .upload-area.dragover .upload-icon {
            color: var(--primary, #007bff);
        }
        .upload-text {
            color: var(--text-main, #333);
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .upload-hint {
            color: var(--text-muted, #666);
            font-size: 0.9rem;
        }
        #imageInput {
            display: none;
        }
        .preview-container {
            margin-top: 2rem;
            display: none;
        }
        .preview-container.active {
            display: block;
        }
        .preview-image {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin: 1rem 0;
        }
        .upload-btn {
            background: var(--primary, #007bff);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }
        .upload-btn:hover {
            background: var(--primary-dark, #0056b3);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }
        .upload-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: var(--primary, #007bff);
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .error-message {
            background: #fee;
            color: #c33;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        .success-message {
            background: #efe;
            color: #3c3;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="upload-container">
        <a href="{{ route('admin.trainers.index') }}" class="back-link">
            <i class="fas fa-arrow-right"></i> חזרה לרשימת מאמנים
        </a>

        <div class="upload-header">
            <h1><i class="fas fa-image"></i> העלאת תמונה</h1>
            <div class="trainer-info">
                <strong>מאמן:</strong> {{ $trainer->full_name }} (ID: {{ $trainer->id }})<br>
                @if($trainer->profile_image_path)
                    <strong>נתיב נוכחי:</strong> {{ $trainer->profile_image_path }}<br>
                    <small style="color: #c33;">⚠️ התמונה חסרה מהשרת</small>
                @else
                    <strong>סטטוס:</strong> אין תמונה מוגדרת
                @endif
            </div>
        </div>

        @if(session('error'))
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="success-message">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.trainers.upload-image.store', $trainer) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf

            <div class="upload-area" id="uploadArea">
                <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="upload-text">גרור תמונה לכאן או לחץ לבחירה</div>
                <div class="upload-hint">JPEG, PNG, GIF, WebP עד 10MB</div>
                <input type="file" id="imageInput" name="image" accept="image/*" required>
            </div>

            <div class="preview-container" id="previewContainer">
                <h3>תצוגה מקדימה:</h3>
                <img id="previewImage" class="preview-image" src="" alt="Preview">
                <button type="submit" class="upload-btn" id="uploadBtn">
                    <i class="fas fa-upload"></i> העלה תמונה
                </button>
            </div>
        </form>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');
        const uploadBtn = document.getElementById('uploadBtn');

        // Click to select file
        uploadArea.addEventListener('click', () => {
            imageInput.click();
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        });

        // File input change
        imageInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFile(e.target.files[0]);
            }
        });

        function handleFile(file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('אנא בחר קובץ תמונה בלבד');
                return;
            }

            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('הקובץ גדול מדי. מקסימום 10MB');
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                previewContainer.classList.add('active');
                uploadBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }

        // Form submit
        document.getElementById('uploadForm').addEventListener('submit', function() {
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> מעלה...';
        });
    </script>
</body>
</html>

