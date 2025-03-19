@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Upload Your Resumes</h2>
    
    {{-- Display validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <h4 class="alert-heading">There were some issues with your submission:</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    {{-- Display success message --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('resumes.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
        @csrf
        <div class="mb-3">
            <label for="resume" class="form-label">Upload Resumes (PDF/DOCX)</label>
            <div id="dropZone" class="border border-primary rounded p-4 text-center" style="cursor: pointer;">
                <p>Drag and drop your files here, or click to select files</p>
                <input type="file" class="form-control d-none" id="resume" name="resume[]" multiple>
            </div>
        </div>
        <div id="fileList" class="mt-3"></div>
        <button type="submit" class="btn btn-sm btn-primary mt-3">Upload</button> 
        <a href="{{ route('resumes.index') }}" class="btn btn-success btn-sm mt-3">Back to Resumes List</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('resume');
        const fileList = document.getElementById('fileList');
        let filesArray = []; // Array to store files

        // Handle click on drop zone
        dropZone.addEventListener('click', () => fileInput.click());

        // Handle file input change
        fileInput.addEventListener('change', () => {
            handleFiles(fileInput.files);
        });

        // Handle drag-and-drop events
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('bg-light');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('bg-light');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('bg-light');
            handleFiles(e.dataTransfer.files);
        });

        // Function to handle files
        function handleFiles(files) {
            Array.from(files).forEach((file) => {
                // Check if the file is already listed
                if (!isFileAlreadyListed(file.name)) {
                    filesArray.push(file); // Add file to the array

                    // Update the file list visually
                    const listItem = document.createElement('div');
                    listItem.textContent = `${file.name}`;
                    listItem.classList.add('border', 'p-2', 'mb-2', 'rounded', 'bg-light');
                    fileList.appendChild(listItem);
                }
            });

            // Update the file input element with the new files
            updateFileInput();
        }

        // Helper function to check if a file is already listed
        function isFileAlreadyListed(fileName) {
            return filesArray.some(file => file.name === fileName);
        }

        // Helper function to update the file input element
        function updateFileInput() {
            const dataTransfer = new DataTransfer(); // Create a new DataTransfer object
            filesArray.forEach(file => dataTransfer.items.add(file)); // Add files to the DataTransfer object
            fileInput.files = dataTransfer.files; // Assign the files to the input element
        }
    });
</script>
@endsection
