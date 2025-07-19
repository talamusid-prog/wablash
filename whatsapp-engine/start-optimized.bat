@echo off
REM WhatsApp Engine - Optimized Startup Script for Windows
REM Script untuk menjalankan WhatsApp Engine dengan berbagai konfigurasi optimasi

setlocal enabledelayedexpansion

REM Default values
if "%NODE_ENV%"=="" set NODE_ENV=development
if "%PORT%"=="" set PORT=3000
if "%API_KEY%"=="" set API_KEY=your_api_key
if "%LOG_LEVEL%"=="" set LOG_LEVEL=info

REM Colors untuk output (Windows 10+)
set "RED=[91m"
set "GREEN=[92m"
set "YELLOW=[93m"
set "BLUE=[94m"
set "NC=[0m"

REM Function untuk print colored output
:print_status
echo %GREEN%[INFO]%NC% %~1
goto :eof

:print_warning
echo %YELLOW%[WARNING]%NC% %~1
goto :eof

:print_error
echo %RED%[ERROR]%NC% %~1
goto :eof

:print_header
echo %BLUE%================================%NC%
echo %BLUE%  WhatsApp Engine - Optimized%NC%
echo %BLUE%================================%NC%
goto :eof

REM Function untuk check dependencies
:check_dependencies
call :print_status "Checking dependencies..."

node --version >nul 2>&1
if errorlevel 1 (
    call :print_error "Node.js is not installed"
    exit /b 1
)

npm --version >nul 2>&1
if errorlevel 1 (
    call :print_error "npm is not installed"
    exit /b 1
)

REM Check Node.js version
for /f "tokens=1,2,3 delims=." %%a in ('node -v') do (
    set NODE_VERSION=%%a
    set NODE_VERSION=!NODE_VERSION:~1!
)

if !NODE_VERSION! LSS 16 (
    call :print_error "Node.js version 16 or higher is required"
    exit /b 1
)

call :print_status "Dependencies check passed"
goto :eof

REM Function untuk install dependencies
:install_dependencies
call :print_status "Installing dependencies..."

if exist "package-optimized.json" (
    npm install
    call :print_status "Dependencies installed successfully"
) else (
    call :print_error "package-optimized.json not found"
    exit /b 1
)
goto :eof

REM Function untuk create uploads directory
:create_uploads_dir
if not exist "uploads" (
    call :print_status "Creating uploads directory..."
    mkdir uploads
)
goto :eof

REM Function untuk check environment
:check_environment
call :print_status "Checking environment..."

if "%API_KEY%"=="your_api_key" (
    call :print_warning "API_KEY not set, using default"
)

call :print_status "Environment: %NODE_ENV%"
call :print_status "Port: %PORT%"
call :print_status "Log Level: %LOG_LEVEL%"
goto :eof

REM Function untuk start dengan memory optimization
:start_memory_optimized
call :print_status "Starting with memory optimization..."
set NODE_OPTIONS=--max-old-space-size=4096 --expose-gc
call npm run start:memory
goto :eof

REM Function untuk start dengan garbage collection
:start_gc_optimized
call :print_status "Starting with garbage collection enabled..."
set NODE_OPTIONS=--expose-gc
call npm run start:gc
goto :eof

REM Function untuk start production
:start_production
call :print_status "Starting in production mode..."
set NODE_ENV=production
call npm run start:prod
goto :eof

REM Function untuk start development
:start_development
call :print_status "Starting in development mode..."
call npm run dev
goto :eof

REM Function untuk start default
:start_default
call :print_status "Starting with default configuration..."
call npm start
goto :eof

REM Function untuk cleanup
:cleanup
call :print_status "Cleaning up..."

REM Kill background processes (if any)
taskkill /f /im node.exe >nul 2>&1

REM Clear temporary files
if exist "uploads\temp_*" del /q "uploads\temp_*" >nul 2>&1

call :print_status "Cleanup completed"
goto :eof

REM Function untuk show help
:show_help
echo Usage: %~nx0 [OPTIONS]
echo.
echo Options:
echo   -h, --help              Show this help message
echo   -m, --memory            Start with memory optimization
echo   -g, --gc                Start with garbage collection
echo   -p, --production        Start in production mode
echo   -d, --development       Start in development mode
echo   -c, --cleanup           Clean up before starting
echo   -i, --install           Install dependencies before starting
echo.
echo Environment Variables:
echo   NODE_ENV                Node environment (development^|production^|testing^)
echo   PORT                    Server port (default: 3000^)
echo   API_KEY                 API key for authentication
echo   LOG_LEVEL               Log level (debug^|info^|warn^|error^)
echo.
echo Examples:
echo   %~nx0 --memory             Start with memory optimization
echo   %~nx0 --production         Start in production mode
echo   %~nx0 --install --memory   Install deps and start with memory optimization
goto :eof

REM Main script
:main
call :print_header

REM Parse command line arguments
:parse_args
if "%~1"=="" goto :end_parse
if "%~1"=="-h" goto :show_help
if "%~1"=="--help" goto :show_help
if "%~1"=="-m" set START_MODE=memory
if "%~1"=="--memory" set START_MODE=memory
if "%~1"=="-g" set START_MODE=gc
if "%~1"=="--gc" set START_MODE=gc
if "%~1"=="-p" set START_MODE=production
if "%~1"=="--production" set START_MODE=production
if "%~1"=="-d" set START_MODE=development
if "%~1"=="--development" set START_MODE=development
if "%~1"=="-c" set DO_CLEANUP=true
if "%~1"=="--cleanup" set DO_CLEANUP=true
if "%~1"=="-i" set DO_INSTALL=true
if "%~1"=="--install" set DO_INSTALL=true
shift
goto :parse_args
:end_parse

REM Set default start mode if not specified
if "%START_MODE%"=="" set START_MODE=default

REM Check dependencies
call :check_dependencies
if errorlevel 1 exit /b 1

REM Install dependencies if requested
if "%DO_INSTALL%"=="true" (
    call :install_dependencies
    if errorlevel 1 exit /b 1
)

REM Create uploads directory
call :create_uploads_dir

REM Check environment
call :check_environment

REM Cleanup if requested
if "%DO_CLEANUP%"=="true" (
    call :cleanup
)

REM Start the server based on mode
if "%START_MODE%"=="memory" (
    call :start_memory_optimized
) else if "%START_MODE%"=="gc" (
    call :start_gc_optimized
) else if "%START_MODE%"=="production" (
    call :start_production
) else if "%START_MODE%"=="development" (
    call :start_development
) else if "%START_MODE%"=="default" (
    call :start_default
) else (
    call :print_error "Unknown start mode: %START_MODE%"
    exit /b 1
)

goto :eof

REM Run main function
call :main %* 