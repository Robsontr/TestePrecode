<?php
require_once 'C:\xampp\htdocs\PHPtestes\BD\pgAdmin.php';
require_once 'C:\xampp\htdocs\PHPtestes\model\ProductsDTO.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $productsDTO = new ProductsDTO();
        $productsDTO->setId($_POST['id']);
        $productsDTO->setSku($_POST['sku']);
        $productsDTO->setQty($_POST['qty']);
        $productsDTO->setPrice($_POST['price']);
        $productsDTO->setStatus($_POST['status']);

        /*
        echo "id: " . $productsDTO->getId() . PHP_EOL;
        echo "sku: " . $productsDTO->getSku() . PHP_EOL;
        echo "qty: " . $productsDTO->getQty() . PHP_EOL;
        echo "price: " . $productsDTO->getPrice() . PHP_EOL;
        echo "status: " . $productsDTO->getStatus() . PHP_EOL;
        */

        $pdo->beginTransaction();

        $productId = $_POST['id'];

        // Consulta o produto atual no banco
        $sql = "SELECT * FROM products WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);


        // Converte os preços para número de ponto flutuante
        $atualPrice = floatval($product['price']);
        $newPrice = floatval($_POST['price']);

        // Exibe os preços corretamente
        echo 'Preço atual: ' . $atualPrice . '<br>';
        echo 'Preço Novo: ' . $newPrice . '<br>';

        // Calcula os valores para comparação
        $d = ($atualPrice * 0.502) + $atualPrice;  // Preço máximo permitido
        $d2 = ($atualPrice * 0.668);  // Preço mínimo permitido




        // Verifica se o novo preço é maior que 50% do preço atual
        // if ($newPrice > $atualPrice * 1.5) {
        //    die("O novo preço não pode ser maior que 50% do preço atual!");
        // }

        if ($newPrice < $d2 || $newPrice > $d) {
            echo 'O novo preço não pode ser maior ou menor que 50% do preço atual! <br> O novo preço precisa ser entre ' . $d2 . ' e ' . $d;
            die("");
        } else {

            $data = [
                "products" => [
                    [
                        "ref" => "",
                        "sku" => (int) $productsDTO->getSku(),
                        "promotional_price" => (float) $productsDTO->getPrice(),
                        "price" => (float) $productsDTO->getPrice(),
                        "priceSite" => 0,
                        "cost" => 0,
                        "shippingTime" => 0,
                        "status" => $productsDTO->getStatus(),
                        "qty" => (int) $productsDTO->getQty(),
                        "stock" => [
                            [
                                "stores" => 1,
                                "availableStock" => 0,
                                "realStock" => 0
                            ]
                        ]
                    ]
                ]
            ];

            $jsonData = json_encode($data, JSON_PRETTY_PRINT);
            echo 'Json: ' . $jsonData;


            $url = 'https://www.replicade.com.br/api/v3/products/inventory';

            // Inicializar cURL
            $ch = curl_init($url);

            // Definir as opções do cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retornar a resposta como string
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Basic aXdPMzVLZ09EZnRvOHY3M1I6'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Enviar os dados JSON gerados no corpo da requisição

            // Executar a requisição cURL e obter a resposta
            $response = curl_exec($ch);
            $jsonResponse = json_decode($response, true);



            // Verificar se ocorreu algum erro com cURL
            if ($response === false) {
                throw new Exception('Erro cURL: ' . curl_error($ch));
            } else {
                echo 'Resposta da API: ' . $response;
            }

            curl_close($ch);

            // Decodificar a resposta JSON
            $data = json_decode($response, true);

            // Verificar se a chave 'products' e a chave 'return' existem
            if (isset($data['products'][0]['return']) && is_array($data['products'][0]['return'])) {

                // Iterar sobre o array de retornos e exibir as mensagens
                foreach ($data['products'][0]['return'] as $returnItem) {
                    if (isset($returnItem['message'])) {
                        echo "Mensagem: " . $returnItem['message'] . "<br>";
                    }
                }
            } else {
                echo "Não foram encontradas mensagens na resposta da API.";
            }

            // Atualizar o produto no banco de dados
            $sql = "UPDATE products SET price = :price, status = :status WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':price' => $productsDTO->getPrice(),
                ':status' => $productsDTO->getStatus(),
                ':id' => $productsDTO->getId()
            ]);

            // Verificar se o produto foi atualizado
            if ($stmt->rowCount() === 0) {
                throw new Exception("Produto não encontrado para atualização.");
            }

            // Atualizar a quantidade no banco de dados
            $sql = "UPDATE variations SET qty = :qty WHERE product_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':qty' => $productsDTO->getQty(),
                ':id' => $productsDTO->getId()
            ]);

            $pdo->commit();

            // Redirecionar após sucesso

            // }
        }
    } else {
        echo "Método inválido!";
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack(); // Reverter transação em caso de erro
    }
    die("Erro ao atualizar produto: " . $e->getMessage());
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack(); // Reverter transação em caso de erro
    }
    die("Erro: " . $e->getMessage());
}
