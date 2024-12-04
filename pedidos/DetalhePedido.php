<?php

require_once 'C:\xampp\htdocs\PHPtestes\BD\pgAdmin.php';

try {
    // Verificar se o número do pedido foi passado via URL
    if (isset($_GET['numeropedido'])) {
        $numeroPedido = $_GET['numeropedido'];

        // Buscar os detalhes de todos os pedidos com o mesmo número
        $sql = "SELECT * FROM pedido WHERE numeropedido = :numeropedido";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['numeropedido' => $numeroPedido]);

        // Buscar o preço total do pedido na tabela cart_total
        $sql_total = "SELECT total_price FROM cart_total WHERE numero_pedido = :numeropedido";
        $stmt_total = $pdo->prepare($sql_total);
        $stmt_total->execute(['numeropedido' => $numeroPedido]);

        // Recuperar todos os pedidos com o mesmo número de pedido
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalPrice = $stmt_total->fetch(PDO::FETCH_ASSOC); // Recuperar o total_price

        // Verificar se há pedidos encontrados
        if (count($pedidos) > 0) {
            echo "<!DOCTYPE html>
            <html lang='pt-BR'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Detalhes do Pedido</title>
                <link rel='stylesheet' type='text/css' href='style.css'>
            </head>
            <body>";

            echo "<h1>Detalhes dos Pedidos #{$numeroPedido}</h1>";
            echo "<a href='Pedidos.php'>Voltar</a>";
            echo "<table border='1'>
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Preço Unitário</th>
                            <th>Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($pedidos as $pedido) {
                echo "<tr>
                        <td>" . htmlspecialchars($pedido['sku']) . "</td>
                        <td>R$ " . number_format((float)$pedido['price'], 2, ',', '.') . "</td>
                        <td>" . htmlspecialchars($pedido['quantidade']) . "</td>
                    </tr>";
            }

            echo "</tbody></table>";

            // Exibir o total_price logo após a tabela
            if ($totalPrice) {
                echo "<h2>Total do Pedido: R$ " . number_format((float)$totalPrice['total_price'], 2, ',', '.') . "</h2>";
            } else {
                echo "<p>Total não encontrado.</p>";
            }

            echo "</body></html>";
        } else {
            echo "<p>Nenhum pedido encontrado para o número {$numeroPedido}.</p>";
        }
    } else {
        echo "<p>Nenhum número de pedido foi fornecido.</p>";
    }
} catch (Exception $e) {
    echo "<p>Erro ao processar a solicitação: " . htmlspecialchars($e->getMessage()) . "</p>";
}
