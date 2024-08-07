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
              const bets = await fetchBets(i, user.username);
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
        const currentWeekData = await getCurrentWeek();
        // since currentWeek is the first week where no results have been set
        // we substract 1 to show the week which has points awarded
        // because this is what users will be interested in here
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
      <div className="flex flex-col p-2 items-center">
        <h1 className='my-3 text-highlightGold text-xl font-bold text-shadow-sm shadow-black'>Leaderboard</h1>

        <div className="mb-5 rounded-lg shadow-md w-full lg:w-2/5 mx-auto">
          <Accordion
            title="Total Points"
            isOpen={openAccordion === -1}
            toggleAccordion={() => setOpenAccordion(prev => (prev === -1 ? null : -1))}
          >
            <div className="p-4 rounded-xl">
              {users
                // Sort in descending order
                .slice()
                .sort((a, b) => {
                  const scoreA = leaderboardData[a.username]?.Overall || 0;
                  const scoreB = leaderboardData[b.username]?.Overall || 0;
                  return scoreB - scoreA;
                })
                .map((user) => (
                  <div key={user.username} className="flex w-full text-highlightCream justify-between items-center p-2transition-colors duration-400">
                    <span className={user.username === currentUser.username ? "text-highlightGold font-medium" : ""}>{user.username}</span>
                    <span className={user.username === currentUser.username ? "text-highlightGold" : ""}>{leaderboardData[user.username]?.Overall || 0}</span>
                  </div>
                ))}
            </div>
          </Accordion>
        </div>
        {Array.from({ length: NFLWEEKS }, (_, i) => i + 1).map((week) => (
          <div key={week} className="rounded-lg shadow-md w-full lg:w-2/5 mx-auto">
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
                  <div key={username} className="flex justify-between items-center p-2 text-highlightCream hover:bg-gray-800 transition-colors duration-300">
                    <span className={username === currentUser.username ? "text-highlightGold font-medium" : ""}>{username}</span>
                    <span className={username === currentUser.username ? "text-highlightGold" : ""}>{leaderboardData[username][`Week ${week}`] || 0}</span>
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
