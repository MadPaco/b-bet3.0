import React from "react";
import { Scatter } from "react-chartjs-2";

interface HitRateVsPointAverageChartProps {
    teamHitRate: Record<string, number>;
    teamPointAverage: Record<string, number>;
    teamLogos: Record<string, string>;
}

const HitRateVsPointAverageChart: React.FC<HitRateVsPointAverageChartProps> = ({ teamHitRate, teamPointAverage, teamLogos }) => {
    const scatterData = {
        datasets: [
            {
                label: 'Team Hit Rate vs Point Average',
                data: Object.keys(teamHitRate).map((team) => ({
                    x: teamHitRate[team],
                    y: teamPointAverage[team],
                    label: team,
                    logo: `/assets/images/teams/${teamLogos[team]}`
                })),
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                pointRadius: 5,
            },
        ],
    };

    const scatterOptions = {
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Hit Rate'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Point Average'
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function (context) {
                        return `${context.raw.label}: (${context.raw.x}, ${context.raw.y})`;
                    }
                }
            },
            title: {
                display: true,
                text: 'Team Hit Rate vs Point Average'
            },
            legend: {
                display: false
            }
        },
        elements: {
            point: {
                pointStyle: (context) => {
                    const image = new Image();
                    image.src = context.raw.logo;
                    image.height = 20;
                    image.width = 20;
                    return image;
                },
                radius: 10,
            },
        },
    };
    return (
        <div className="bg-white p-4 rounded-lg shadow-lg">
            <Scatter
                data={scatterData}
                options={scatterOptions}
            />
        </div>
    );
}
export default HitRateVsPointAverageChart;