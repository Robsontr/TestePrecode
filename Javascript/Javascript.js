function enviarFormularios() {
    const form = document.getElementById('Products');

    // Variável para armazenar mensagens de erro
    let mensagensErro = [];

    // Verificar se todos os campos required estão preenchidos
    const camposObrigatorios = form.querySelectorAll("[required]");
    for (let i = 0; i < camposObrigatorios.length; i++) {
        if (camposObrigatorios[i].value === "") {
            // Usar o atributo "name" ou "id" do campo para identificar o campo que falta
            const nomeCampo = camposObrigatorios[i].name || camposObrigatorios[i].id;
            mensagensErro.push(`O campo "${nomeCampo}" é obrigatório.`);
        }
    }

    // Se houver mensagens de erro, exibe-as e impede o envio
    if (mensagensErro.length > 0) {
        alert(mensagensErro.join("\n"));
        return;  // Não envia o formulário
    }

    // Criar FormData
    const products = new FormData(form);

    // Enviar via fetch
    fetch('../controllers/Controller.php', {
        method: 'POST',
        body: products
    })
    .then(response => response.text())
    .then(result => {
        alert(result);  // Exibe a resposta do servidor
        if (result.includes('sucesso')) {  // Verifica se o servidor retornou 'sucesso' ou algo similar
            Produtos();  // Redireciona para a página de produtos
        } else {
            alert('Erro ao enviar os dados do produto.');
        }
    })
    .catch(error => {
        console.error('Erro no envio:', error);
        alert('Houve um erro ao enviar o formulário.');
    });
}

function Produtos() {
    // Redireciona para a página de produtos
    window.location.href = '../products/recuperaDados.php';
}
