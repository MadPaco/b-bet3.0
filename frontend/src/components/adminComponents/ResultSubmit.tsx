import { useEffect, useState } from 'react';
import { fetchSchedule } from '../../utility/api';

interface Game {
  id: number;
  date: string;
  homeTeam: string;
  awayTeam: string;
  homeTeamScore: number;
  awayTeamScore: number;
}

const ResultSubmit: React.FC = () => {
  const [games, setGames] = useState<Game[]>([]);
  const [weekNumber, setWeekNumber] = useState(1);
  const NFLWEEKS = 22;

  useEffect(() => {
    const getGames = async () => {
      const response = await fetchSchedule(weekNumber);
      const games = await response.json();
      setGames(games);
    };

    getGames();
  }, [weekNumber]);

  return (
    <div>
      <select
        onChange={(e) => {
          setWeekNumber(Number((e.target as HTMLSelectElement).value));
        }}
        className="text-black"
      >
        {Array.from({ length: NFLWEEKS }, (_, i) => i + 1).map((number) => (
          <option key={number} value={number}>
            week {number}
          </option>
        ))}
      </select>
      <div>
        <form className="text-white">
          {games.map((game) => (
            <div key={game.id}>
              <p>ID: {game.id}</p>
              <p>{game.date}</p>
              <p>
                {game.awayTeam} at {game.homeTeam}
              </p>
              <input
                className="mx-4"
                type="number"
                placeholder="Away Team Score"
              />
              <input type="number" placeholder="Home Team Score" />
            </div>
          ))}
        </form>
      </div>
    </div>
  );
};

export default ResultSubmit;
