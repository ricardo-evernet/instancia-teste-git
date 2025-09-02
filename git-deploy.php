<?php
// Segurança básica: verifique se o push foi para a branch main
$payload = json_decode(file_get_contents('php://input'), true);

if ($payload['ref'] === 'refs/heads/main') {
    $output = shell_exec('cd /opt/bitnami/apache/htdocs/producao && git pull origin main 2>&1');
    file_put_contents('/opt/bitnami/apache/logs/deploy.log', date('Y-m-d H:i:s') . " - Deploy executado\n" . $output . "\n", FILE_APPEND);
    http_response_code(200);
    echo "Deploy executado!";
} else {
    http_response_code(200);
    echo "Push não é da main, ignorado.";
}
?>