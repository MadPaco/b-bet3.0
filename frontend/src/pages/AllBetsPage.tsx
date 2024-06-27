import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchBets, fetchSchedule } from '../utility/api';
import { useState, useEffect } from 'react';
import Accordion from '../components/common/Accordion';
import { Bet, Game } from '../utility/types';

const AllBetsPage: React.FC = () => {


  const [bets, setBets] = useState<Bet[]>([]);
  const [weekNumber, setWeekNumber] = useState<number>(1);
  const [games, setGames] = useState<Game[]>([]);
  const [openAccordion, setOpenAccordion] = useState<number | null>(null);
  const NFLWEEKS = 22;
  const weeks = [];
  for (let i = 1; i <= NFLWEEKS; i++) {
    weeks.push(<option value={i} key={i}>Week {i}</option>);
  }

  useEffect(() => {
    const getAllBets = async () => {
      const fetchedBets = await fetchBets(weekNumber);
      const data = await fetchedBets.json(); // Await the json method
      setBets(data);
    };

    getAllBets();
  }, [weekNumber]);

  useEffect(() => {
    const getGames = async () => {
      const fetchedGames = await fetchSchedule(weekNumber);
      const data = await fetchedGames.json();
      setGames(data);
    };

    getGames();
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
        return 'text-gray-500';
    }
  };

  return (
    <LoggedInLayout>
      <div className="flex flex-col lg:pt-10 lg:px-20">
        <div className="lg:col-span-1 lg:row-span-1 ">
          <div className='flex flex-col items-center mb-3 '>
            <h1 className="text-white text-2xl">All Bets</h1>
            <select
              className="bg-gray-600 text-white p-2 rounded-lg border-none focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-opacity-50"
              value={weekNumber || ''}
              onChange={(e) => setWeekNumber(Number(e.target.value))}
            >
              <option value="" disabled>Select a week</option>
              {weeks}
            </select>
          </div>

          {games.map((game: Game) => {
            const gameBets = bets.filter((bet: Bet) => bet.gameID === game.id);
            const title = (
              <div>
                <div className='flex items-center justify-between p-2'>
                  {/* Away team section */}
                  <div className='flex flex-col items-center w-1/3'>
                    <img
                      className="w-10 h-auto mb-1"
                      src={`/assets/images/teams/${game.awayTeamLogo}`}
                      alt={`${game.awayTeam} logo`}
                    />
                    <span className='text-center'>{game.awayTeam}</span>
                  </div>
                  {/* "at" text */}
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
                      <div className='mx-3'>
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
