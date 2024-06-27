import { Pie } from "react-chartjs-2";
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, LineElement, PointElement, ArcElement } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, LineElement, PointElement, ArcElement, Title, Tooltip, Legend);

interface PointDistributionChartProps {
    pointDistribution: Record<number, number>;
}

const PointDistributionChart: React.FC<PointDistributionChartProps> = ({ pointDistribution }) => {



    const pointDistributionData = {
        labels: Object.keys(pointDistribution),
        datasets: [
            {
                label: 'Point Distribution',
                data: Object.values(pointDistribution),
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                ],
                borderWidth: 1,
            },
        ],
    };

    const pointDistributionOptions = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top' as const,
                labels: {
                    color: 'white',
                },
            },
            title: {
                display: true,
                text: 'Point Distribution',
                color: 'white',
            },
        },
    };

    return (
        <div className='bg-gray-500'>
            <Pie
                options={pointDistributionOptions}
                data={pointDistributionData}
            />
        </div>
    );

}
export default PointDistributionChart;
