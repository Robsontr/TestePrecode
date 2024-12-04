<?php
require_once 'C:\xampp\htdocs\PHPtestes\BD\pgAdmin.php';

try {
    // Configurar PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se um ID foi passado na URL
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        die("ID do produto não informado!");
    }

    $productId = $_GET['id'];

    // Consultar o produto com o ID
    $sql = "SELECT * FROM products WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Produto não encontrado!");
    }

    // Consultar a variação associada ao produto pelo `product_id`
    $sqlV = "SELECT * FROM variations WHERE product_id = :product_id";
    $stmtV = $pdo->prepare($sqlV);
    $stmtV->execute([':product_id' => $productId]);
    $variations = $stmtV->fetch(PDO::FETCH_ASSOC);

    if (!$variations) {
        die("Variação não encontrada para este produto!");
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="style.css">

    <script>
        // Função de validação do preço
        function validarPreco() {
            // Preço atual do produto
            var currentPrice = <?php echo $product['price']; ?>;
            var d = (currentPrice * 0.500) + currentPrice; // Preço máximo permitido
            var d2 = (currentPrice * 0.700); // Preço mínimo permitido

            // Obter o novo preço inserido pelo usuário
            var novoPreco = parseFloat(document.getElementById("price").value);

            // Verificar se o novo preço está dentro do intervalo permitido
            if (novoPreco > d || novoPreco < d2) {
                // Exibir o alerta com a mensagem de erro
                alert("O novo preço precisa estar entre " + d2.toFixed(2) + " e " + d.toFixed(2) + ".");
                return false; // Impede o envio do formulário
            }
            alert("sucesso");
            return true; // Permite o envio do formulário


        }
    </script>

</head>

<body>

    <h1>Editar Produto</h1>
    <a href="recuperaDados.php" class="voltar">Voltar</a>
    <form action="updateProduct.php" method="POST" onsubmit="return validarPreco()">
        <!-- Campo oculto para o ID do produto -->
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
        <input type="hidden" id="sku" name="sku" value="<?php echo htmlspecialchars($variations['sku']); ?>"><br>

        <label for="name">Nome:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" disabled><br>

        <label for="sku">SKU:</label>
        <input type="number" id="sku" name="sku" value="<?php echo htmlspecialchars($variations['sku']); ?>" disabled><br>

        <!-- Campo para o preço -->
        <label for="price">Preço:</label>
        <input type="decimal" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>"><br>

        <!-- Campo para o status -->
        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="enabled" <?php echo $product['status'] === 'enabled' ? 'selected' : ''; ?>>Enabled</option>
            <option value="disabled" <?php echo $product['status'] === 'disabled' ? 'selected' : ''; ?>>Disabled</option>
        </select>
        <br><br>

        <!-- Campo para a quantidade -->
        <label for="qty">Quantidade:</label>
        <input type="number" id="qty" name="qty" value="<?php echo htmlspecialchars($variations['qty']); ?>"><br>

        <!-- Botão para salvar alterações -->
        <button type="submit">Salvar Alterações</button>
    </form>
</body>

</html>