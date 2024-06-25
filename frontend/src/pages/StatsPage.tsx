import React, { useEffect, useState } from 'react';
import { Bar } from 'react-chartjs-2';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchUserStats } from '../utility/api';
import { useAuth } from '../components/auth/AuthContext';

// Registering necessary components for Chart.js
ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

const StatsPage: React.FC = () => {
  const { username } = useAuth();
  const [stats, setStats] = useState<any>(null);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const response = await fetchUserStats(username);
        const data = await response.json();
        setStats(data);
      } catch (error) {
        console.error('Failed to fetch stats:', error);
      }
    };

    fetchStats();
  }, [username]);

  const pointsPerWeekData = stats ? {
    labels: Object.keys(stats.points_per_week),
    datasets: [
      {
        label: 'Points Per Week',
        data: Object.values(stats.points_per_week),
        borderColor: 'rgba(75, 192, 192, 1)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderWidth: 2,
      },
    ],
  } : null;

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
    <LoggedInLayout>
      <div className="flex flex-col lg:pt-10 lg:grid lg:grid-cols-3 lg:grid-rows-3 text-white">
        <div className="lg:col-span-1 lg:row-span-1 text-center">
          <h1>Stats of {username}</h1>
          {stats && (
            <div>
              <p className='mt-3'>Bets Placed: {stats.bets_placed}</p>
              <br></br>
              <div className="bg-white p-4 rounded-lg shadow-lg">
                {pointsPerWeekData ? (
                  <Bar
                    options={pointsPerWeekOptions}
                    data={pointsPerWeekData}
                  />
                ) : (
                  'Loading...'
                )}
              </div>
            </div>
          )}
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default StatsPage;
