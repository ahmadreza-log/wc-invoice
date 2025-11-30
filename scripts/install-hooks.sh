#!/bin/bash
#
# Install git hooks
# Run: bash scripts/install-hooks.sh
#

HOOKS_DIR=".githooks"
GIT_HOOKS_DIR=".git/hooks"

if [ ! -d "$GIT_HOOKS_DIR" ]; then
    echo "❌ Error: .git/hooks directory not found. Are you in a git repository?"
    exit 1
fi

if [ ! -d "$HOOKS_DIR" ]; then
    echo "❌ Error: $HOOKS_DIR directory not found"
    exit 1
fi

echo "📦 Installing git hooks..."

# Install pre-push hook
if [ -f "$HOOKS_DIR/pre-push" ]; then
    cp "$HOOKS_DIR/pre-push" "$GIT_HOOKS_DIR/pre-push"
    chmod +x "$GIT_HOOKS_DIR/pre-push"
    echo "✅ Installed pre-push hook"
else
    echo "⚠️  Warning: pre-push hook not found"
fi

echo "✅ Git hooks installed successfully!"
echo ""
echo "The pre-push hook will automatically bump the version when you push to main/master branch."

