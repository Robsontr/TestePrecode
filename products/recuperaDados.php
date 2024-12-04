<?php

require_once 'C:\xampp\htdocs\PHPtestes\BD\pgAdmin.php';
require_once 'C:\xampp\htdocs\PHPtestes\model\ProductsDTO.php';
require_once 'C:\xampp\htdocs\PHPtestes\products\Cart.php';

$id;

session_start();

$sql = "INSERT INTO cart (cart_id, product_id, sku, quantity, price)
            VALUES (:cart_id, :product_id, :sku, :quantity, :price)";

try {
    // Verifica se o ID foi passado na URL e se é um número válido
    if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $productId = (int) $_GET['id']; // Converte para inteiro

        // Consulta SQL para buscar produto e suas variações
        $sql = "
            SELECT
                p.id AS product_id,
                p.price,
                v.sku AS variation_sku,
                v.qty AS variation_qty
            FROM
                products p
            LEFT JOIN
                variations v ON p.id = v.product_id
            WHERE
                p.id = :id;
        ";

        $stmt = $pdo->prepare($sql); // Prepara a consulta
        $stmt->execute([':id' => $productId]); // Executa com o ID passado
        $result = $stmt->fetch(PDO::FETCH_ASSOC); // Obtém o resultado

        if ($result) {
            $productsDTO = new ProductsDTO();
            $productsDTO->setId($result['product_id']);
            $productsDTO->setSku($result['variation_sku']);
            $productsDTO->setPrice($result['price']);
            $productsDTO->setQty(1);

            $cart = new Cart();
            $cart->add($productsDTO); // Adiciona o produto ao carrinho via sessão

            // Verificar se o id_product já existe no banco
            $sql = "SELECT COUNT(*) FROM cart WHERE id_product = :id_product";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id_product' => $productsDTO->getId()]);
            $exists = $stmt->fetchColumn();  // Retorna o número de registros encontrados

            $id = $productsDTO->getId();

            //INSERINDO NO BANCO DE DADOS O CARRINHO
            if ($exists == 0) {
                // Se o id_product não existir, insere um novo registro
                $sql = "INSERT INTO cart (sku, price, quantidade, id_product)
                        VALUES (:sku, :price, :quantidade, :id_product)";

                $sql2 = "INSERT INTO cart_total (total_price)
                        VALUES ( :total_price)";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':sku' => $productsDTO->getSku(),
                    ':price' => $productsDTO->getPrice(),
                    ':quantidade' => $productsDTO->getQty(),
                    ':id_product' => $productsDTO->getId()
                ]);
            } else {
                // Se o id_product já existir, faz a atualização
                $sql = "UPDATE cart
              SET quantidade = quantidade + 1 WHERE id_product = :id_product";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':id_product' => $productsDTO->getId() // Passa o ID do produto
                ]);
            }

            // Imprime uma mensagem ou apenas faz com que os dados sejam enviados sem redirecionar
            echo "Produto adicionado ao carrinho!";
        } else {
            echo "Produto não encontrado.";
        }
    }
} catch (PDOException $e) {
    echo "Erro ao acessar o banco de dados: " . $e->getMessage();
}

