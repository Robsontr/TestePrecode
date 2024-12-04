<?php

require_once 'Cart.php';

// Cria o carrinho
$cart = new Cart();
$cartData = $cart->getCart();

// Verifica se o carrinho está vazio
if (empty($cartData)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'O carrinho está vazio.'
    ]);
    exit;
}

// total calculado no banco
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$totalPrice = $result['total_price'] ?? 0; // Se o resultado for null, define o total como 0

$pedido = [
    'pedido' => [
        'idPedidoParceiro' => 'null',
        'valorFrete' => 0,
        'prazoEntrega' => 1,
        'valorTotalCompra' => array_sum(array_map(function ($product) {
            return $product->getPrice() * $product->getQty();
        }, $cartData)),
        'formaPagamento' => 4,
        'dadosCliente' => [
            'cpfCnpj' => 'string',
            'nomeRazao' => 'string',
            'fantasia' => 'string',
            'sexo' => 'string',
            'dataNascimento' => 'string',
            'email' => 'string',
            'dadosEntrega' => [
                'cep' => 'string',
                'endereco' => 'string',
                'numero' => 'string',
                'bairro' => 'string',
                'complemento' => 'string',
                'cidade' => 'string',
                'uf' => 'string',
                'responsavelRecebimento' => 'string'
            ],
            'telefones' => [
                'residencial' => 'string',
                'comercial' => 'string',
                'celular' => 'string'
            ]
        ],
        'pagamento' => [
            [
                'valor' => array_sum(array_map(function ($product) {
                    return $product->getPrice() * $product->getQty();
                }, $cartData)),
                'quantidadeParcelas' => 0,
                'meioPagamento' => 'string',
                'autorizacao' => 'string',
                'nsu' => 'string',
                'sefaz' => [
                    'idOperacao' => 'string',
                    'idFormaPagamento' => 'string',
                    'idMeioPagamento' => 'string',
                    'cnpjInstituicaoPagamento' => 'string',
                    'idBandeira' => 'string',
                    'autorizacao' => 'string',
                    'cnpjIntermediadorTransacao' => 'string',
                    'intermediadorIdentificador' => 'string'
                ]
            ]
        ],
        'itens' => array_map(function ($product) {
            return [
                'sku' => $product->getSku(),
                'valorUnitario' => $product->getPrice(),
                'quantidade' => $product->getQty()
            ];
        }, $cartData)  // Preenche os itens com os produtos do carrinho
    ]
];


$pedidoJson = json_encode($pedido, JSON_PRETTY_PRINT);

//API
$apiUrl = 'https://www.replicade.com.br/api/v1/pedido/pedido';

// Inicializa o cURL
$ch = curl_init($apiUrl);

// Configura as opções do cURL
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic aXdPMzVLZ09EZnRvOHY3M1I6'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $pedidoJson);

$response = curl_exec($ch);

// erros
if (curl_errno($ch)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro na requisição cURL: ' . curl_error($ch)
    ]);
} else {
    // Processa de resposta API
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode === 201 || $httpCode === 200) {
        echo json_encode([

            'response' => json_decode($response, true) // Decodifica o JSON da resposta da API
        ]);


        $responseData = json_decode($response, true);

        // Exibe a resposta da API em formato legível (JSON Pretty Print)
        echo '<h3>Resposta da API (em JSON):</h3>';
        echo '<pre>';
        echo json_encode($responseData, JSON_PRETTY_PRINT);  // Exibe a resposta em formato JSON legível
        echo '</pre>';

        if (isset($_SESSION['cart'])) {

            // Limpa a sessão do carrinho
            $_SESSION['cart']['productsDTO'] = []; // Remove todos os produtos
            $_SESSION['cart']['total'] = 0;       // Zera o total

            // Decodifica a resposta para obter o número do pedido
            $data = json_decode($response, true);
            $numeroPedido = $data["pedido"]['numeroPedido'];

            // Calcula o total_price com os dados do carrinho na sessão
            $totalPrice = array_sum(array_map(function ($product) {
                return $product->getPrice() * $product->getQty();
            }, $cartData)); //


            $sql = "SELECT * FROM cart";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // Busca como array associativo

            $insertSql = "INSERT INTO pedido (sku, price, quantidade, id_cliente, numeropedido)
                          VALUES (:sku, :price, :quantidade, :id_cliente, :numeropedido)";
            $insertStmt = $pdo->prepare($insertSql);

            // Itera sobre os resultados e insere na tabela 'cart_backup'
            foreach ($results as $row) {
                $insertStmt->execute([
                    ':sku' => $row['sku'],
                    ':price' => $row['price'],
                    ':quantidade' => $row['quantidade'],
                    ':numeropedido' => $numeroPedido,
                    ':id_cliente' => 1 // Ajuste conforme o nome correto
                ]);
            }

            // Insere o total e o número do pedido no banco de dados
            $sql = "INSERT INTO cart_total (total_price, numero_pedido, status) values (:total_price, :numero_pedido, :status)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':total_price' => $totalPrice,      // Placeholder corrigido
                ':numero_pedido' => $numeroPedido,  // Placeholder corrigido
                ':status' => 'Pendente'
            ]);

            // Remove todos os itens do banco de dados
            $sql = "DELETE FROM cart";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
    } else {

        echo $pedidoJson;
        echo json_encode([

            'response' => json_decode($response, true)
        ]);
    }
}

// Fecha o cURL
curl_close($ch);
