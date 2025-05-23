fetch('../js/datos.php')
    .then(Response => Response.json())
    .then(data => inciarGrafico(data))

function inciarGrafico(){
    document.body.classList.add('running')
    grafico1()
    grafico2()
    grafico3()
}

function grafico1(){

    const data = {
        labels: ['uno', 'dos', 'tres', 'cuatro'],
        datasets: [{
            data:[6,22,9,18]
        }]
    }
    new Chart(document.getElementById('chart1'), {type: 'bar', data})
}

function grafico2(){

    const data = {
        labels: ['uno', 'dos', 'tres', 'cuatro'],
        datasets: [{
            data:[6,22,9,18]
        }]
    }
    new Chart(document.getElementById('chart2'), {type: 'pie', data})
}

function grafico3(){

    const data = {
        labels: ['uno', 'dos', 'tres', 'cuatro'],
        datasets: [{
            data:[6,22,9,18]
        }]
    }
    new Chart(document.getElementById('chart3'), {type: 'polarArea', data})
}