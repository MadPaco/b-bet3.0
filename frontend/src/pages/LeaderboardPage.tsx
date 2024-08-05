import { useState, useEffect } from 'react';
import Accordion from '../components/common/Accordion';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchAllUsers, fetchBets, getCurrentWeek } from '../utility/api';
import { Bet, User } from '../utility/types';
import { useAuth } from '../components/auth/AuthContext';

const LeaderboardPage: React.FC = () => {
  const [openAccordion, setOpenAccordion] = useState<number | null>(null);
  const [leaderboardData, setLeaderboardData] = useState<Record<string, any>>({});
  const [users, setUsers] = useState<User[]>([]);
  const [currentWeek, setCurrentWeek] = useState<number | null>(null);
  const NFLWEEKS = 22;
  const currentUser = useAuth();


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

  useEffect(() => {
    const getCurrentWeekData = async () => {
      try {
        const response = await getCurrentWeek();
        const currentWeekData = await response.json();
        setCurrentWeek(currentWeekData.currentWeek - 1);
      } catch (error) {
        console.error('Error fetching current week:', error);
      }
    };
    getCurrentWeekData();
  }, []);
  const calculatePoints = (bets: Bet[]): number => {
    return bets.reduce((total, bet) => total + (bet.points || 0), 0); // Calculate total points
  };

  return (
    <LoggedInLayout>
      <div className="flex flex-col lg:pt-10 lg:px-10 items-center">
        <h1 className="text-center text-3xl font-bold mb-8 text-gray-100">Leaderboard Page</h1>

        <div className="mb-5 bg-gray-800 rounded-lg shadow-md w-full lg:w-2/5 mx-auto">
          <Accordion
            title="Overall Leaderboard"
            isOpen={openAccordion === -1}
            toggleAccordion={() => setOpenAccordion(prev => (prev === -1 ? null : -1))}
          >
            <div className="bg-gray-700 rounded-md p-4">
              {users
                // Sort in descending order
                .slice()
                .sort((a, b) => {
                  const scoreA = leaderboardData[a.username]?.Overall || 0;
                  const scoreB = leaderboardData[b.username]?.Overall || 0;
                  return scoreB - scoreA;
                })
                .map((user) => (
                  <div key={user.username} className="flex justify-between items-center p-2 bg-gray-900 hover:bg-gray-800 transition-colors duration-400">
                    <span className={user.username === currentUser.username ? "text-highlightGold font-medium" : "text-gray-400 font-medium"}>{user.username}</span>
                    <span className={user.username === currentUser.username ? "text-highlightGold" : "text-gray-400"}>{leaderboardData[user.username]?.Overall || 0}</span>
                  </div>
                ))}
            </div>
          </Accordion>
        </div>
        {Array.from({ length: NFLWEEKS }, (_, i) => i + 1).map((week) => (
          <div key={week} className="bg-gray-800 rounded-lg shadow-md w-full lg:w-2/5 mx-auto">
            <Accordion
              title={`Week ${week}`}
              isOpen={openAccordion === week}
              toggleAccordion={() => setOpenAccordion(prev => (prev === week ? null : week))}
              currentWeek={currentWeek === week ? true : false}
            >
              {Object.keys(leaderboardData)
                // Sort usernames based on weekly points in descending order
                .slice()
                .sort((a, b) => {
                  const scoreA = leaderboardData[a][`Week ${week}`] || 0;
                  const scoreB = leaderboardData[b][`Week ${week}`] || 0;
                  return scoreB - scoreA;
                })
                .map((username) => (
                  <div key={username} className="flex justify-between items-center p-2 bg-gray-900 hover:bg-gray-800 transition-colors duration-300">
                    <span className={username === currentUser.username ? "text-highlightGold font-medium" : "text-gray-400 font-medium"}>{username}</span>
                    <span className={username === currentUser.username ? "text-highlightGold" : "text-gray-400"}>{leaderboardData[username][`Week ${week}`] || 0}</span>
                  </div>
                ))}
            </Accordion>
          </div>
        ))}
      </div >
    </LoggedInLayout >
  );
};

export default LeaderboardPage;
