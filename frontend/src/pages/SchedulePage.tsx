import { useState, useEffect } from 'react';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchSchedule, getCurrentWeek as fetchCurrentWeek } from '../utility/api';
import { Game } from '../utility/types';
import { generateWeekOptions } from '../data/weekLabels';

const SchedulePage: React.FC = () => {
  const [schedule, setSchedule] = useState<Game[]>([]);
  const [weekNumber, setWeekNumber] = useState<number | null>(null); // Initialize as null to detect loading state
  const [isLoading, setIsLoading] = useState(true); // Loading state for schedule

  useEffect(() => {
    const getCurrentWeek = async () => {
      try {
        const currentWeekData = await fetchCurrentWeek();
        setWeekNumber(currentWeekData.currentWeek); // Set the week number to the current week
      } catch (error) {
        console.error('Error fetching current week:', error);
      }
    };

    getCurrentWeek();
  }, []);

  useEffect(() => {

    const getSchedule = async (week: number) => {
      try {
        setIsLoading(true); // Set loading state before fetching data
        const scheduleData = await fetchSchedule(week);
        setSchedule(scheduleData);
      } catch (error) {
        console.error('Error fetching schedule:', error);
      } finally {
        setIsLoading(false); // Clear loading state after fetching data
      }
    };

    if (weekNumber !== null) { // Only fetch schedule if the week number is set
      getSchedule(weekNumber);
    }
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
              value={weekNumber || 1}
              onChange={(e) => setWeekNumber(Number(e.target.value))}
            >
              {options}
            </select>
          </div>

          {isLoading ? (
            <p>Loading schedule...</p>
          ) : (
            schedule.map((game, index) => (
              <div
                key={index}
                className='bg-gray-900 bg-opacity-90 flex items-center rounded-lg p-1 lg:p-2 m-3 border-solid border-2 border-highlightCream'
              >
                <div className='w-1/5 lg:p-4 m-3'>
                  <img
                    className="mr-1"
                    src={`/assets/images/teams/${game.awayTeamLogo}`}
                    alt={game.awayTeam}
                  />
                </div>
                <div className='w-3/5 text-center'>
                  <div className="time text-lg font-bold text-highlightGold">
                    {new Intl.DateTimeFormat('en-GB', { weekday: 'short', year: 'numeric', month: 'long', day: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(game.date))}
                  </div>
                  <div className="teams lg:text-lg flex items-center pt-1">
                    <span className='text-center w-1/3'>{game.awayTeam}</span>
                    <span className="mx-2 w-1/3"> at </span>
                    <span className='text-center w-1/3'>{game.homeTeam}</span>
                  </div>
                  {game.homeScore !== null && game.awayScore !== null ? (
                    <div className="score text-lg font-bold">
                      {game.awayScore} - {game.homeScore}
                    </div>
                  ) : null}
                  <div className="odds mb-2 flex items-center justify-center text-sm">
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
                  <div className="location">{game.location}</div>
                </div>
                <div className='w-1/5 lg:p-4 m-3'>
                  <img
                    className="ml-1"
                    src={`/assets/images/teams/${game.homeTeamLogo}`}
                    alt={game.homeTeam}
                  />
                </div>
              </div>
            ))
          )}
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default SchedulePage;
