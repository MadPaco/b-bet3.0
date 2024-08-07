import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchBets, fetchSchedule, getCurrentWeek as fetchCurrentWeek } from '../utility/api';
import { useState, useEffect } from 'react';
import Accordion from '../components/common/Accordion';
import { Bet, Game } from '../utility/types';
import { generateWeekOptions } from '../data/weekLabels';

const AllBetsPage: React.FC = () => {
  const [bets, setBets] = useState<Bet[]>([]);
  const [weekNumber, setWeekNumber] = useState<number | null>(null);
  const [games, setGames] = useState<Game[]>([]);
  const [openAccordion, setOpenAccordion] = useState<number | null>(null);
  const weeks = generateWeekOptions();

  useEffect(() => {
    const getCurrentWeek = async () => {
      const response = await fetchCurrentWeek();
      setWeekNumber(response.currentWeek);
    };

    getCurrentWeek();
  }, []);

  useEffect(() => {
    const getAllBets = async (week: number) => {
      const data = await fetchBets(week);
      setBets(data);
    };

    if (weekNumber !== null) {
      getAllBets(weekNumber);
    }
  }, [weekNumber]);

  useEffect(() => {
    const getGames = async (week: number) => {
      const data = await fetchSchedule(week);
      setGames(data);
    };

    if (weekNumber !== null) {
      getGames(weekNumber);
    }
  }, [weekNumber]);

  const getColorClass = (points: number) => {
    switch (points) {
      case 5:
        return 'text-red-500';
      case 3:
        return 'text-yellow-500';
      case 1:
        return 'text-green-500';
      default:
        return 'text-highlightCream';
    }
  };

  return (
    <LoggedInLayout>
      <div className="flex flex-col p-2 text-white items-center justify-center min-h-screen">
        <div className='flex flex-col items-center mb-3 w-full lg:w-1/3'>
          <h1 className='my-3 text-highlightGold text-xl font-bold text-shadow-sm shadow-black'>All Bets</h1>
          <select
            className="bg-gray-900 text-white p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-highlightGold focus:ring-opacity-50 border-highlightCream border-solid border-2"
            value={weekNumber || 1}
            onChange={(e) => setWeekNumber(Number(e.target.value))}
          >
            {weeks}
          </select>
        </div>
        <div className='w-full lg:w-1/3 border-highlightCream'>
          {games.map((game: Game) => {
            const gameBets = bets.filter((bet: Bet) => bet.gameID === game.id);
            const title = (
              <div>
                <div className='flex items-center justify-between p-2 '>
                  {/* Away team section */}
                  <div className='flex flex-col items-center w-1/3'>
                    <img
                      className="w-10 h-auto mb-1"
                      src={`/assets/images/teams/${game.awayTeamLogo}`}
                      alt={`${game.awayTeam} logo`}
                    />
                    <span className='text-center'>{game.awayTeam}</span>
                  </div>
                  <div className='w-1/3 text-center'>
                    at
                  </div>
                  {/* Home team section */}
                  <div className='flex flex-col items-center w-1/3'>
                    <img
                      className="w-10 h-auto mb-1"
                      src={`/assets/images/teams/${game.homeTeamLogo}`}
                      alt={`${game.homeTeam} logo`}
                    />
                    <span className='text-center'>{game.homeTeam}</span>
                  </div>
                </div>
                <div>
                  {game.homeScore !== null && game.awayScore !== null ?
                    <div className='flex justify-center'>
                      <span className='text-white'>{game.awayScore} - {game.homeScore}</span>
                    </div>
                    : <div className='flex justify-center'>
                      <span className='text-white'>No score yet</span>
                    </div>
                  }
                </div>
              </div>
            );

            return (
              <div key={game.id} className="max-w-lg mx-auto">
                <Accordion
                  title={title}
                  isOpen={openAccordion === game.id}
                  toggleAccordion={() => setOpenAccordion(prev => prev === game.id ? null : game.id)}
                >
                  {gameBets.map((bet: Bet) => (
                    <div key={bet.id} className="p-2 flex space-between">
                      <div className='mx-3 text-highlightCream'>
                        {bet.username}:
                      </div>
                      {game.homeScore !== null && game.awayScore !== null ?
                        <div className={getColorClass(bet.points)}>
                          {bet.awayPrediction} - {bet.homePrediction} ({bet.points} points)
                        </div>
                        :
                        <div className='text-gray-500'>
                          {bet.awayPrediction} - {bet.homePrediction}
                        </div>
                      }
                    </div>
                  ))}
                </Accordion>
              </div>
            );
          })}
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default AllBetsPage;
