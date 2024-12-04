<?php
require_once 'C:\xampp\htdocs\PHPtestes\BD\pgAdmin.php';
require_once 'C:/xampp/htdocs/PHPtestes/model/ProductsDTO.php';
require_once 'C:/xampp/htdocs/PHPtestes/products/recuperaDados.php';
require_once 'C:/xampp/htdocs/PHPtestes/model/CartDTO.php';

class Cart
{
    public function __construct()
    {
        // Inicializa a sessão e o carrinho se ainda não existir
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [
                'productsDTO' => [],
                'total' => 0
            ];
        }
    }



    public function add(ProductsDTO $productsDTO)
    {
        $inCart = false;
        // Atualiza o total ao adicionar o produto
        $this->setTotal($productsDTO);


        // Verifica se o produto já está no carrinho
        foreach ($this->getCart() as $productInCart) {
            if ($productInCart->getId() === $productsDTO->getId()) {
                // Atualiza a quantidade se o produto já estiver no carrinho
                $quantity = $productInCart->getQty() + $productsDTO->getQty();
                $productInCart->setQty($quantity); // Usa o setter para atualizar a quantidade
                $inCart = true;
                break;
            }
        }

        // Se o produto não estiver no carrinho, adiciona-o
        if (!$inCart) {
            $this->setProductsInCart($productsDTO);
        }
    }


    private function setProductsInCart(ProductsDTO $productsDTO)
    {
        // Adiciona o produto ao carrinho
        $_SESSION['cart']['productsDTO'][] = $productsDTO;
    }



    private function setTotal(ProductsDTO $productsDTO)
    {
        // Atualiza o total do carrinho
        $_SESSION['cart']['total'] += $productsDTO->getPrice() * $productsDTO->getQty();
    }




    public function remove(int $id)
    {
        global $pdo;

        if (isset($_SESSION['cart']['productsDTO'])) {
            foreach ($this->getCart() as $index => $product) {
                if ($product->getId() === $id) {
                    unset($_SESSION['cart']['productsDTO'][$index]);
                    $_SESSION['cart']['total'] -= $product->getPrice() * $product->getQty();
                }
            }
        }
        // Remove o item do banco de dados
        $sql = "DELETE FROM cart";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([]);
    }

    private function updateTotal()
    {
        // Recalcula o total do carrinho
        $total = 0;
        foreach ($this->getCart() as $product) {
            $total += $product->getPrice() * $product->getQty();
        }
        $_SESSION['cart']['total'] = $total;
    }

    public function getCart()
    {
        return array_filter($_SESSION['cart']['productsDTO'], function ($product) {
            return $product->getId() !== null;
        });
    }

    public function getTotal()
    {
        // Filtra os produtos para garantir que só os com ID não nulo sejam considerados
        $validProducts = array_filter($_SESSION['cart']['productsDTO'], function ($product) {
            return $product->getId() !== null;
        });

        // Se houver produtos válidos, calcula o total
        $total = 0;
        foreach ($validProducts as $product) {
            $total += $product->getPrice() * $product->getQty();
        }

        // Retorna o total calculado ou 0 se não houver produtos válidos
        return $total;
    }
}
