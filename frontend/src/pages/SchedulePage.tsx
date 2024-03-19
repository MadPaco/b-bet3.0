import React, { useState, useEffect } from 'react';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { useColor } from '../context/ColorContext';
import { colorClasses } from '../data/colorClasses';

const SchedulePage: React.FC = () => {
  type Game = {
    id: number;
    weekNumber: number;
    date: string;
    location: string;
    homeTeam: string;
    awayTeam: string;
    homeTeamLogo: string;
    awayTeamLogo: string;
    homeOdds: number;
    awayOdds: number;
    overUnder: number;
  };

  const [schedule, setSchedule] = useState<Game[]>([]);
  const [weekNumber, setWeekNumber] = useState(1);
  const { primaryColor } = useColor();
  const colorClass = primaryColor
    ? colorClasses[primaryColor as keyof typeof colorClasses]
    : 'bg-gray-400 hover:bg-gray-300';

  useEffect(() => {
    const fetchSchedule = async () => {
      const response = await fetch(
        `http://127.0.0.1:8000/backend/schedule?weekNumber=${weekNumber}`,
      );
      const data = await response.json();
      setSchedule(data);
    };

    fetchSchedule();
  }, [weekNumber]);

  const options = [];
  for (let i = 1; i <= 17; i++) {
    options.push(<option value={i}>Week {i}</option>);
  }
  return (
    <LoggedInLayout>
      <div className="flex flex-col items-center justify-center pt-2">
        <div className="text-white lg:w-1/2">
          <div className="flex items-center flex-col mb-4">
            <h1>Schedule Page</h1>
            <select
              className="bg-gray-600 text-white p-2 rounded-lg border-none focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-opacity-50"
              value={weekNumber}
              onChange={(e) => setWeekNumber(Number(e.target.value))}
            >
              {options}
            </select>
          </div>

          {schedule.map((game, index) => (
            <div
              key={index}
              className={`${colorClass} game flex flex-col items-center bg-opacity-70 rounded-lg p-3 m-3`}
            >
              <div className="time text-lg font-bold">{game.date}</div>
              <div className="teams lg:text-lg flex items-center py-3">
                <img
                  className="h-7 w-7 lg:h-10 lg:w-10 mr-3"
                  src={`/assets/images/teams/${game.awayTeamLogo}`}
                  alt={game.awayTeam}
                />
                <span>{game.awayTeam}</span>
                <span className="mx-2"> at </span>
                <span>{game.homeTeam}</span>
                <img
                  className="h-7 w-7 lg:h-10 lg:w-10 ml-3"
                  src={`/assets/images/teams/${game.homeTeamLogo}`}
                  alt={game.homeTeam}
                />
              </div>
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
              <div className="location">{game.location}</div>
            </div>
          ))}
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default SchedulePage;
