#!/bin/bash

# WhatsApp Engine - Optimized Startup Script
# Script untuk menjalankan WhatsApp Engine dengan berbagai konfigurasi optimasi

# Colors untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Default values
NODE_ENV=${NODE_ENV:-"development"}
PORT=${PORT:-3000}
API_KEY=${API_KEY:-"your_api_key"}
LOG_LEVEL=${LOG_LEVEL:-"info"}

# Function untuk print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${BLUE}================================${NC}"
    echo -e "${BLUE}  WhatsApp Engine - Optimized${NC}"
    echo -e "${BLUE}================================${NC}"
}

# Function untuk check dependencies
check_dependencies() {
    print_status "Checking dependencies..."
    
    if ! command -v node &> /dev/null; then
        print_error "Node.js is not installed"
        exit 1
    fi
    
    if ! command -v npm &> /dev/null; then
        print_error "npm is not installed"
        exit 1
    fi
    
    # Check Node.js version
    NODE_VERSION=$(node -v | cut -d'v' -f2 | cut -d'.' -f1)
    if [ "$NODE_VERSION" -lt 16 ]; then
        print_error "Node.js version 16 or higher is required"
        exit 1
    fi
    
    print_status "Dependencies check passed"
}

# Function untuk install dependencies
install_dependencies() {
    print_status "Installing dependencies..."
    
    if [ -f "package-optimized.json" ]; then
        npm install
        print_status "Dependencies installed successfully"
    else
        print_error "package-optimized.json not found"
        exit 1
    fi
}

# Function untuk create uploads directory
create_uploads_dir() {
    if [ ! -d "uploads" ]; then
        print_status "Creating uploads directory..."
        mkdir -p uploads
    fi
}

# Function untuk check environment
check_environment() {
    print_status "Checking environment..."
    
    if [ -z "$API_KEY" ]; then
        print_warning "API_KEY not set, using default"
    fi
    
    print_status "Environment: $NODE_ENV"
    print_status "Port: $PORT"
    print_status "Log Level: $LOG_LEVEL"
}

# Function untuk start dengan memory optimization
start_memory_optimized() {
    print_status "Starting with memory optimization..."
    NODE_OPTIONS="--max-old-space-size=4096 --expose-gc" npm run start:memory
}

# Function untuk start dengan garbage collection
start_gc_optimized() {
    print_status "Starting with garbage collection enabled..."
    NODE_OPTIONS="--expose-gc" npm run start:gc
}

# Function untuk start production
start_production() {
    print_status "Starting in production mode..."
    NODE_ENV=production npm run start:prod
}

# Function untuk start development
start_development() {
    print_status "Starting in development mode..."
    npm run dev
}

# Function untuk start default
start_default() {
    print_status "Starting with default configuration..."
    npm start
}

# Function untuk monitor performance
monitor_performance() {
    print_status "Starting performance monitoring..."
    
    # Monitor memory usage
    while true; do
        if curl -s http://localhost:$PORT/health > /dev/null; then
            MEMORY=$(curl -s http://localhost:$PORT/performance | jq -r '.data.memoryUsage.heapUsed' 2>/dev/null)
            if [ "$MEMORY" != "null" ] && [ "$MEMORY" != "" ]; then
                print_status "Memory Usage: $MEMORY"
            fi
        fi
        sleep 30
    done
}

# Function untuk cleanup
cleanup() {
    print_status "Cleaning up..."
    
    # Kill background processes
    pkill -f "node.*server-optimized.js" 2>/dev/null
    
    # Clear temporary files
    find uploads -name "temp_*" -delete 2>/dev/null
    
    print_status "Cleanup completed"
}

# Function untuk show help
show_help() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  -h, --help              Show this help message"
    echo "  -m, --memory            Start with memory optimization"
    echo "  -g, --gc                Start with garbage collection"
    echo "  -p, --production        Start in production mode"
    echo "  -d, --development       Start in development mode"
    echo "  -c, --cleanup           Clean up before starting"
    echo "  -i, --install           Install dependencies before starting"
    echo "  -o, --monitor           Start performance monitoring"
    echo ""
    echo "Environment Variables:"
    echo "  NODE_ENV                Node environment (development|production|testing)"
    echo "  PORT                    Server port (default: 3000)"
    echo "  API_KEY                 API key for authentication"
    echo "  LOG_LEVEL               Log level (debug|info|warn|error)"
    echo ""
    echo "Examples:"
    echo "  $0 --memory             Start with memory optimization"
    echo "  $0 --production         Start in production mode"
    echo "  $0 --install --memory   Install deps and start with memory optimization"
}

# Main script
main() {
    print_header
    
    # Parse command line arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            -h|--help)
                show_help
                exit 0
                ;;
            -m|--memory)
                START_MODE="memory"
                shift
                ;;
            -g|--gc)
                START_MODE="gc"
                shift
                ;;
            -p|--production)
                START_MODE="production"
                NODE_ENV="production"
                shift
                ;;
            -d|--development)
                START_MODE="development"
                NODE_ENV="development"
                shift
                ;;
            -c|--cleanup)
                DO_CLEANUP=true
                shift
                ;;
            -i|--install)
                DO_INSTALL=true
                shift
                ;;
            -o|--monitor)
                DO_MONITOR=true
                shift
                ;;
            *)
                print_error "Unknown option: $1"
                show_help
                exit 1
                ;;
        esac
    done
    
    # Set default start mode if not specified
    if [ -z "$START_MODE" ]; then
        START_MODE="default"
    fi
    
    # Check dependencies
    check_dependencies
    
    # Install dependencies if requested
    if [ "$DO_INSTALL" = true ]; then
        install_dependencies
    fi
    
    # Create uploads directory
    create_uploads_dir
    
    # Check environment
    check_environment
    
    # Cleanup if requested
    if [ "$DO_CLEANUP" = true ]; then
        cleanup
    fi
    
    # Start performance monitoring in background if requested
    if [ "$DO_MONITOR" = true ]; then
        monitor_performance &
        MONITOR_PID=$!
    fi
    
    # Start the server based on mode
    case $START_MODE in
        memory)
            start_memory_optimized
            ;;
        gc)
            start_gc_optimized
            ;;
        production)
            start_production
            ;;
        development)
            start_development
            ;;
        default)
            start_default
            ;;
        *)
            print_error "Unknown start mode: $START_MODE"
            exit 1
            ;;
    esac
}

# Trap signals for cleanup
trap cleanup EXIT
trap 'print_status "Received SIGINT, shutting down..."; exit 0' INT
trap 'print_status "Received SIGTERM, shutting down..."; exit 0' TERM

# Run main function
main "$@" 