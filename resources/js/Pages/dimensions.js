import Chart from 'chart.js/auto';

export function createAcquisitionsChart(canvasElement, chartData) {

  new Chart(
    canvasElement,
    {
      type: 'bar',
      data: {
        labels: chartData.map(row => row.actividad),
        datasets: [
          {
            label: 'Semanas',
            data: chartData.map(row => row.semana)
          },
          {
            label: 'Promedio (minutos)', // Segundo conjunto de datos
            data: chartData.map(row => row.promedio), 
            backgroundColor: 'rgba(153, 102, 255, 0.6)', // Color diferente para el segundo set
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 1
          }
        ]
      }
    }
  );
}
