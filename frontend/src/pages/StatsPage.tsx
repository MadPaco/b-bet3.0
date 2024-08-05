import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchUserStats, fetchLeadboard, fetchAllTeamLogos } from '../utility/api';
import PointsPerWeekChart from '../components/charts/PointsPerWeekChart';
import TotalPointsChart from '../components/charts/TotalPointsChart';
import PointDistributionChart from '../components/charts/PointDistributionChart';
import TeamHitRateVsTeamAverageChart from '../components/charts/HitRateVsPointAverageChart';
import LeaderboardEvolutionChart from '../components/charts/LeaderboardEvolutionChart';
import HitRateEvolutionChart from '../components/charts/HitRateEvolutionChart';

const StatsPage: React.FC = () => {
  const { username } = useParams<{ username: string }>();
  const [stats, setStats] = useState<any>(null);
  const [leaderboard, setLeaderboard] = useState<any>(null);
  const [teamLogos, setTeamLogos] = useState<any>(null);

  useEffect(() => {
    const fetchTeamLogos = async () => {
      try {
        const response = await fetchAllTeamLogos();
        const data = await response.json();
        setTeamLogos(data);
      } catch (error) {
        console.error('Failed to fetch team logos:', error);
      }
    };

    fetchTeamLogos();
  }, []);

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

  useEffect(() => {
    const fetchUserStandings = async () => {
      try {
        const response = await fetchLeadboard();
        const data = await response.json();
        setLeaderboard(data);
      } catch (error) {
        console.error('Failed to fetch leaderboard:', error);
      }
    }
    fetchUserStandings();
  }, []);

  return (
    <LoggedInLayout>
      <div className="flex flex-col text-white items-center w-full">
        <div className="text-center w-full max-w-6xl">
          <h1 className='my-3 text-highlightGold text-xl font-bold text-shadow-sm shadow-black'>Stats of {username}</h1>
          {stats && (
            <div className='w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6'>
              <div className="bg-gray-900 p-6 rounded-lg shadow-lg col-span-1 md:col-span-2 flex justify-evenly text-highlightCream">
                <p className='m-3'>Bets Placed: {stats.betsPlaced}</p>
                <p className='m-3'>Total points: {stats.totalPoints}</p>
                <p className='m-3'>Current place: {stats.currentPlace}</p>
                <p className='m-3'>Best week: {stats.highestScoringWeek}</p>
                <p className='m-3'>Worst week: {stats.lowestScoringWeek}</p>
                <p className='m-3'>Hit-rate: {(stats.hitRate * 100).toFixed(2)} %</p>
              </div>

              <div className="bg-gray-800 p-6 rounded-lg shadow-lg">
                {stats ? (
                  <PointsPerWeekChart
                    pointsPerWeek={stats.pointsPerWeek}
                    averagePointsPerWeek={stats.averagePointsPerWeek}
                  />
                ) : 'Loading...'}
              </div>

              <div className="bg-gray-800 p-6 rounded-lg shadow-lg">
                {leaderboard ? (
                  <TotalPointsChart leaderboard={leaderboard} />
                ) : 'Loading...'}
              </div>

              <div className="bg-gray-800 p-6 rounded-lg shadow-lg flex items-center justify-center">
                {stats.pointDistribution ? (
                  <PointDistributionChart
                    pointDistribution={stats.pointDistribution} />
                ) : 'Loading...'}
              </div>


              <div className="bg-gray-800 p-6 align-center items-center rounded-lg shadow-lg ">
                {stats.hitRateEvolution ? (
                  <div className='mb-3'>
                    <HitRateEvolutionChart
                      hitRateEvolution={stats.hitRateEvolution}
                      hitRatePerWeek={stats.hitRatePerWeek}
                    />
                  </div>) : 'Loading...'}

                {stats.leaderboardEvolution ? (
                  <div>
                    <LeaderboardEvolutionChart
                      leaderboardEvolution={stats.leaderboardEvolution} />
                  </div>) : 'Loading...'}


              </div>

              <div className="bg-gray-800 p-6 rounded-lg shadow-lg col-span-1 md:col-span-2">
                {stats.teamHitRate && stats.teamPointAverage && teamLogos ? (
                  <TeamHitRateVsTeamAverageChart
                    teamHitRate={stats.teamHitRate}
                    teamPointAverage={stats.teamPointAverage}
                    teamLogos={teamLogos}
                  />
                ) : 'Loading...'}
              </div>
            </div>
          )}
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default StatsPage;
