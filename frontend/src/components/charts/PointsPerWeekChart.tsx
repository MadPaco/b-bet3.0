import { Bar } from 'react-chartjs-2';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, LineElement, PointElement } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, LineElement, PointElement, Title, Tooltip, Legend);

interface PointsPerWeekChartProps {
    pointsPerWeek: Record<string, number>;
    averagePointsPerWeek: Record<string, number>;
}

const PointsPerWeekChart: React.FC<PointsPerWeekChartProps> = ({ pointsPerWeek, averagePointsPerWeek }) => {

    const pointsPerWeekData = {
        labels: Object.keys(pointsPerWeek),
        datasets: [
            {
                label: 'Points Per Week',
                data: Object.values(pointsPerWeek),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
            },
            {
                label: 'Average Points Per Week',
                data: Object.values(averagePointsPerWeek),
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderWidth: 2,
                type: 'line' as const,
            }
        ],
    };

    const pointsPerWeekOptions = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top' as const,
            },
            title: {
                display: true,
                text: 'Points Per Week',
            },
        },
    };
    return (
        <div className="bg-white p-4 rounded-lg shadow-lg">
            <Bar
                options={pointsPerWeekOptions}
                data={pointsPerWeekData}
            />
        </div>
    );

}
export default PointsPerWeekChart;