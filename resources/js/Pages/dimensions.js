import Chart from 'chart.js/auto';

export function createAcquisitionsChart(canvasElement, chartData) {

    return new Chart(canvasElement, {
            type: 'bar',
            data: {
                labels: chartData.map(row => row.actividad),
                datasets: [
                    {
                        label: 'Estimado',
                        data: chartData.map(row => row.Estimado)
                    },
                    {
                        label: 'Tiempo Promedio (Horas)', // Segundo conjunto de datos
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
                            text: 'Horas'
                        }
                    }
                }
            }
        }
    );
}

export function createAcquisitionsChart3(canvasElement, chartData) {

    return new Chart(canvasElement, {
            type: 'bar',
            data: {
                labels: chartData.map(row => row.actividad),
                datasets: [
                    {
                        label: 'Estimado',
                        data: chartData.map(row => row.Estimado)
                    },
                    {
                        label: 'Tiempo Promedio (Horas)', // Segundo conjunto de datos
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
                            text: 'Horas'
                        }
                    }
                }
            }
        }
    );
}

export function createAcquisitionsChart4(canvasElement, chartData) {

    const dataValues = [
        Number(chartData.reportes ?? 0),
        Number(chartData.reprocesos ?? 0),
        Number(chartData.paros ?? 0),
    ];

    return new Chart(canvasElement, {
        type: 'pie',
        data: {
            labels: ['Reportes', 'Reprocesos', 'Paros'],
            datasets: [{
                label: 'Fracción de paros y reprocesos',
                data: dataValues,
                backgroundColor: [
                    '#4bc0c0',
                    '#ffcd56',
                    '#ff6384'
                ],
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: {
                    display: true,
                    text: 'Análisis de Operaciones'
                }
            }
        }
    });
}


export function charSuma(canvasElement, chartData) {

    return new Chart(canvasElement, {
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

//peores trabajadores 
export function charSuma2(canvasElement, chartData) {

    return new Chart(canvasElement, {
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
