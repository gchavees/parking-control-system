<?php
require 'config/config.php';
date_default_timezone_set('America/Sao_Paulo');

$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => ''];

try {
    switch ($action) {
        case 'entrada':
            $placa = strtoupper(preg_replace('/[^A-Z0-9]/', '', $_POST['placa']));
            if (empty($placa)) throw new Exception('Placa inválida');

            $agoraBD = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare("INSERT INTO veiculos (placa, data_entrada) VALUES (?, ?)");
            $stmt->execute([$placa, $agoraBD]);

            $response['success'] = true;
            $response['message'] = 'Entrada registrada com sucesso';
            $response['placa'] = $placa;
            $response['data_entrada'] = date('d/m/Y H:i:s', strtotime($agoraBD));
            break;

        case 'saida':
            $placa = strtoupper(preg_replace('/[^A-Z0-9]/', '', $_POST['placa']));
            if(empty($placa)) throw new Exception('Placa inválida');

            // Buscar veículo sem saída registrada
            $stmt = $pdo->prepare("SELECT * FROM veiculos WHERE placa = ? AND data_saida IS NULL ORDER BY data_entrada DESC LIMIT 1");
            $stmt->execute([$placa]);
            $veiculo = $stmt->fetch();
            if(!$veiculo) throw new Exception('Veículo não encontrado ou já saiu');

            // Buscar valor da tarifa atual
            $stmt = $pdo->query("SELECT valor_hora FROM precos ORDER BY id DESC LIMIT 1");
            $preco = $stmt->fetch()['valor_hora'];

            $entrada = new DateTime($veiculo['data_entrada']);
            $saida = new DateTime(); // hora atual para saída

            // Tolerância de 15 minutos
            $saidaTolerante = clone $saida;
            $saidaTolerante->sub(new DateInterval('PT15M'));

            $diff = $entrada->diff($saidaTolerante);
            $horas = $diff->h + ($diff->days * 24);
            if($diff->i > 0 || $diff->s > 0){
                $horas++;
            }
            $horas = max($horas, 0);
            $valor = $horas * $preco;

            // Obtém a forma de pagamento enviada
            $pagamento = $_POST['pagamento'] ?? 'Dinheiro';

            $agora = $saida->format('Y-m-d H:i:s');
            $stmt = $pdo->prepare("UPDATE veiculos SET data_saida = ?, valor = ?, pagamento = ? WHERE id = ?");
            $stmt->execute([$agora, $valor, $pagamento, $veiculo['id']]);

            $response['success'] = true;
            $response['message'] = 'Saída registrada com sucesso';
            $response['placa'] = $placa;
            $response['data_entrada'] = date('d/m/Y H:i:s', strtotime($veiculo['data_entrada']));
            $response['data_saida'] = date('d/m/Y H:i:s');
            $response['valor'] = number_format($valor, 2, ',', '.');
            $response['pagamento'] = $pagamento;
            break;

        case 'listar_estacionados':
            $stmt = $pdo->query("SELECT placa, DATE_FORMAT(data_entrada, '%d/%m/%Y %H:%i:%s') as data_entrada FROM veiculos WHERE data_saida IS NULL ORDER BY data_entrada DESC");
            $veiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response['success'] = true;
            $response['veiculos'] = $veiculos;
            break;

        case 'fechamento_caixa':
            // Data atual
            $hoje = date('Y-m-d');
            
            // Soma total dos valores no dia (com saída registrada)
            $stmt = $pdo->prepare("SELECT SUM(valor) as total FROM veiculos WHERE DATE(data_saida) = ?");
            $stmt->execute([$hoje]);
            $total = $stmt->fetch()['total'] ?: 0;
            
            // Verifica se já existe um fechamento para o dia
            $stmt = $pdo->prepare("SELECT id FROM fechamento_caixa WHERE data = ?");
            $stmt->execute([$hoje]);
            $registro = $stmt->fetch();
            
            if ($registro) {
                // Atualiza o registro se já existir
                $stmt = $pdo->prepare("UPDATE fechamento_caixa SET total = ? WHERE id = ?");
                $stmt->execute([$total, $registro['id']]);
            } else {
                // Insere novo registro se não existir
                $stmt = $pdo->prepare("INSERT INTO fechamento_caixa (data, total) VALUES (?, ?)");
                $stmt->execute([$hoje, $total]);
            }
            
            $response['success'] = true;
            $response['total'] = number_format($total, 2, ',', '.');
            break;

        default:
            throw new Exception('Ação inválida');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>