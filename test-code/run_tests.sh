#!/bin/bash
# test-code/run_tests.sh
# 商品管理アプリのテストを実行するスクリプト

echo "🧪 商品管理アプリ テスト実行スクリプト"
echo "=================================="

# 現在のディレクトリを保存
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# テスト環境の準備
echo "📋 テスト環境の準備中..."

# Dockerコンテナが起動しているかチェック
if ! docker ps | grep -q "product_management_postgres"; then
    echo "⚠️  PostgreSQLコンテナが起動していません。"
    echo "   以下のコマンドでコンテナを起動してください："
    echo "   docker-compose up -d"
    exit 1
fi

# テスト用データベースを作成（存在しない場合）
echo "🗄️  テスト用データベースの準備..."
docker exec product_management_postgres psql -U postgres -c "
    CREATE DATABASE product_management_test;
" 2>/dev/null || echo "   テスト用データベースは既に存在します"

# テスト用データベースにテーブルを作成
echo "📊 テスト用テーブルの作成..."
docker exec product_management_postgres psql -U postgres -d product_management_test -f /docker-entrypoint-initdb.d/init.sql

# PHPテストの実行
echo ""
echo "🚀 テスト実行中..."
echo "=================="

# DockerコンテナでPHPテストを実行
docker exec -e DB_USER=postgres -e DB_PASSWORD=password product_management_php php /var/www/html/../test-code/ProductTest.php

# テスト結果の保存
TEST_RESULT_FILE="${PROJECT_ROOT}/test-results/test_$(date +%Y%m%d_%H%M%S).log"
mkdir -p "${PROJECT_ROOT}/test-results"

echo ""
echo "📁 テスト結果を保存中: $TEST_RESULT_FILE"

# テスト結果をファイルに保存
{
    echo "# PHP Application Test Report"
    echo "Date: $(date)"
    echo "Commit: $(git rev-parse HEAD 2>/dev/null || echo 'No Git Repository')"
    echo ""
    echo "## Test Results"
    docker exec -e DB_USER=postgres -e DB_PASSWORD=password product_management_php php /var/www/html/../test-code/ProductTest.php
} > "$TEST_RESULT_FILE"

echo "✅ テスト実行完了！"
echo ""
echo "📋 次のステップ："
echo "   1. テスト結果を確認してください"
echo "   2. 失敗したテストがある場合は、コードを修正してください"
echo "   3. 新しい機能を追加した際は、対応するテストも追加してください"
echo ""
