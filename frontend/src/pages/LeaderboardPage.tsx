import { useState, useEffect } from 'react';
import Accordion from '../components/common/Accordion';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchAllUsers, fetchBets } from '../utility/api';
import { Bet } from '../utility/types';

const LeaderboardPage: React.FC = () => {
  const [openAccordion, setOpenAccordion] = useState<number | null>(null);
  const [leaderboardData, setLeaderboardData] = useState<Record<string, any>>({});
  const [users, setUsers] = useState<User[]>([]); // Add this line at the top of your component
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
      <div className="flex flex-col lg:pt-10 lg:grid lg:grid-cols-3 lg:grid-rows-3">
        <div className="lg:col-span-1 lg:row-span-1">
          <h1 className="text-center text-2xl font-bold mb-4">Leaderboard Page</h1>

          <Accordion
            title="Overall Leaderboard"
            isOpen={openAccordion === -1}
            toggleAccordion={() => setOpenAccordion(prev => (prev === -1 ? null : -1))}
          >
          <div>
            {users.map((user) => (
              <div key={user.username} className="flex justify-between">
                <span className="font-medium">{user.username}</span>
                <span>{leaderboardData[user.username]?.Overall || 0}</span>
              </div>
            ))}
          </div>
          </Accordion>

          {Array.from({ length: NFLWEEKS }, (_, i) => i + 1).map((week) => (
            <Accordion
              title={`Week ${week}`}
              key={week}
              isOpen={openAccordion === week}
              toggleAccordion={() => setOpenAccordion(prev => (prev === week ? null : week))}
            >
              <div className="bg-gray-900 rounded-md p-4 space-y-2">
                {Object.keys(leaderboardData).map((username) => (
                  <div key={username} className="flex justify-between">
                    <span className="font-medium mr-3">{username}</span>
                    <span>{leaderboardData[username][`Week ${week}`] || 0}</span>
                  </div>
                ))}
              </div>
            </Accordion>
          ))}
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default LeaderboardPage;
