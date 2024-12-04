<?php

require_once 'C:/xampp/htdocs/PHPtestes/model/Products.php';
require_once 'C:/xampp/htdocs/PHPtestes/model/Specifications.php';
require_once 'C:/xampp/htdocs/PHPtestes/model/attributes.php';
require_once 'C:/xampp/htdocs/PHPtestes/model/variations.php';
require_once 'C:\xampp\htdocs\PHPtestes\BD\pgAdmin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $price = $_POST['price'];
    $promotionalPrice = $_POST['promotionalPrice'];
    $name = $_POST['name'];


    if ($promotionalPrice >= $price) {
        if (strlen($name) > 10) {
            // Definir os campos obrigatórios
            $requiredFields = [
                'name',
                'description',
                'status',
                'price',
                'promotionalPrice',
                'cost',
                'weight',
                'width',
                'height',
                'length',
                'brand'
            ];

            $errors = [];

            // Validar os campos
            foreach ($requiredFields as $field => $label) {
                if (empty($_POST[$field])) {
                    $errors[$field] = "O campo '$label' é obrigatório.";
                }
            }

            // Verificar os campos obrigatórios
            foreach ($requiredFields as $field => $label) {
                if (empty($_POST[$field])) {
                    $errors[$field] = "O campo '$label' é obrigatório.";
                }
            }

            $sku = $_POST['sku'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $shortName = $_POST['shortName'];
            $status = $_POST['status'];
            $wordKeys = $_POST['wordKeys'];
            $price = $_POST['price'];
            $promotionalPrice = $_POST['promotionalPrice'];
            $cost = $_POST['cost'];
            $weight = $_POST['weight'];
            $width = $_POST['width'];
            $height = $_POST['height'];
            $length = $_POST['length'];
            $brand = $_POST['brand'];
            $urlYoutube = $_POST["urlYoutube"];
            $googleDescription = $_POST["googleDescription"];
            $manufacturing = $_POST["manufacturing"];
            $nbm = $_POST["nbm"];
            $model = $_POST["model"];
            $gender = $_POST["gender"];
            $volumes = $_POST["volumes"];
            $warrantyTime = $_POST["warrantyTime"];
            $category = $_POST["category"];
            $subcategory = $_POST["subcategory"];
            $endcategory = $_POST["endcategory"];

            // SPECIFICATIONS
            $keys = $_POST['keys'];
            $values = $_POST['values'];

            // ATTRIBUTES
            $key = $_POST['key'];
            $value = $_POST['value'];

            // VARIATIONS
            $ref = $_POST['ref'];
            $sku = $_POST['sku'];
            $qty = $_POST['qty'];
            $ean = $_POST['ean'];
            $images = $_POST['images'];


            // INSTANCIA PRODUCTS
            $products = new Products(
                $name,
                $description,
                $status,
                $price,
                $promotionalPrice,
                $cost,
                $weight,
                $width,
                $height,
                $length,
                $brand
            );

            if ($sku == null) {
                $products->setSku(null);
            } else {
                $products->setSku($sku);
            }

            $products->setShortName($shortName);
            $products->setWordKeys($wordKeys);
            $products->setUrlYoutube($urlYoutube);
            $products->setGoogleDescription($googleDescription);
            $products->setManufacturing($manufacturing);
            $products->setNbm($nbm);
            $products->setModel($model);
            $products->setGender($gender);

            if ($volumes == null) {
                $products->setVolumes(0);
            } else {
                $products->setVolumes($volumes);
            }

            if ($warrantyTime == null) {
                $products->setWarrantyTime(0);
            } else {
                $products->setWarrantyTime($warrantyTime);
            }

            $products->setCategory($category);
            $products->setSubcategory($subcategory);
            $products->setEndcategory($endcategory);

            //INSERÇÃO AO BANCO DE DADOS
            $sql = "INSERT INTO products (
    sku, name, description, short_name, status, word_keys, price,
    promotional_price, cost, weight, width, height, length, brand,
    url_youtube, google_description, manufacturing, nbm, model, gender,
    volumes, warranty_time, category, subcategory, endcategory)
    VALUES (
        :sku, :name, :description, :short_name, :status, :word_keys, :price,
        :promotional_price, :cost, :weight, :width, :height, :length, :brand,
        :url_youtube, :google_description, :manufacturing, :nbm, :model, :gender,
        :volumes, :warranty_time, :category, :subcategory, :endcategory)";

            $stmt = $pdo->prepare($sql);

            // Executar a query com os valores
            $stmt->execute([
                ':sku' => $products->getSku(),
                ':name' => $products->getName(),
                ':description' => $products->getDescription(),
                ':short_name' => $products->getShortName(),
                ':status' => $products->getStatus(),
                ':word_keys' => $products->getWordKeys(),
                ':price' => $products->getPrice(),
                ':promotional_price' => $products->getPromotionalPrice(),
                ':cost' => $products->getCost(),
                ':weight' => $products->getWeight(),
                ':width' => $products->getWidth(),
                ':height' => $products->getHeight(),
                ':length' => $products->getLength(),
                ':brand' => $products->getBrand(),
                ':url_youtube' => $products->getUrlYoutube(),
                ':google_description' => $products->getGoogleDescription(),
                ':manufacturing' => $products->getManufacturing(),
                ':nbm' => $products->getNbm(),
                ':model' => $products->getModel(),
                ':gender' => $products->getGender(),
                ':volumes' => $products->getVolumes(),
                ':warranty_time' => $products->getWarrantyTime(),
                ':category' => $products->getCategory(),
                ':subcategory' => $products->getSubcategory(),
                ':endcategory' => $products->getEndcategory()
            ]);

            $id = $pdo->lastInsertId(); // Recupera o último ID inserido

            echo "Produto salvo com sucesso!" . PHP_EOL;



            // Configurando as especificações
            $specifications = new Specifications();
            $specifications->setKeys($keys);
            $specifications->setValues($values);
            $specifications->setProductId($id);

            // Preparando a consulta SQL
            $sql = "INSERT INTO specifications (keys, values, product_id) VALUES (:keys, :values, :id)";

            // Executando a consulta
            $stmt = $pdo->prepare($sql);


            $stmt->execute([
                ':keys' => $specifications->getKeys(),
                ':values' => $specifications->getValues(),
                ':id' => $specifications->getProductId()
            ]);

            echo "Specifications salvo com sucesso!" . PHP_EOL;


            // Instanciar a classe Specifications e definir os valores
            $attributes = new Attributes();
            $attributes->setKey($key);
            $attributes->setValue($value);
            $attributes->setProductId($id);


            $sql = "INSERT INTO Attributes (key, value, product_id) VALUES (:key, :value, :id)";

            $stmt = $pdo->prepare($sql);

            // Executar a query com os valores
            $stmt->execute([
                ':key' => $attributes->getKey(),
                ':value' => $attributes->getValue(),
                ':id' => $attributes->getProductId()
            ]);

            echo "Attributes salvo com sucesso!" . PHP_EOL;

            $variations = new Variations();
            $variations->setRef($ref);

            if ($sku == null) {
                $variations->setSku(0);
            } else {
                $variations->setSku($sku);
            }

            $variations->setQty($qty);
            $variations->setEan($ean);
            $variations->setImages($images);
            $variations->setProductId($id);

            $sql = "INSERT INTO Variations (ref, sku, qty, ean, images, product_id) VALUES (:ref, :sku, :qty, :ean, :images, :id)";

            $stmt = $pdo->prepare($sql);

            // Executar a query com os valores
            $stmt->execute([
                ':ref' => $variations->getRef(),
                ':sku' => $variations->getSku(),
                ':qty' => $variations->getQty(),
                ':ean' => $variations->getEan(),
                ':images' => $variations->getImages(),
                ':id' => $variations->getProductId()
            ]);

            echo "Variations salvo com sucesso!" . PHP_EOL;

            /*
    echo PHP_EOL . "sku: " . $products->getSku() . PHP_EOL;
    echo "Name: " . $products->getName() . PHP_EOL;
    echo "Description: " . $products->getDescription() . PHP_EOL;
    echo "ShortName: " . $products->getShortName() . PHP_EOL;
    echo "Status: " . $products->getStatus() . PHP_EOL;
    echo "WordKeys: " . $products->getWordKeys() . PHP_EOL;
    echo "Price: " . $products->getPrice() . PHP_EOL;
    echo "PromotionalPrice: " . $products->getPromotionalPrice() . PHP_EOL;
    echo "Cost: " . $products->getCost() . PHP_EOL;
    echo "Weight: " . $products->getWeight() . PHP_EOL;
    echo "Width: " . $products->getWidth() . PHP_EOL;
    echo "Height: " . $products->getHeight() . PHP_EOL;
    echo "Length: " . $products->getLength() . PHP_EOL;
    echo "Brand: " . $products->getBrand() . PHP_EOL;
    echo "urlYoutube: " . $products->geturlYoutube() . PHP_EOL;
    echo "GoogleDescription: " . $products->getGoogleDescription() . PHP_EOL;
    echo "Manufacturing: " . $products->getManufacturing() . PHP_EOL;
    echo "Nbm: " . $products->getNbm() . PHP_EOL;
    echo "Model: " . $products->getModel() . PHP_EOL;
    echo "Gender: " . $products->getGender() . PHP_EOL;
    echo "Volumes: " . $products->getVolumes() . PHP_EOL;
    echo "WarrantyTime: " . $products->getWarrantyTime() . PHP_EOL;
    echo "Category: " . $products->getCategory() . PHP_EOL;
    echo "Subcategory: " . $products->getSubcategory() . PHP_EOL;
    echo "Endcategory: " . $products->getEndcategory() . PHP_EOL;

    
*/


            // Montar os dados em um array associativo
            $data = [
                "product" => [
                    "sku" => $products->getSku(),
                    "name" => $products->getName(),
                    "description" => $products->getDescription(),
                    "shortName" => $products->getShortName(),
                    "status" => $products->getStatus(),
                    "wordKeys" => $products->getWordKeys(),
                    "price" => (float)$products->getPrice(),
                    "promotional_price" => (float)$products->getPromotionalPrice(),
                    "cost" => (float)$products->getCost(),
                    "weight" => (float)$products->getWeight(),
                    "width" => (float)$products->getWidth(),
                    "height" => (float)$products->getHeight(),
                    "length" => (float)$products->getLength(),
                    "brand" => $products->getBrand(),
                    "urlYoutube" => $products->getUrlYoutube(),
                    "googleDescription" => $products->getGoogleDescription(),
                    "manufacturing" => $products->getManufacturing(),
                    "nbm" => $products->getNbm(),
                    "model" => $products->getModel(),
                    "gender" => $products->getGender(),
                    "volumes" => (int)$products->getVolumes(),
                    "warrantyTime" => (int)$products->getWarrantyTime(),
                    "category" => $products->getCategory(),
                    "subcategory" => $products->getSubcategory(),
                    "endcategory" => $products->getEndcategory(),
                    "attribute" => [
                        [
                            "key" => $attributes->getKey(),
                            "value" => $attributes->getValue(),
                        ]
                    ],
                    "variations" => [
                        [
                            "ref" => $variations->getRef(),
                            "sku" => $variations->getSku(),
                            "qty" => (int)$variations->getQty(),
                            "ean" => $variations->getEan(),
                            "images" => [
                                $variations->getImages() // Transformar imagens em array se necessário
                            ],
                            "specifications" => [
                                [
                                    "key" => $specifications->getKeys(),
                                    "value" => $specifications->getValues(),
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            // Converter para JSON
            $jsonData = json_encode($data, JSON_PRETTY_PRINT);
            echo "JSON Gerado:\n";
            echo $jsonData;


            // URL da API externa
            $url = 'https://www.replicade.com.br/api/v3/products';

            // Inicializar cURL
            $ch = curl_init($url);

            // Definir as opções do cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retornar a resposta como string
            curl_setopt($ch, CURLOPT_POST, true); // Usar o método POST
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json', // Definir o tipo de conteúdo como JSON
                'Authorization: Basic aXdPMzVLZ09EZnRvOHY3M1I6' // Substitua pelo seu token de autenticação (se necessário)
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Enviar os dados JSON gerados no corpo da requisição

            // Executar a requisição cURL e obter a resposta
            $response = curl_exec($ch);

            $data = json_decode($response, true);

            // Verificar se ocorreu algum erro
            if ($response === false) {
                echo 'Erro cURL: ' . curl_error($ch);
            } else {
                echo 'Resposta da API: ' . $response;

                /*
        print_r("Message: " . $data["message"] . PHP_EOL);
        print_r("Pai: " . $data["sku"] . PHP_EOL);
        print_r("Sku: " . $data["variations"][0]["sku"] . PHP_EOL);
        */

                $skuPai = $data["sku"];
                $sku = $data["variations"][0]["sku"];

                $sql = "UPDATE products SET sku = :sku WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':sku' => $skuPai,
                    ':id' => $id
                ]);

                //ATUALIZAÇÃO DE SKU DA TABELA VARIATIONS
                $sql = "UPDATE variations SET sku = :sku WHERE product_id = :product_id";
                $stmt = $pdo->prepare($sql);
                $updated = $stmt->execute([
                    ':sku' => $sku,
                    ':product_id' => $id
                ]);
            }

            curl_close($ch);
        } else {
            echo 'o campo name não pode ter menos que 10 caracteres!';
        }
    } else {
        echo 'o promotionalPrice ' . $_POST['promotionalPrice'] . ' não pode ser menor que price '  . $_POST['price'];
    }
}
