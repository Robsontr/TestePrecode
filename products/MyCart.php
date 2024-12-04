<?php

require_once 'C:/xampp/htdocs/PHPtestes/model/ProductsDTO.php';
require_once 'Cart.php';

$cart = new Cart();

// Verifica se o ID do produto foi passado na URL para remoção
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $cart->remove($id);
}

// Obtém os produtos do carrinho após a remoção
$productsInCart = $cart->getCart();

?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <h1>Seu Carrinho</h1>

    <a href="recuperaDados.php" class="back-link">Voltar aos Produtos</a>

    <?php if (empty($productsInCart)): ?>
        <p class="empty-cart">O carrinho está vazio.</p>
    <?php else: ?>
        <div class="cart-section">
            <a href="Pedido.php" class="create-order-link">Criar pedido</a>
            <ul class="cart-list">
                <?php foreach ($productsInCart as $product): ?>
                    <li class="cart-item">
                        <a href="?id=<?php echo $product->getId(); ?>" class="remove-link">Remover</a>
                        <div>
                            <strong>SKU:</strong> <?php echo htmlspecialchars($product->getSku()); ?><br>
                            <strong>Quantidade:</strong>
                            <input type="text" value="<?php echo htmlspecialchars($product->getQty()); ?>" readonly>
                            <br>
                            <strong>Preço Unitário:</strong> R$
                            <?php echo number_format($product->getPrice(), 2, ',', '.'); ?>
                            <br>
                            <strong>Subtotal:</strong> R$
                            <?php echo number_format($product->getPrice() * $product->getQty(), 2, ',', '.'); ?>
                        </div>
                    </li>
                    <hr>
                <?php endforeach; ?>
                <li class="cart-total">
                    <strong>Total:</strong> R$ <?php echo number_format($cart->getTotal(), 2, ',', '.'); ?>
                </li>
            </ul>
        </div>
    <?php endif; ?>

</body>

</html>