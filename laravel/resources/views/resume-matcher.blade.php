<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automated Resume Screening with Predictive Analytics</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.css') }}" rel="stylesheet">
    <style>
        .custom-file-upload {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .custom-file-upload:hover {
            border-color: #007bff;
        }

        .resume-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .resume-item {
            padding: 10px;
            border: 1px solid #eee;
            margin-bottom: 5px;
            border-radius: 4px;
        }

        .match-percentage {
            font-size: 1.2em;
            font-weight: bold;
        }

        .skills-match {
            font-size: 0.9em;
            color: #666;
        }

        .nav-tabs .nav-link {
            color: #495057;
        }

        .nav-tabs .nav-link.active {
            font-weight: bold;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid py-2">
        <!--h2 class="text-center"><h2-->

        <div class="row">
            <!-- Left Section - Upload and Requirements -->
            <div class="col-md-6 pe-md-2">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2">
                            <i class="fas fa-file-alt me-2"></i>Upload Resumes
                        </h5>
                        <div class="custom-file-upload" id="resumeUpload">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-1"></i>
                            <p class="mb-0">Drag & Drop Resumes Here</p>
                            <p class="text-muted small">or click to browse</p>
                            <input type="file" id="resumeInput" multiple accept=".pdf,.doc,.docx" class="d-none">
                        </div>
                        <div class="resume-list" id="uploadedResumes">
                            <!-- Uploaded resumes will appear here -->
                        </div>

                        <!-- Job Requirements Section -->
                        <h5 class="card-title mb-2">
                            <i class="fas fa-briefcase me-1"></i>Job Requirements
                        </h5>
                        <div class="custom-file-upload" id="jobRequirementUpload">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-1"></i>
                            <p class="mb-0">Drag & Drop Job Description</p>
                            <p class="text-muted small">or click to browse (.pdf, .doc, .docx)</p>
                            <input type="file" id="jobInput" accept=".pdf,.doc,.docx" class="d-none">
                        </div>
                        <div class="resume-list" id="uploadedJob">
                            <!-- Uploaded job description will appear here -->
                        </div>

                        <div class="mt-1">
                            <button type="button" id="analyzeButton" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Analyze Matches
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Section - Results -->
            <div class="col-md-6 ps-md-2 mt-4 mt-md-0">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            <i class="fas fa-chart-bar me-2"></i>Analytics Result
                        </h4>

                        <div id="analysisProgress" class="d-none">
                            <div class="progress mb-3">
                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                    role="progressbar" style="width: 100%"></div>
                            </div>
                            <p class="text-center text-muted">Analyzing resumes...</p>
                        </div>

                        <div id="matchResults">
                            <!-- Results will be populated here -->
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                <p>Upload resumes and set job requirements to see matching results</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle drag and drop for resumes
            const resumeDropZone = document.getElementById('resumeUpload');
            const resumeInput = document.getElementById('resumeInput');
            const uploadedResumes = document.getElementById('uploadedResumes');

            // Handle drag and drop for job requirements
            const jobDropZone = document.getElementById('jobRequirementUpload');
            const jobInput = document.getElementById('jobInput');
            const uploadedJob = document.getElementById('uploadedJob');

            const analyzeButton = document.getElementById('analyzeButton');

            // Add drag and drop handlers for both zones
            [resumeDropZone, jobDropZone].forEach(dropZone => {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => highlight(dropZone), false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => unhighlight(dropZone), false);
                });
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            function highlight(element) {
                element.classList.add('border-primary');
            }

            function unhighlight(element) {
                element.classList.remove('border-primary');
            }

            // Resume upload handlers
            resumeDropZone.addEventListener('drop', handleResumeDrop, false);
            resumeDropZone.addEventListener('click', () => resumeInput.click());
            resumeInput.addEventListener('change', handleResumeFiles);

            // Job requirements upload handlers
            jobDropZone.addEventListener('drop', handleJobDrop, false);
            jobDropZone.addEventListener('click', () => jobInput.click());
            jobInput.addEventListener('change', handleJobFiles);

            function handleResumeDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleResumeFiles({
                    target: {
                        files: files
                    }
                });
            }

            function handleJobDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleJobFiles({
                    target: {
                        files: files
                    }
                });
            }

            function handleResumeFiles(e) {
                const files = [...e.target.files];
                files.forEach(file => {
                    const div = document.createElement('div');
                    div.className = 'resume-item d-flex justify-content-between align-items-center';
                    div.innerHTML = `
                        <div>
                            <i class="fas fa-file-pdf me-2"></i>
                            ${file.name}
                        </div>
                        <button class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    uploadedResumes.appendChild(div);
                });
            }

            function handleJobFiles(e) {
                const files = [...e.target.files];
                if (files.length > 0) {
                    // Only show the last uploaded file
                    uploadedJob.innerHTML = '';
                    const file = files[0];
                    const div = document.createElement('div');
                    div.className = 'resume-item d-flex justify-content-between align-items-center';
                    div.innerHTML = `
                        <div>
                            <i class="fas fa-file-pdf me-2"></i>
                            ${file.name}
                        </div>
                        <button class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    uploadedJob.appendChild(div);
                }
            }

            // Handle analyze button click
            analyzeButton.addEventListener('click', function() {
                const resumeFiles = $('#uploadedResumes .resume-item').length;
                const jobFile = $('#uploadedJob .resume-item').length;

                if (resumeFiles === 0) {
                    alert('Please upload at least one resume');
                    return;
                }

                if (jobFile === 0) {
                    alert('Please upload a job description');
                    return;
                }

                // Show progress
                $('#analysisProgress').removeClass('d-none');
                $('#matchResults').html('');

                // Simulate analysis (replace with actual API call)
                setTimeout(() => {
                    $('#analysisProgress').addClass('d-none');
                    showResults();
                }, 2000);
            });

            function showResults() {
                const results = `
                    <div class="mb-4">
                        <div class="alert alert-info">
                            <strong>Analysis Complete!</strong><br>
                            Found 3 matching candidates from 5 resumes
                        </div>
                    </div>

                    <!-- Top Match -->
                    <div class="card mb-3 border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">John Doe</h5>
                                <span class="badge bg-success">95% Match</span>
                            </div>
                            <p class="mb-2"><strong>Experience:</strong> 5 years</p>
                            <p class="mb-2"><strong>Education:</strong> Master's in Computer Science</p>
                            <p class="mb-2"><strong>Skills Match:</strong></p>
                            <div class="skills-match">
                                <span class="badge bg-primary me-1">Python</span>
                                <span class="badge bg-primary me-1">Machine Learning</span>
                                <span class="badge bg-primary me-1">Data Analysis</span>
                            </div>
                        </div>
                    </div>

                    <!-- Other Matches -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Jane Smith</h5>
                                <span class="badge bg-warning text-dark">80% Match</span>
                            </div>
                            <p class="mb-2"><strong>Experience:</strong> 3 years</p>
                            <p class="mb-2"><strong>Education:</strong> Bachelor's in Computer Science</p>
                            <p class="mb-2"><strong>Skills Match:</strong></p>
                            <div class="skills-match">
                                <span class="badge bg-primary me-1">Python</span>
                                <span class="badge bg-primary me-1">Data Analysis</span>
                            </div>
                        </div>
                    </div>
                `;

                $('#matchResults').html(results);
            }
        });
    </script>
</body>

</html>
