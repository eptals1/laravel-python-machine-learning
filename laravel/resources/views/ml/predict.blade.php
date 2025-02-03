<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ML Prediction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Machine Learning Prediction</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form id="predictionForm">
                            <div class="mb-3">
                                <label class="form-label">Feature 1:</label>
                                <input type="number" step="0.01" class="form-control" name="feature1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Feature 2:</label>
                                <input type="number" step="0.01" class="form-control" name="feature2" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Feature 3:</label>
                                <input type="number" step="0.01" class="form-control" name="feature3" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Feature 4:</label>
                                <input type="number" step="0.01" class="form-control" name="feature4" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Get Prediction</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Prediction Results</h5>
                        <div id="results">
                            <p class="text-muted">Submit the form to see predictions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#predictionForm').on('submit', function(e) {
                e.preventDefault();
                
                const features = [
                    parseFloat($('input[name="feature1"]').val()),
                    parseFloat($('input[name="feature2"]').val()),
                    parseFloat($('input[name="feature3"]').val()),
                    parseFloat($('input[name="feature4"]').val())
                ];

                $.ajax({
                    url: '/predict',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        features: features
                    }),
                    contentType: 'application/json',
                    success: function(response) {
                        const predictions = response.prediction[0];
                        $('#results').html(`
                            <div class="alert alert-success">
                                <p><strong>Class 0 Probability:</strong> ${(predictions[0] * 100).toFixed(2)}%</p>
                                <p><strong>Class 1 Probability:</strong> ${(predictions[1] * 100).toFixed(2)}%</p>
                            </div>
                        `);
                    },
                    error: function(xhr) {
                        $('#results').html(`
                            <div class="alert alert-danger">
                                Error: ${xhr.responseJSON?.error || 'Failed to get prediction'}
                            </div>
                        `);
                    }
                });
            });
        });
    </script>
</body>
</html>
