import React from 'react';
import { Bar } from 'react-chartjs-2';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

interface LeaderboardEntry {
    username: string;
    totalPoints: number;
}

interface TotalPointsChartProps {
    leaderboard: LeaderboardEntry[];
}

const TotalPointsChart: React.FC<TotalPointsChartProps> = ({ leaderboard }) => {
    const totalPointsOptions = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top' as const,
                labels: {
                    color: '#E09F1F',
                },
            },
            title: {
                display: true,
                text: 'Total Points',
                color: '#E09F1F',
            },
        },
        scales: {
            x: {
                ticks: {
                    color: '#FFF7D6',
                },
                grid: {
                    color: 'rgba(255, 247, 214, 0.4)'
                },
            },
            y: {
                ticks: {
                    color: '#FFF7D6',
                },
                grid: {
                    color: 'rgba(255, 247, 214, 0.4)'
                },
            },
        },
    };

    const totalPointsData = {
        labels: leaderboard.map(user => user.username),
        datasets: [
            {
                label: 'Total Points',
                data: leaderboard.map(user => user.totalPoints),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
            },
        ],
    };

    return (
        <div className="bg-gray-900 p-4 rounded-lg shadow-lg border-2 border-highlightCream">
            <Bar
                options={totalPointsOptions}
                data={totalPointsData}
            />
        </div>
    );
}

export default TotalPointsChart;
