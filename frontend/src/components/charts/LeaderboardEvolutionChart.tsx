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
            },
            title: {
                display: true,
                text: 'Rankings Over Time',
            },
        },
        scales: {
            y: {
                beginAtZero: false,
                reverse: true, // Reverse the y-axis to show rank 1 at the top
                ticks: {
                    stepSize: 1,
                },
            },
        },
    };

    return (
        <div className='bg-white'>
            <Line
                options={leaderboardEvolutionOptions}
                data={leaderboardEvolutionData}
            />
        </div>
    );
}

export default LeaderboardEvolutionChart;
