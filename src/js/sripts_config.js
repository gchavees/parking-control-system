function carregarHistorico() {
    $.get('../php/api.php?action=historico_precos', function(data) {
        let html = '<ul class="list-group">';
        data.forEach(preco => {
            html += `
                <li class="list-group-item d-flex justify-content-between">
                    <span>${preco.data}</span>
                    <span>R$ ${preco.valor_hora}</span>
                </li>
            `;
        });
        html += '</ul>';
        $('#historicoPrecos').html(html);
    }, 'json');
}

function salvarPreco(e) {
    e.preventDefault();
    const valor = parseFloat($('#valorHora').val()).toFixed(2);

    if(valor <= 0) {
        alert('Valor inválido!');
        return;
    }

    $.post('../php/api.php', {
        action: 'atualizar_preco',
        valor_hora: valor
    }, function(data) {
        if(data.success) {
            $('#valorHora').val('');
            carregarHistorico();
            alert('Valor atualizado!');
        }
    }, 'json');
}

$(document).ready(function() {
    carregarHistorico();
});