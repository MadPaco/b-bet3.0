import React from 'react';
import { Line } from 'react-chartjs-2';
import { Chart as ChartJS, CategoryScale, LinearScale, LineElement, PointElement, Title, Tooltip, Legend } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, LineElement, PointElement, Title, Tooltip, Legend);

interface LeaderboardEntry {
    username: string;
    totalPoints: number;
}

interface LeaderboardEvolutionChartProps {
    leaderboardEvolution: Record<string, LeaderboardEntry[]>;
}

const LeaderboardEvolutionChart: React.FC<LeaderboardEvolutionChartProps> = ({ leaderboardEvolution }) => {
    // Extract the list of weeks
    const weeks = Object.keys(leaderboardEvolution);

    // Create a mapping of usernames to their ranks over time
    const userRanks: Record<string, number[]> = {};

    weeks.forEach(week => {
        leaderboardEvolution[week].forEach((entry, index) => {
            if (!userRanks[entry.username]) {
                userRanks[entry.username] = [];
            }
            // The rank is the index + 1
            userRanks[entry.username].push(index + 1);
        });
    });

    // Prepare datasets for the chart
    const datasets = Object.keys(userRanks).map(username => ({
        label: username,
        data: userRanks[username],
        borderColor: `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`,
        backgroundColor: `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.2)`,
        borderWidth: 2,
        fill: false,
    }));

    const leaderboardEvolutionData = {
        labels: weeks.map(week => `Week ${week}`),
        datasets: datasets,
    };

    const leaderboardEvolutionOptions = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top' as const,
                labels: {
                    color: '#FFF7D6',
                },
            },
            title: {
                display: true,
                text: 'Rankings Over Time',
                color: '#E09F1F',
            },
        },
        scales: {
            y: {
                beginAtZero: false,
                reverse: true, // Reverse the y-axis to show rank 1 at the top
                ticks: {
                    stepSize: 1,
                    color: '#FFF7D6',
                },
                grid: {
                    color: 'rgba(255, 247, 214, 0.4)'
                }
            },
            x: {
                ticks: {
                    color: '#FFF7D6',
                },
                grid: {
                    color: 'rgba(255, 247, 214, 0.4)'
                }

            }
        },
    };

    return (
        <div className='bg-gray-900 m-2 p-4 rounded-lg shadow-lg border-2 border-highlightCream'>
            <Line
                options={leaderboardEvolutionOptions}
                data={leaderboardEvolutionData}
            />
        </div>
    );
}

export default LeaderboardEvolutionChart;
