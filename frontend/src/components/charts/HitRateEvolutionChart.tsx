import { Bar } from 'react-chartjs-2';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, LineElement, PointElement } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, LineElement, PointElement, Title, Tooltip, Legend);

interface HitRateEvolutionChartProps {
    hitRateEvolution: Record<string, number>;
    hitRatePerWeek: Record<string, number>;
}

const HitRateEvolutionChart: React.FC<HitRateEvolutionChartProps> = ({ hitRateEvolution, hitRatePerWeek }) => {

    const hitRateEvolutionData = {
        labels: Object.keys(hitRatePerWeek),
        datasets: [
            {
                label: 'HitRate Per Week',
                data: Object.values(hitRatePerWeek),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
            },
            {
                label: 'Average HitRate Per Week',
                data: Object.values(hitRateEvolution),
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderWidth: 2,
                type: 'line' as const,
            }
        ],
    };

    const hitRateEvolutionOptions = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top' as const,
            },
            title: {
                display: true,
                text: 'HitRate per week',
            },
        },
    };
    return (
        <div className="bg-white p-4 rounded-lg shadow-lg">
            <Bar
                options={hitRateEvolutionOptions}
                data={hitRateEvolutionData}
            />
        </div>
    );

}
export default HitRateEvolutionChart;