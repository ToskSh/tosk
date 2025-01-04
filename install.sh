#!/bin/bash
#
# This script should be run via curl:
#   bash -c "$(curl -fsSL https://raw.githubusercontent.com/ToskSh/tosk/main/install.sh)"
# or via wget:
#   bash -c "$(wget -qO- https://raw.githubusercontent.com/ToskSh/tosk/main/install.sh)"
# or via fetch:
#   bash -c "$(fetch -o - https://raw.githubusercontent.com/ToskSh/tosk/main/install.sh)"
#
# As an alternative, you can first download the install script and run it afterwards:
#   wget https://raw.githubusercontent.com/ToskSh/tosk/main/install.sh
#   bash install.sh
set -e

# Configuration
BINARY_NAME="tosk"
REPO_OWNER="ToskSh"
REPO_NAME="tosk"

# Colors for messages
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to display messages
print_message() {
    echo -e "${BLUE}==>${NC} $1"
}

print_error() {
    echo -e "${RED}Error:${NC} $1"
}

print_success() {
    echo -e "${GREEN}Success:${NC} $1"
}

# OS detection
detect_os() {
    if [ "$(uname)" == "Darwin" ]; then
        echo "darwin"
    elif [ "$(uname)" == "Linux" ]; then
        echo "linux"
    elif [ "$(expr substr $(uname -s) 1 10)" == "MINGW32_NT" ] ||
         [ "$(expr substr $(uname -s) 1 10)" == "MINGW64_NT" ]; then
        echo "windows"
    else
        print_error "Unsupported operating system"
        exit 1
    fi
}

# Determine installation directory
get_install_dir() {
    local os=$1
    case $os in
        "windows")
            if [ -d "/c/Program Files" ]; then
                echo "/c/Program Files/${BINARY_NAME}"
            else
                echo "$HOME/bin"
            fi
            ;;
        "darwin"|"linux")
            if [ -w "/usr/local/bin" ]; then
                echo "/usr/local/bin"
            else
                echo "$HOME/.local/bin"
            fi
            ;;
        *)
            echo "$HOME/bin"
            ;;
    esac
}

# Add to PATH if needed
add_to_path() {
    local install_dir=$1
    local shell_profile=""
    
    # Shell profile detection
    if [ -n "$ZSH_VERSION" ]; then
        shell_profile="$HOME/.zshrc"
    elif [ -n "$BASH_VERSION" ]; then
        shell_profile="$HOME/.bashrc"
    elif [ -f "$HOME/.profile" ]; then
        shell_profile="$HOME/.profile"
    fi

    if [ -n "$shell_profile" ]; then
        if ! echo $PATH | grep -q "$install_dir"; then
            echo "export PATH=\"\$PATH:$install_dir\"" >> "$shell_profile"
            print_message "Added $install_dir to PATH in $shell_profile"
            print_message "Restart your terminal or run 'source $shell_profile' to apply changes"
        fi
    fi
}

# Installation
install() {
    local os=$(detect_os)
    local install_dir=$(get_install_dir "$os")
    local download_url="https://raw.githubusercontent.com/ToskSh/tosk/main/tosk"
    local temp_dir=$(mktemp -d)

    print_message "Installing ${BINARY_NAME}..."
    print_message "OS: ${os}"
    print_message "Installation directory: ${install_dir}"

    # Create installation directory if needed
    if [ ! -d "${install_dir}" ]; then
        if ! mkdir -p "${install_dir}"; then
            sudo mkdir -p "${install_dir}"
        fi
    fi

    # Download
    print_message "Downloading from ${download_url}..."
    if ! curl -sL "${download_url}" -o "${temp_dir}/${BINARY_NAME}"; then
        print_error "Download failed"
        rm -rf "${temp_dir}"
        exit 1
    fi

    # Install
    print_message "Installing to ${install_dir}..."
    if ! mv "${temp_dir}/${BINARY_NAME}" "${install_dir}"; then
        sudo mv "${temp_dir}/${BINARY_NAME}" "${install_dir}"
    fi

    if ! chmod +x "${install_dir}/${BINARY_NAME}"; then
        sudo chmod +x "${install_dir}/${BINARY_NAME}"
    fi

    # Add to PATH if installed in non-standard directory
    if [[ "$install_dir" == "$HOME"* ]]; then
        add_to_path "$install_dir"
    fi

    # Cleanup
    rm -rf "${temp_dir}"

    # Verify installation
    if command -v "${BINARY_NAME}" >/dev/null 2>&1; then
        print_success "${BINARY_NAME} has been successfully installed to ${install_dir}"
        print_message "Installed version: $(${BINARY_NAME} --version 2>/dev/null || echo 'Version not available')"
    else
        print_error "Installation failed"
        exit 1
    fi
}

# Main execution
install