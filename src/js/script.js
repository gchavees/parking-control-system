let currentAction = '';

function showInput(action) {
    currentAction = action;
    $('#inputSection').show();
    $('#placaInput').val('').focus();
    if(action === 'saida'){
        $('#pagamentoSection').show();
        $('#placaInput').attr('placeholder', 'DIGITE A PLACA PARA SAÍDA');
    } else {
        $('#pagamentoSection').hide();
        $('#placaInput').attr('placeholder', 'DIGITE A PLACA');
    }
}

function cancelar() {
    $('#inputSection').hide();
    $('#placaInput').val('');
    currentAction = '';
}

function processarAcao() {
    const placa = $('#placaInput').val().trim().toUpperCase();
    
    if(!placa) {
        alert('Digite a placa!');
        return;
    }

    if(currentAction === 'entrada') {
        registrarEntrada(placa);
    } else if(currentAction === 'saida') {
        // Obtém a forma de pagamento
        const pagamento = $('#pagamento').val();
        registrarSaida(placa, pagamento);
    }
}

function registrarEntrada(placa) {
    $.post('src/php/api.php', { action: 'entrada', placa: placa }, function(data) {
        if(data.success) {
            carregarVeiculosEstacionados(); // Atualiza a lista
            // Exibe comprovante de entrada (pode personalizar se necessário)
            alert(data.message);
            resetInterface();
        } else {
            alert(data.message);
        }
    }, 'json');
}

function registrarSaida(placa, pagamento) {
    $.post('src/php/api.php', { action: 'saida', placa: placa, pagamento: pagamento }, function(data) {
        if(data.success) {
            carregarVeiculosEstacionados(); // Atualiza a lista
            // Exibe o comprovante minimalista para saída
            showTicket(data);
            resetInterface();
        } else {
            alert(data.message);
        }
    }, 'json');
}

function showTicket(info) {
    // Layout minimalista para impressão do comprovante de Saída
    const content = `
    <div style="font-size:12px; text-align:center; margin:0; padding:0;">
        <h2>COMPROVANTE</h2>
        <p>Saída</p>
        <p>Placa: ${info.placa}</p>
        <p>Data/Hora: ${info.data_saida}</p>
        <p>Entrada: ${info.data_entrada}</p>
        <p>Valor: R$ ${info.valor}</p>
        <p>Pagamento: ${info.pagamento}</p>
    </div>
    `;
    // Abre nova janela contendo somente o comprovante para impressão
    const printWindow = window.open('', '', 'height=400,width=300');
    printWindow.document.write('<html><head><title>Comprovante</title>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(content);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

function resetInterface() {
    $('#placaInput').val('');
    $('#inputSection').hide();
    currentAction = '';
}

// Adicione estas funções
function toggleList() {
    const lista = $('#listaEstacionados');
    lista.toggle();
    if (lista.is(':visible')) {
        carregarVeiculosEstacionados();
    }
}

function carregarVeiculosEstacionados() {
    $.post('src/php/api.php', { action: 'listar_estacionados' }, function(data) {
        if(data.success) {
            let html = '';
            data.veiculos.forEach(veiculo => {
                html += `
                    <div class="veiculo-item">
                        <div class="veiculo-info">
                            <span class="placa-badge">${veiculo.placa}</span>
                            <span>Entrou em: ${veiculo.data_entrada}</span>
                        </div>
                    </div>
                `;
            });
            $('#listaEstacionados').html(html);
            $('#contador').text(data.veiculos.length);
        }
    }, 'json');
}

// Carrega a lista ao iniciar
$(document).ready(function() {
    carregarVeiculosEstacionados();
});