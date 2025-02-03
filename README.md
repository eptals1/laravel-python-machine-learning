# Laravel with Python Machine Learning Integration

This project demonstrates how to integrate Laravel (PHP) with Python machine learning capabilities.

## Project Structure

```
laravel-python-ml/
├── laravel/              # Laravel application
├── python/              # Python ML service
│   ├── app.py           # Flask API for ML predictions
│   ├── model.py         # ML model implementation
│   └── requirements.txt # Python dependencies
└── README.md
```

## Setup Instructions

1. Set up Laravel project:
```bash
composer create-project laravel/laravel laravel
cd laravel
php artisan serve
```

2. Set up Python environment:
```bash
cd python
python -m venv venv
source venv/bin/activate  # On Windows: .\venv\Scripts\activate
pip install -r requirements.txt
python app.py
```

## How it works

1. Laravel handles the web interface and main application logic
2. Python Flask API runs separately to serve ML predictions
3. Laravel communicates with the Python service via HTTP requests
4. ML predictions are returned to Laravel and displayed to the user

## Requirements

- PHP >= 8.1
- Composer
- Python >= 3.8
- pip
"# laravel-python-flask-ml" 
"# laravel-python-machine-learning" 
