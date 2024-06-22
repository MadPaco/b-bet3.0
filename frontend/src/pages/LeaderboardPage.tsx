import { useState, useEffect } from 'react';
import Accordion from '../components/common/Accordion';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchAllUsers, fetchBets } from '../utility/api';
import { Bet } from '../utility/types';

const LeaderboardPage: React.FC = () => {
  const [openAccordion, setOpenAccordion] = useState<number | null>(null);
  const [leaderboardData, setLeaderboardData] = useState<Record<string, any>>({});
  const [users, setUsers] = useState<User[]>([]);
  const NFLWEEKS = 22;

  useEffect(() => {
    const fetchLeaderboardData = async () => {
      try {
        const users = await fetchAllUsers();
        setUsers(users);
        const leaderboard: Record<string, any> = {};

        for (const user of users) {
          leaderboard[user.username] = { Overall: 0 }; // Initialize overall points for the user

          for (let i = 1; i <= NFLWEEKS; i++) {
            try {
              const response = await fetchBets(i, user.username);
              const bets = await response.json();
              const points = calculatePoints(bets);

              // Store weekly points
              leaderboard[user.username][`Week ${i}`] = points;
              // Update overall points
              leaderboard[user.username]['Overall'] += points;
            } catch (error) {
              console.error(`Error fetching bets for user ${user.username} week ${i}:`, error);
            }
          }
        }

        setLeaderboardData(leaderboard);
      } catch (error) {
        console.error('Error fetching leaderboard data:', error);
      }
    };

    fetchLeaderboardData();
  }, []);

  const calculatePoints = (bets: Bet[]): number => {
    return bets.reduce((total, bet) => total + (bet.points || 0), 0); // Calculate total points
  };

  return (
    <LoggedInLayout>
      <div className="flex flex-col lg:pt-10 lg:px-10 items-center">
        <h1 className="text-center text-3xl font-bold mb-8 text-gray-100">Leaderboard Page</h1>

        <div className="mb-5 bg-gray-800 rounded-lg shadow-md w-full w-4/5 lg:w-2/5 mx-auto">
          <Accordion
            title="Overall Leaderboard"
            isOpen={openAccordion === -1}
            toggleAccordion={() => setOpenAccordion(prev => (prev === -1 ? null : -1))}
          >
            <div className="bg-gray-700 rounded-md p-4">
              {users.map((user) => (
                <div key={user.username} className="flex justify-between items-center p-2 bg-gray-900 hover:bg-gray-800 transition-colors duration-300">
                  <span className="font-medium text-gray-200">{user.username}</span>
                  <span className="text-gray-400">{leaderboardData[user.username]?.Overall || 0}</span>
                </div>
              ))}
            </div>
          </Accordion>
        </div>

        {Array.from({ length: NFLWEEKS }, (_, i) => i + 1).map((week) => (
          <div key={week} className="bg-gray-800 rounded-lg shadow-md w-full w-4/5 lg:w-2/5 mx-auto">
            <Accordion
              title={`Week ${week}`}
              isOpen={openAccordion === week}
              toggleAccordion={() => setOpenAccordion(prev => (prev === week ? null : week))}
            >
                {Object.keys(leaderboardData).map((username) => (
                  <div key={username} className="flex justify-between items-center p-2 bg-gray-900 hover:bg-gray-800 transition-colors duration-300">
                    <span className="font-medium text-gray-200">{username}</span>
                    <span className="text-gray-400">{leaderboardData[username][`Week ${week}`] || 0}</span>
                  </div>
                ))}
            </Accordion>
          </div>
        ))}
      </div>
    </LoggedInLayout>
  );
};

export default LeaderboardPage;
