import { useState, useEffect } from 'react';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchSchedule } from '../utility/api';
import { Game } from '../utility/types';
import { generateWeekOptions } from '../data/weekLabels';

const SchedulePage: React.FC = () => {
  const [schedule, setSchedule] = useState<Game[]>([]);
  const [weekNumber, setWeekNumber] = useState(1);

  useEffect(() => {
    const getSchedule = async () => {
      const response = await fetchSchedule(weekNumber);
      const data = await response.json();
      setSchedule(data);
    };

    getSchedule();
  }, [weekNumber]);

  const options = generateWeekOptions();

  return (
    <LoggedInLayout>
      <div className="flex flex-col items-center justify-center pt-2">
        <h1 className='my-3 text-highlightGold text-xl font-bold text-shadow-sm shadow-black'>Schedule</h1>
        <div className="text-white lg:w-1/3">
          <div className="flex items-center flex-col mb-4">
            <select
              className="bg-gray-900 text-white p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-highlightGold focus:ring-opacity-50 border-highlightCream border-solid border-2"
              value={weekNumber}
              onChange={(e) => setWeekNumber(Number(e.target.value))}
            >
              {options}
            </select>
          </div>

          {schedule.map((game, index) => (
            <div
              key={index}
              className='bg-gray-900 bg-opacity-90 flex flex-col items-center rounded-lg p-2 m-3 border-solid border-2 border-highlightCream'
            >
              {/* Start of date row */}
              <div className="time text-lg font-bold text-highlightGold">
                {new Intl.DateTimeFormat('en-GB', { weekday: 'short', year: 'numeric', month: 'long', day: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(game.date))}
              </div>

              {/* Start of teams row */}
              <div className="teams lg:text-lg flex items-center pt-1">
                <img
                  className="h-7 w-8 lg:h-10 lg:w-11 mr-1"
                  src={`/assets/images/teams/${game.awayTeamLogo}`}
                  alt={game.awayTeam}
                />
                <span className='text-center'>{game.awayTeam}</span>
                <span className="mx-2"> at </span>
                <span className='text-center'>{game.homeTeam}</span>
                <img
                  className="h-7 w-7 lg:h-10 lg:w-10 ml-1"
                  src={`/assets/images/teams/${game.homeTeamLogo}`}
                  alt={game.homeTeam}
                />
              </div>

              {/* Start of score row */}
              {game.homeScore !== null && game.awayScore !== null ?
                (
                  <div className="score text-lg font-bold">
                    {game.awayScore} - {game.homeScore}
                  </div>
                )
                : null}

              {/* Start of odds row */}
              <div className="odds mb-2 flex items-center text-sm">
                <span className="flex flex-col mr-3 items-center">
                  <span>away odds</span>
                  <span>({game.awayOdds})</span>
                </span>
                <span className="flex flex-col mx-3 items-center">
                  <span>over/under</span>
                  <span>({game.overUnder})</span>
                </span>{' '}
                <span className="flex flex-col ml-3 items-center">
                  <span>home odds</span>
                  <span>({game.homeOdds})</span>
                </span>
              </div>

              {/* Start of location row */}
              <div className="location">{game.location}</div>
            </div>
          ))}
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default SchedulePage;
