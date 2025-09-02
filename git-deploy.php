<?php
$secret = "chave-secreta-teste-instancia-git";

// Pega o corpo da requisição
$payload = file_get_contents("php://input");
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

// Calcula assinatura com o mesmo secret
$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

// Compara
if (!hash_equals($hash, $signature)) {
    http_response_code(403);
    echo "Assinatura inválida!";
    exit;
}

$data = json_decode($payload, true);

// Só faz deploy se for branch main
if ($data['ref'] === 'refs/heads/main') {
    $output = shell_exec('cd /opt/bitnami/apache/htdocs/producao && git pull origin main 2>&1');
    file_put_contents('/opt/bitnami/apache/logs/deploy.log', date('Y-m-d H:i:s') . " - Deploy executado\n" . $output . "\n", FILE_APPEND);
    echo "Deploy executado!";
} else {
    echo "Push não é da main, ignorado.";
}
?>