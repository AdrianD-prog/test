<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!DOCTYPE html>
    <title>404 - Strona nie znaleziona | Amazonix</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #0066c0 0%, #004b8f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .error-code {
            font-size: 180px;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.2);
            line-height: 1;
            margin-bottom: 20px;
            text-shadow: 4px 4px 20px rgba(0, 0, 0, 0.1);
            animation: fadeInDown 0.6s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            font-size: 36px;
            color: white;
            margin-bottom: 16px;
            font-weight: 700;
            animation: fadeInUp 0.6s ease-out 0.1s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 32px;
            line-height: 1.6;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        .button-group {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .btn-primary {
            background: white;
            color: #667eea;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            background: #f8f9fa;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .illustration {
            margin-bottom: 30px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .search-box {
            margin-top: 48px;
            padding-top: 32px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            animation: fadeInUp 0.6s ease-out 0.4s both;
        }

        .search-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            margin-bottom: 12px;
            display: block;
        }

        .search-input-wrapper {
            display: flex;
            gap: 10px;
            max-width: 400px;
            margin: 0 auto;
        }

        .search-input {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 50px;
            font-size: 14px;
            background: rgba(255, 255, 255, 0.95);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            background: white;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
        }

        .search-btn {
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .error-code {
                font-size: 120px;
            }
            
            h1 {
                font-size: 28px;
            }
            
            .message {
                font-size: 16px;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 14px;
            }
            
            .button-group {
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="illustration">
            <svg width="120" height="120" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="rgba(255,255,255,0.8)"/>
                <circle cx="12" cy="12" r="11" stroke="rgba(255,255,255,0.3)" stroke-width="2" fill="none"/>
            </svg>
        </div>
        
        <div class="error-code">404</div>
        
        <h1>Ups! Strona nie została znaleziona</h1>
        
        <div class="message">
            Przepraszamy, ale strona, której szukasz, nie istnieje.<br>
            Mogła zostać usunięta lub zmienić swój adres.
        </div>
        
        <div class="button-group">
            <a href="/amazonix" class="btn btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" fill="currentColor"/>
                </svg>
                Strona główna
            </a>
            
            <button onclick="history.back()" class="btn btn-secondary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="currentColor"/>
                </svg>
                Wróć
            </button>
        </div>
        
    </div>
</body>
</html>