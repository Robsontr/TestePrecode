<?php

require_once 'C:\xampp\htdocs\PHPtestes\BD\pgAdmin.php';

// Passo 2: Buscar os dados da tabela 'pedido' e 'cart_total'
$sql = "SELECT p.numeropedido, c.status
        FROM pedido p
        LEFT JOIN cart_total c ON p.numeropedido = c.numero_pedido";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Passo 3: Exibir os dados em uma tabela HTML
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filtrando números de pedidos únicos
$numerosPedidosUnicos = array_unique(array_column($pedidos, 'numeropedido'));

// Verificar se o botão de aprovação foi pressionado
if (isset($_POST['aprovar'])) {
    $numeroPedido = $_POST['numeropedido']; // Pega o número do pedido enviado pelo formulário
    $acao = $_POST['acao']; // Ação escolhida: 'aprovar' ou 'cancelar'

    // Atualizar o status do pedido conforme a ação
    $novoStatus = ($acao == 'aprovar') ? 'Aprovado' : 'Cancelado';

    // Atualizar o status do pedido no banco de dados
    $sqlAtualizaStatus = "UPDATE cart_total SET status = :novoStatus WHERE numero_pedido = :numeroPedido";
    $stmtAtualiza = $pdo->prepare($sqlAtualizaStatus);
    $stmtAtualiza->bindParam(':numeroPedido', $numeroPedido);
    $stmtAtualiza->bindParam(':novoStatus', $novoStatus);
    $stmtAtualiza->execute();

    // Preparando o JSON para enviar à API (se necessário)
    $pedido = [
        "pedido" => [
            "codigoPedido" => $numeroPedido,
            "idPedidoParceiro" => "string"
        ]
    ];

    $jsonAprov = json_encode($pedido, JSON_PRETTY_PRINT);

    // URL da API externa
    $url = 'https://www.replicade.com.br/api/v1/pedido/pedido';

    // Inicializar cURL
    $ch = curl_init($url);

    // Definir as opções do cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($acao == 'aprovar') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    } else {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic aXdPMzVLZ09EZnRvOHY3M1I6' // Substitua pelo seu token de autenticação (se necessário)
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonAprov);

    // Executar a requisição cURL e obter a resposta
    $response = curl_exec($ch);

    // Verificar se ocorreu algum erro na requisição
    if ($response === false) {
        echo 'Erro cURL: ' . curl_error($ch);
    }

    // Armazenar a resposta para exibição posterior
    $responseData = json_decode($response, true);

    // Exibir a resposta da API em formato legível (JSON Pretty Print)
    echo '<h3>Resposta da API:</h3>';
    echo '<pre>';
    echo json_encode($responseData, JSON_PRETTY_PRINT);  // Exibe a resposta em formato JSON legível
    echo '</pre>';

    // Fechar a conexão cURL
    curl_close($ch);
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' href='style.css'>
    <title>Pedidos</title>
</head>

<body>
    <h1>Lista de Pedidos</h1>

    <?php if (count($numerosPedidosUnicos) > 0): ?>
        <table border="1">
            <thead>
                <a href="../products/recuperaDados.php">Produtos</a>
                <tr>
                    <th>Numero Pedido</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Exibir os pedidos com status correto
                foreach ($numerosPedidosUnicos as $numeroPedido):
                    // Encontrar o status para o número do pedido
                    $status = '';
                    foreach ($pedidos as $pedido) {
                        if ($pedido['numeropedido'] == $numeroPedido) {
                            $status = $pedido['status'];
                            break;
                        }
                    }

                    // Verificar se o pedido foi aprovado ou cancelado
                    $pedidoAprovado = ($status == 'Aprovado');
                    $pedidoCancelado = ($status == 'Cancelado');
                ?>
                    <tr>
                        <td>
                            <!-- Tornando o número de pedido clicável -->
                            <a href="DetalhePedido.php?numeropedido=<?php echo urlencode($numeroPedido); ?>">
                                <?php echo htmlspecialchars($numeroPedido); ?>
                            </a>
                        </td>

                        <td>
                            <!-- Exibir botão de aprovação somente se o pedido não foi aprovado nem cancelado -->
                            <?php if (!$pedidoAprovado && !$pedidoCancelado): ?>
                                <button onclick="abrirModal('<?php echo htmlspecialchars($numeroPedido); ?>')">Pendente</button>
                            <?php elseif ($pedidoAprovado): ?>
                                <span style="color: green;">Aprovado</span>
                            <?php elseif ($pedidoCancelado): ?>
                                <span style="color: red;">Cancelado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>

    <?php else: ?>
        <p>Nenhum pedido encontrado.</p>
    <?php endif; ?>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">&times;</span>
            <h2>Status</h2>
            <form method="POST">
                <input type="hidden" name="numeropedido" id="modalNumeroPedido" />
                <label for="acao">Seleção:</label><br>
                <input type="radio" id="aprovar" name="acao" value="aprovar" checked>
                <label for="aprovar">Aprovar</label><br>
                <input type="radio" id="cancelar" name="acao" value="cancelar">
                <label for="cancelar">Cancelar</label><br><br>
                <input type="submit" name="aprovar" value="Confirmar" />
            </form>
        </div>
    </div>

    <script>
        // Função para abrir o modal
        function abrirModal(numeroPedido) {
            document.getElementById("modalNumeroPedido").value = numeroPedido;
            document.getElementById("myModal").style.display = "block";
        }

        // Função para fechar o modal
        function fecharModal() {
            document.getElementById("myModal").style.display = "none";
        }

        // Fechar o modal quando clicar fora da área do modal
        window.onclick = function(event) {
            if (event.target == document.getElementById("myModal")) {
                fecharModal();
            }
        }
    </script>
</body>

</html>