import Chart from 'chart.js/auto';

export function createAcquisitionsChart(canvasElement, chartData) {

    new Chart(canvasElement, {
            type: 'bar',
            data: {
                labels: chartData.map(row => row.actividad),
                datasets: [
                    {
                        label: 'Reportes por semana',
                        data: chartData.map(row => row.cantidad_reportes)
                    },
                    {
                        label: 'Tiempo Promedio (minutos)', // Segundo conjunto de datos
                        data: chartData.map(row => row.promedio),
                        backgroundColor: 'rgba(153, 102, 255, 0.6)', // Color diferente para el segundo set
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'ACTIVIDAD'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'MINUTOS'
                        }
                    }
                }
            }
        }
    );
}

export function charSuma(canvasElement, chartData) {

    new Chart(canvasElement, {
            type: 'bar',
            data: {
                labels: chartData.map(row => row.trabajador),
                datasets: [
                    {
                        label: 'Minutos Reportados',
                        data: chartData.map(row => row.total_mins)
                    },
                    {
                        label: 'Faltante',
                        data: chartData.map(row => row.disponible)
                    },
                    {
                        label: 'Meta semanal',
                        data: chartData.map(row => row.minimo)
                    },
                ]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'TRABAJADORES'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'MINUTOS'
                        }
                    }
                }
            }
        }
    );
}