try {
    // Configurando o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL para selecionar todos os produtos
    $sqlProducts = "SELECT * FROM products";

    // Preparar e executar a consulta
    $stmt = $pdo->query($sqlProducts);

    // Início do HTML com cabeçalho
    echo "<!DOCTYPE html>
          <html lang='pt-br'>
          <head>
              <meta charset='UTF-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
              <title>Produtos</title>
<link rel='stylesheet' type='text/css' href='style.css'>
          </head>
          <body>
           
              <header>
                  <h1>Lista de Produtos</h1>
              </header>
              <div class='container'>";

    // Criando a tabela para exibir os produtos

    echo "<table border='1'>
   <a href='MyCart.php'>Carrinho     /</a>
   <a href='../index/index.html'>     Cadastro de produtos       /</a>
    <a href='../pedidos/pedidos.php'>     Pedidos   </a>
            <thead>
                <tr>
                <th></th>
                    <th>ID</th>
                    <th>SKU</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Status</th>
                     <th>wordKeys</th>
                      <th>price </th>
                       <th>promotional_price </th>
                        <th>cost </th>
                         <th>weight </th>
                          <th>width</th>
                           <th>brand </th>
                            <th>nbm</th>
                            <th>model </th>
                            <th>gender </th>
                            <th>volumes </th>
                            <th>warrantyTime </th>
                            <th>category </th>
                            <th>subcategory </th>
                            <th>endcategory </th>
                            <th>urlYoutube </th>
                            <th>googleDescription </th>
                             <th>manufacturing  </th>
                             <th>key</th>
                             <th>value</th>
                    <th>ref</th>
                    <th>sku</th>
                    <th>qty</th>
                    <th>ean</th>
                    <th>images</th>
                    <th>Chave Especificação</th>
                    <th>Valor Especificação</th>
                    <th></th>
                </tr>
            </thead>
            
            <tbody>";

    // Iterar sobre os resultados e exibir em linhas de tabela
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Consultar dados relacionados em `attributes`
        $sqlAttributes = "SELECT * FROM attributes WHERE product_id = :product_id";
        $stmtAttributes = $pdo->prepare($sqlAttributes);
        $stmtAttributes->execute([':product_id' => $row['id']]);

        $attributeKey = "";
        $attributeValue = "";
        if ($stmtAttributes->rowCount() > 0) {
            $attribute = $stmtAttributes->fetch(PDO::FETCH_ASSOC);
            $attributeKey = $attribute['key'];
            $attributeValue = $attribute['value'];
        } else {
            $attributeKey = "Nenhum atributo encontrado";
            $attributeValue = "";
        }

        // Consultar dados relacionados em `variations`
        $sqlVariations = "SELECT * FROM variations WHERE product_id = :product_id";
        $stmtVariations = $pdo->prepare($sqlVariations);
        $stmtVariations->execute([':product_id' => $row['id']]);

        $variationRef = "";
        $variationSku = "";
        $variationQty = "";
        $variationEan = "";
        $variationImages = "";

        if ($stmtVariations->rowCount() > 0) {
            $variation = $stmtVariations->fetch(PDO::FETCH_ASSOC);
            $variationRef = $variation['ref'];
            $variationSku = $variation['sku'];
            $variationQty = $variation['qty'];
            $variationEan = $variation['ean'];
            $variationImages = $variation['images'];
        } else {
            $variationRef = "Nenhuma variação encontrada";
            $variationSku = "";
            $variationQty = "";
            $variationEan = "";
            $variationImages = "";
        }

        // Consultar dados relacionados em `specifications`
        $sqlSpecifications = "SELECT * FROM specifications WHERE product_id = :product_id";
        $stmtSpecifications = $pdo->prepare($sqlSpecifications);
        $stmtSpecifications->execute([':product_id' => $row['id']]);

        $specificationKey = "";
        $specificationValue = "";
        if ($stmtSpecifications->rowCount() > 0) {
            $specification = $stmtSpecifications->fetch(PDO::FETCH_ASSOC);
            $specificationKey = $specification['keys'];
            $specificationValue = $specification['values'];
        } else {
            $specificationKey = "Nenhuma especificação encontrada";
            $specificationValue = "";
        }

        // Exibindo os dados na tabela
        echo "<tr>
                 <td>
        
        <a href='Cart.php?id=" . $row['id'] . "'>Add ao Carrinho</a>
       
        </td>
                <td>" . $row['id'] . "</td>
                <td>" . $row['sku'] . "</td>
                <td>" . $row['name'] . "</td>
                <td>" . $row['description'] . "</td>
                <td>" . $row['status'] . "</td>
                <td>" . $row['word_keys'] . "</td>
                <td>R$ " . number_format($row['price'], 2, ',', '.') . "</td>
                <td>" . $row['promotional_price'] . "</td>
                <td>" . $row['cost'] . "</td>
                <td>" . $row['weight'] . "</td>
                <td>" . $row['width'] . "</td>
                <td>" . $row['brand'] . "</td>
                <td>" . $row['nbm'] . "</td>
                <td>" . $row['model'] . "</td>
                <td>" . $row['gender'] . "</td>
                <td>" . $row['volumes'] . "</td>
                <td>" . $row['warranty_time'] . "</td>
                <td>" . $row['category'] . "</td>
                <td>" . $row['subcategory'] . "</td>
                <td>" . $row['endcategory'] . "</td>
                <td>" . $row['url_youtube'] . "</td>
                <td>" . $row['google_description'] . "</td>
                <td>" . $row['manufacturing'] . "</td>
                <td>" . $attributeKey . "</td>
                <td>" . $attributeValue . "</td>
                <td>" . $variationRef . "</td>
                <td>" . $variationSku . "</td>
                <td>" . $variationQty . "</td>
                <td>" . $variationEan . "</td>
                <td>" . $variationImages . "</td>
                <td>" . $specificationKey . "</td>
                <td>" . $specificationValue . "</td>
                <td>
              <a href='editProduct.php?id=" . $row['id'] . "'>Editar</a>
              </td>
              </tr>";
    }

    // Fechando a tabela e o corpo da página
    echo "</tbody></table></div></body></html>";
} catch (PDOException $e) {
    // Exibe erro em caso de falha
    echo "Erro na conexão ou consulta: " . $e->getMessage();
}
